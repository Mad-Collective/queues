<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

use Cmp\DomainEvent\Domain\Publisher\ConnectionException;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpSpec\Exception\Example\ErrorException;
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

    /**
     * @return \PhpAmqpLib\Channel\AMQPChannel
     * @throws ConnectionException
     */
    public function initialize()
    {
        try {
            $channel = $this->connection->channel(); // this is the one starting the connection
            $channel->exchange_declare($this->config['exchange'], 'topic', false, true, false);
            return $channel;
        } catch (\ErrorException $e) {
            $this->logger->error('Error trying to connect to rabbitMQ:' . $e->getMessage());
            throw new ConnectionException('Error trying to connect to the queue backend');
        }

    }

}