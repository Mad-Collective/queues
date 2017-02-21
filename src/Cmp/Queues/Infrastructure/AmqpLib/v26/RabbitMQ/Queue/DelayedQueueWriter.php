<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 15/02/17
 * Time: 12:00
 */

namespace Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue;


use Cmp\Queues\Domain\Queue\Exception\WriterException;
use Cmp\Queues\Domain\Queue\Message;
use Cmp\Queues\Domain\Queue\QueueWriter as DomainQueueWriter;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ConnectionConfig;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class DelayedQueueWriter implements DomainQueueWriter
{
    const DELAY_QUEUE_PREFIX = 'Delay';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AMQPLazyConnection
     */
    protected $connection;

    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @var int
     */
    protected $delay;

    /**
     * @var string
     */
    protected $exchangeName;

    /**
     * @var string
     */
    protected $delayedExchangeName;

    /**
     * DelayedQueueWriter constructor.
     * @param $exchangeName
     * @param $delay
     * @param AMQPChannel $channel
     * @param LoggerInterface $logger
     */
    public function __construct(
        $exchangeName,
        $delay,
        AMQPChannel $channel,
        LoggerInterface $logger
    )
    {
        $this->delay = $delay;
        $this->exchangeName = $exchangeName;
        $this->delayedExchangeName = self::DELAY_QUEUE_PREFIX.$this->delay.$this->exchangeName;
        $this->logger = $logger;
        $this->channel = $channel;
    }

    /**
     * @param Message[] $messages
     * @return void
     * @throws WriterException
     */
    public function write(array $messages)
    {
        $this->initialize();
        try {
            foreach($messages as $message) {
                $encodedMessage = json_encode($message);
                $this->logger->debug('Writing:' . $encodedMessage);
                $msg = new AMQPMessage($encodedMessage, array('delivery_mode' => 2));
                $this->channel->batch_basic_publish($msg, $this->delayedExchangeName, $message->getName());
            }
            $this->channel->publish_batch();
        } catch(\Exception $exception) {
            $this->logger->error('Error writing delayed messages: '.$exception->getMessage());
            throw new WriterException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @throws WriterException
     */
    protected function initialize()
    {
        try{
            $delayedQueue = self::DELAY_QUEUE_PREFIX.$this->delay.'Queue';

            $this->logger->info('Creating delayed exchange '.$this->delayedExchangeName);
            // Delay Queue
            $this->channel->exchange_declare($this->delayedExchangeName, 'fanout', false, true, true);
            $this->logger->info('Creating delayed queue '.$delayedQueue);
            $this->channel->queue_declare(
                $delayedQueue,
                false,
                true,
                false,
                true,
                false,
                [
                    'x-expires' => ['I', $this->delay*1000 + 5000],
                    'x-message-ttl' => array('I', $this->delay*1000),
                    'x-dead-letter-exchange' => array('S', $this->exchangeName)
                ]
            );
            $this->channel->queue_bind($delayedQueue, $this->delayedExchangeName);
        } catch(\Exception $exception) {
            $this->logger->error('Error configuring delayed queues: '.$exception->getMessage());
            throw new WriterException($exception->getMessage(), $exception->getCode());
        }
    }
}