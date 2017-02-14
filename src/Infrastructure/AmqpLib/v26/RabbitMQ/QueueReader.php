<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 17:33
 */

namespace Infrastructure\AmqpLib\v26;

use Domain\Queue\JSONMessageFactory;
use Domain\Queue\Message;
use Domain\Queue\QueueReader as DomainQueueReader;
use PhpAmqpLib\Connection\AMQPLazyConnection;

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
     * QueueReader constructor.
     * @param ConnectionConfig $connectionConfig
     * @param QueueConfig $queueConfig
     * @param ExchangeConfig $exchangeConfig
     * @param BindConfig $bindConfig
     * @param ConsumeConfig $consumeConfig
     */
    public function __construct(
        ConnectionConfig $connectionConfig,
        QueueConfig $queueConfig,
        ExchangeConfig $exchangeConfig,
        BindConfig $bindConfig,
        ConsumeConfig $consumeConfig
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
    }
}