<?php

namespace Cmp\Task\Infrastructure\Consumer\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQReaderInitializer;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class RabbitMQConsumerInitializer implements RabbitMQReaderInitializer
{
    /**
     * @var AMQPLazyConnection
     */
    private $connection;

    /**
     * @var string
     */
    private $exchange;

    /**
     * @var string
     */
    private $queue;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(AMQPLazyConnection $connection, $exchange, $queue, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->exchange = $exchange;
        $this->queue = $queue;
        $this->logger = $logger;
    }

    public function initialize(callable $msgCallback)
    {
        $this->logger->info('Connecting to RabbitMQ');

        try {
            $channel = $this->connection->channel(); // this is the one starting the connection

            $channel->exchange_declare($this->exchange, 'fanout', false, true, false);

            list($queueName, ,) = $channel->queue_declare($this->queue, false, true, false, false);

            $channel->queue_bind($queueName, $this->exchange);

            $this->logger->info('Starting to consume RabbitMQ Queue:' . $queueName);
            $channel->basic_consume($queueName, '', false, false, false, false, $msgCallback);

            return $channel;
        } catch (\ErrorException $e) {
            $this->logger->error('Error trying to connect to rabbitMQ:' . $e->getMessage());
            throw new ConnectionException('Error trying to connect to the queue backend');
        }

    }
}