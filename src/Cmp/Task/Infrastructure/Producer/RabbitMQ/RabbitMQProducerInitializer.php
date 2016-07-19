<?php

namespace Cmp\Task\Infrastructure\Producer\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriterInitializer;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class RabbitMQProducerInitializer implements RabbitMQWriterInitializer
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
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(AMQPLazyConnection $connection, $exchange, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->exchange = $exchange;
        $this->logger = $logger;
    }

    /**
     * @return \PhpAmqpLib\Channel\AMQPChannel
     * @throws ConnectionException
     */
    public function initialize()
    {
        $this->logger->info('Connecting to RabbitMQ');

        try {
            $channel = $this->connection->channel(); // this is the one starting the connection
            $channel->exchange_declare($this->exchange, 'fanout', false, true, false);
            return $channel;
        } catch (\ErrorException $e) {
            $this->logger->error('Error trying to connect to rabbitMQ:' . $e->getMessage());
            throw new ConnectionException('Error trying to connect to the queue backend');
        }

    }
}