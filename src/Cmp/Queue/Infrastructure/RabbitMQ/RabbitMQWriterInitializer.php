<?php

namespace Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class RabbitMQWriterInitializer
{

    /**
     * @var AMQPLazyConnection
     */
    private $connection;

    /**
     * @var string
     */
    private $exchangeName;

    /**
     * @var string
     */
    private $exchangeType;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(AMQPLazyConnection $connection, $exchangeName, $exchangeType, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->exchangeName = $exchangeName;
        $this->exchangeType = $exchangeType;
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
            $channel->exchange_declare($this->exchangeName, $this->exchangeType, false, true, false);
            return $channel;
        } catch (\ErrorException $e) {
            $this->logger->error('Error trying to connect to rabbitMQ:' . $e->getMessage());
            throw new ConnectionException('Error trying to connect to the queue backend');
        }

    }

}