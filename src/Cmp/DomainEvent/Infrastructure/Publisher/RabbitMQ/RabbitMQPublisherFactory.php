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

    public function create($config)
    {
        $this->logger->info('Using RabbitMQ Writer');
        $amqpLazyConnection = new AMQPLazyConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $rabbitMQPublisherInitializer = new RabbitMQPublisherInitializer($amqpLazyConnection, $config, $this->logger);
        return new RabbitMQWriter($rabbitMQPublisherInitializer, $config['exchange'], $this->logger);
    }

}