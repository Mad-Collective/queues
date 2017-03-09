<?php
namespace Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue;

use Cmp\Queues\Domain\Queue\Exception\ReaderException;
use Cmp\Queues\Domain\Queue\Exception\TimeoutReaderException;
use Cmp\Queues\Domain\Queue\QueueReader as DomainQueueReader;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\BindConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ConsumeConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ExchangeConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\QueueConfig;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use Psr\Log\LoggerInterface;

class QueueReader implements DomainQueueReader
{
    /**
     * @var AMQPLazyConnection
     */
    protected $connection;

    /**
     * @var QueueConfig
     */
    protected $queueConfig;

    /**
     * @var ExchangeConfig
     */
    protected $exchangeConfig;

    /**
     * @var BindConfig
     */
    protected $bindConfig;

    /**
     * @var ConsumeConfig
     */
    protected $consumeConfig;

    /**
     * @var MessageHandler
     */
    protected $messageHandler;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * QueueReader constructor.
     * @param AMQPLazyConnection $connection
     * @param QueueConfig $queueConfig
     * @param ExchangeConfig $exchangeConfig
     * @param BindConfig $bindConfig
     * @param ConsumeConfig $consumeConfig
     * @param MessageHandler $messageHandler
     * @param LoggerInterface $logger
     */
    public function __construct(
        AMQPLazyConnection $connection,
        QueueConfig $queueConfig,
        ExchangeConfig $exchangeConfig,
        BindConfig $bindConfig,
        ConsumeConfig $consumeConfig,
        MessageHandler $messageHandler,
        LoggerInterface $logger
    )
    {
        $this->connection = $connection;
        $this->queueConfig = $queueConfig;
        $this->exchangeConfig = $exchangeConfig;
        $this->bindConfig = $bindConfig;
        $this->consumeConfig = $consumeConfig;
        $this->logger = $logger;
        $this->messageHandler = $messageHandler;
    }

    /**
     * @param callable $callback
     * @param int $timeout
     * @throws ReaderException
     * @throws TimeoutReaderException
     */
    public function read(callable $callback, $timeout=0)
    {
        $this->initialize();
        $this->messageHandler->setCallback($callback);
        try {
            $this->channel->wait(null, false, $timeout);
        } catch(AMQPTimeoutException $e) {
            throw new TimeoutReaderException("Timed out while reading", 0, $e);
        }
    }

    /**
     * @throws ReaderException
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
            $this->logger->info('Declaring queue');
            $this->channel->queue_declare(
                $this->queueConfig->getName(),
                $this->queueConfig->getPassive(),
                $this->queueConfig->getDurable(),
                $this->queueConfig->getExclusive(),
                $this->queueConfig->getAutoDelete()
            );
            $this->channel->queue_bind($this->queueConfig->getName(), $this->exchangeConfig->getName());
            foreach ($this->bindConfig->getTopics() as $bindTopic) {
                $this->logger->info('Binding Topic:' . $bindTopic);
                $this->channel->queue_bind($this->queueConfig->getName(), $this->exchangeConfig->getName(), $bindTopic);
            }
            $this->logger->info('Waiting for messages on queue:' . $this->queueConfig->getName());
            $this->channel->basic_consume(
                $this->queueConfig->getName(),
                '',
                $this->consumeConfig->getNoLocal(),
                $this->consumeConfig->getNoAck(),
                $this->consumeConfig->getExclusive(),
                $this->consumeConfig->getNoWait(),
                array($this->messageHandler, 'handleMessage')
            );
        } catch (\ErrorException $exception) {
            $this->logger->error('Error trying to connect to rabbitMQ:' . $exception->getMessage());
            throw new ReaderException("Error initializing queue reader", 0, $exception);
        }
    }

    public function __destruct()
    {
        $this->channel->close();
    }
}