<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 17:33
 */

namespace Infrastructure\AmqpLib\v26\Queue;

use Domain\Queue\QueueReader as DomainQueueReader;
use Infrastructure\AmqpLib\v26\Queue\Config\BindConfig;
use Infrastructure\AmqpLib\v26\Queue\Config\ConnectionConfig;
use Infrastructure\AmqpLib\v26\Queue\Config\ConsumeConfig;
use Infrastructure\AmqpLib\v26\Queue\Config\ExchangeConfig;
use Infrastructure\AmqpLib\v26\Queue\Config\QueueConfig;
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
     * QueueReader constructor.
     * @param ConnectionConfig $connectionConfig
     * @param QueueConfig $queueConfig
     * @param ExchangeConfig $exchangeConfig
     * @param BindConfig $bindConfig
     * @param ConsumeConfig $consumeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConnectionConfig $connectionConfig,
        QueueConfig $queueConfig,
        ExchangeConfig $exchangeConfig,
        BindConfig $bindConfig,
        ConsumeConfig $consumeConfig,
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
        $this->queueConfig = $queueConfig;
        $this->exchangeConfig = $exchangeConfig;
        $this->bindConfig = $bindConfig;
        $this->consumeConfig = $consumeConfig;
    }

    public function read($callback)
    {
        $this->logger->info('Connecting to RabbitMQ');
        try {
            $channel = $this->connection->channel();
            $channel->exchange_declare(
                $this->exchangeConfig->getName(),
                $this->exchangeConfig->getType(),
                $this->exchangeConfig->getPassive(),
                $this->exchangeConfig->getDurable(),
                $this->exchangeConfig->getAutoDelete()
            );
            list($queueName, ,) = $channel->queue_declare(
                $this->queueConfig->getName(),
                $this->queueConfig->getPassive(),
                $this->queueConfig->getDurable(),
                $this->queueConfig->getExclusive(),
                $this->queueConfig->getAutoDelete()
            );
            foreach($this->bindConfig->getTopics() as $bindTopic) {
                $channel->queue_bind($queueName, $this->exchangeConfig->getName(), $bindTopic);
            }
            $channel->queue_bind($queueName, $this->exchangeConfig->getName());
            $channel->basic_consume(
                $queueName,
                '',
                $this->consumeConfig->getNoLocal(),
                $this->consumeConfig->getNoAck(),
                $this->consumeConfig->getExclusive(),
                $this->consumeConfig->getNoWait(),
                $callback
            );
            $channel->wait();
        } catch (\ErrorException $exception) {
            $this->logger->error('Error trying to connect to rabbitMQ:' . $exception->getMessage());
            throw $exception;
        }
    }
}