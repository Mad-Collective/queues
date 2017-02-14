<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 17:33
 */

namespace Infrastructure\AmqpLib\v26\RabbitMQ\Queue;

use Domain\Queue\Exception\ReaderException;
use Domain\Queue\QueueReader as DomainQueueReader;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\BindConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ConnectionConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ConsumeConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ExchangeConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\QueueConfig;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class QueueReader implements DomainQueueReader
{
    /**
     * @var AMQPLazyConnection
     */
    protected $connection;

    /**
     * @var ConnectionConfig
     */
    protected $connectionConfig;

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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @var MessageHandler
     */
    protected $messageHandler;

    /**
     * QueueReader constructor.
     * @param ConnectionConfig $connectionConfig
     * @param QueueConfig $queueConfig
     * @param ExchangeConfig $exchangeConfig
     * @param BindConfig $bindConfig
     * @param ConsumeConfig $consumeConfig
     * @param LoggerInterface $logger
     * @param MessageHandler $messageHandler
     */
    public function __construct(
        ConnectionConfig $connectionConfig,
        QueueConfig $queueConfig,
        ExchangeConfig $exchangeConfig,
        BindConfig $bindConfig,
        ConsumeConfig $consumeConfig,
        LoggerInterface $logger,
        MessageHandler $messageHandler
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
        $this->queueConfig = $queueConfig;
        $this->exchangeConfig = $exchangeConfig;
        $this->bindConfig = $bindConfig;
        $this->consumeConfig = $consumeConfig;
        $this->logger = $logger;
        $this->messageHandler = $messageHandler;
    }

    public function read()
    {
        $this->initialize();
        $this->channel->wait();
    }

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
            $this->channel->queue_declare(
                $this->queueConfig->getName(),
                $this->queueConfig->getPassive(),
                $this->queueConfig->getDurable(),
                $this->queueConfig->getExclusive(),
                $this->queueConfig->getAutoDelete()
            );
            foreach ($this->bindConfig->getTopics() as $bindTopic) {
                $this->logger->info('Binding Topic:' . $bindTopic);
                $this->channel->queue_bind($this->queueConfig->getName(), $this->exchangeConfig->getName(), $bindTopic);
            }
            $this->logger->info('Starting to consume RabbitMQ Queue:' . $this->queueConfig->getName());
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
            throw new ReaderException($exception->getMessage(), $exception->getCode());
        }
    }
}