<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

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

    public function create($config)
    {
        $this->logger->info('Using RabbitMQ Publisher');
        $amqpLazyConnection = new AMQPLazyConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $rabbitMQPublisherInitializer = new RabbitMQPublisherInitializer($amqpLazyConnection, $config, $this->logger);
        return new RabbitMQPublisher($rabbitMQPublisherInitializer, $config, $this->logger);
    }

}