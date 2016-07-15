<?php

namespace Cmp\Task\Infrastructure\Consumer\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class RabbitMQConsumerInitializer
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

    public function initialize(callable $msgCallback)
    {
        $this->logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Queue: %s',
            $this->config['host'], $this->config['port'], $this->config['user'], $this->config['queue']));

        try {
            $channel = $this->connection->channel(); // this is the one starting the connection

            list($queueName, ,) = $channel->queue_declare($this->config['queue'], false, true, false, false);

            $this->logger->info('Starting to consume RabbitMQ Queue:' . $queueName);
            $channel->basic_qos(null, 1, null);
            $channel->basic_consume($queueName, '', false, false, false, false, $msgCallback);

            return $channel;
        } catch (\ErrorException $e) {
            $this->logger->error('Error trying to connect to rabbitMQ:' . $e->getMessage());
            throw new ConnectionException('Error trying to connect to the queue backend');
        }

    }
}