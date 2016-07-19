<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriterInitializer;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class RabbitMQPublisherInitializer implements RabbitMQWriterInitializer
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
        $this->logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Exchange: %s',
            $this->config['host'], $this->config['port'], $this->config['user'], $this->config['exchange']));

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