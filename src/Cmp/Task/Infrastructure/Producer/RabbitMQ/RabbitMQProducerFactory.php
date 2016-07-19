<?php

namespace Cmp\Task\Infrastructure\Producer\RabbitMQ;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriter;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class RabbitMQProducerFactory
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function create($host, $port, $user, $password, $exchange)
    {
        $this->logger->info('Using RabbitMQ Writer');
        $amqpLazyConnection = new AMQPLazyConnection($host, $port, $user, $password);
        $this->logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Exchange: %s',
            $host, $port, $user, $password, $exchange));
        $rabbitMQProducerInitializer = new RabbitMQProducerInitializer($amqpLazyConnection, $exchange, $this->logger);
        return new RabbitMQWriter($rabbitMQProducerInitializer, $exchange, $this->logger);
    }
}