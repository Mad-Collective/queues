<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class RabbitMQPublisherInitializer
{

    /**
     * @var AMQPLazyConnection
     */
    private $connection;

    /**
     * @var array
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(AMQPLazyConnection $connection, array $config, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function initialize()
    {
        $channel = $this->connection->channel();
        $channel->exchange_declare($this->config['exchange'], 'topic', false, true, false);
        return $channel;
    }

}