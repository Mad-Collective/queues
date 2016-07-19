<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriter;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class RabbitMQPublisherFactory
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
        $this->logger->info(sprintf('RabbitMQ Configuration, Host: %s, Port: %s, User: %s, Exchange: %s',
            $host, $port, $user, $exchange));
        $rabbitMQPublisherInitializer = new RabbitMQPublisherInitializer($amqpLazyConnection, $exchange, $this->logger);
        return new RabbitMQWriter($rabbitMQPublisherInitializer, $exchange, $this->logger);
    }

}