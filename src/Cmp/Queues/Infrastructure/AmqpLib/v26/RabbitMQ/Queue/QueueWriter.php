<?php
namespace Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue;

use Cmp\Queues\Domain\Queue\Exception\WriterException;
use Cmp\Queues\Domain\Queue\Message;
use Cmp\Queues\Domain\Queue\QueueWriter as DomainQueueWriter;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ExchangeConfig;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class QueueWriter implements DomainQueueWriter
{
    /**
     * @var AMQPLazyConnection
     */
    protected $connection;

    /**
     * @var ExchangeConfig
     */
    protected $exchangeConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @var DelayedQueueWriter[]
     */
    protected $delayedQueueWriterRegistry = [];

    /**
     * QueueWriter constructor.
     * @param AMQPLazyConnection $connection
     * @param ExchangeConfig $exchangeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        AMQPLazyConnection $connection,
        ExchangeConfig $exchangeConfig,
        LoggerInterface $logger
    )
    {
        $this->connection = $connection;
        $this->exchangeConfig = $exchangeConfig;
        $this->logger = $logger;
    }

    /**
     * @param Message[] $messages
     * @throws WriterException
     * @return null
     */
    public function write(array $messages)
    {
        $this->initialize();
        try {
            $messagesWithDelay = [];
            foreach($messages as $message) {
                if($message->getDelay() > 0) {
                    $messagesWithDelay[$message->getDelay()][] = $message;
                    continue;
                }
                $encodedMessage = json_encode($message);
                $this->logger->debug('Writing:' . $encodedMessage);
                $msg = new AMQPMessage($encodedMessage, array('delivery_mode' => 2));
                $this->channel->batch_basic_publish($msg, $this->exchangeConfig->getName(), $message->getName());
            }
            $this->channel->publish_batch();
            foreach($messagesWithDelay as $delay => $delayedMessages) {
                if(!isset($this->delayedQueueWriterRegistry[$delay])) {
                    $this->delayedQueueWriterRegistry[$delay] = new DelayedQueueWriter(
                        $this->exchangeConfig->getName(),
                        $delay,
                        $this->channel,
                        $this->logger
                    );
                }
                $this->delayedQueueWriterRegistry[$delay]->write($delayedMessages);
            }
        } catch(\Exception $exception) {
            $this->logger->error('Error writing messages: '.$exception->getMessage());
            throw new WriterException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @throws WriterException
     */
    protected function initialize()
    {
        if($this->channel) {
            return;
        }
        $this->logger->info('Connecting to RabbitMQ');
        try {
            $this->channel = $this->connection->channel();
            $this->channel->exchange_declare(
                $this->exchangeConfig->getName(),
                $this->exchangeConfig->getType(),
                $this->exchangeConfig->getPassive(),
                $this->exchangeConfig->getDurable(),
                $this->exchangeConfig->getAutoDelete()
            );
        } catch (\ErrorException $exception) {
            $this->logger->error('Error trying to connect to rabbitMQ:' . $exception->getMessage());
            throw new WriterException($exception->getMessage(), $exception->getCode());
        }
    }
}