<?php
namespace Infrastructure\AmqpLib\v26\RabbitMQ\Queue;

use Domain\Queue\Exception\WriterException;
use Domain\Queue\QueueWriter as DomainQueueWriter;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ConnectionConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ExchangeConfig;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class QueueWriter implements DomainQueueWriter
{
    const DELAY_QUEUE_PREFIX = 'Delay';

    /**
     * @var AMQPLazyConnection
     */
    protected $connection;

    /**
     * @var ConnectionConfig
     */
    protected $connectionConfig;

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

    protected $delayedExchanges = array();

    /**
     * QueueWriter constructor.
     * @param ConnectionConfig $connectionConfig
     * @param ExchangeConfig $exchangeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConnectionConfig $connectionConfig,
        ExchangeConfig $exchangeConfig,
        LoggerInterface $logger
    )
    {
        $this->connection = new AMQPLazyConnection(
            $connectionConfig->getHost(),
            $connectionConfig->getPort(),
            $connectionConfig->getUser(),
            $connectionConfig->getPassword(),
            $connectionConfig->getVHost()
        );
        $this->connectionConfig = $connectionConfig;
        $this->exchangeConfig = $exchangeConfig;
        $this->logger = $logger;
    }

    /**
     * @param array $messages
     * @throws WriterException
     * @return null
     */
    public function write(array $messages)
    {
        $this->initialize();
        try {
            foreach($messages as $message) {
                $encodedMessage = json_encode($message);
                $this->logger->debug('Writing:' . $encodedMessage);
                $msg = new AMQPMessage($encodedMessage, array('delivery_mode' => 2));
                $this->channel->batch_basic_publish($msg, $this->exchangeConfig->getName(), $message->getName());
            }
            $this->channel->publish_batch();
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

    protected function initializeWithDelay($delay)
    {
        $channel = $this->connection->channel();

        $exchangeDelayed = self::DELAY_QUEUE_PREFIX.$delay.$this->exchangeConfig->getName();
        $queueDelayed = self::DELAY_QUEUE_PREFIX.$delay.'Queue';

        // Delay Queue
        $channel->exchange_declare($exchangeDelayed, 'fanout', false, true, true);
        $channel->queue_declare(
            $queueDelayed,
            false,
            true,
            false,
            true,
            false,
            [
                'x-expires' => ['I', $delay*1000 + 5000],
                'x-message-ttl' => array('I', $delay*1000),
                'x-dead-letter-exchange' => array('S', $this->exchangeConfig->getName())
            ]
        );
        $channel->queue_bind($queueDelayed, $exchangeDelayed);
        $this->delayedExchanges[] = ;
        return $exchangeDelayed;
    }
}