<?php

namespace Cmp\Task\Infrastructure\Producer;

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

    public function create($config)
    {
        $this->logger->info('Using RabbitMQ Producer');
        $amqpLazyConnection = new AMQPLazyConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $rabbitMQProducerInitializer = new RabbitMQProducerInitializer($amqpLazyConnection, $config, $this->logger);
        return new RabbitMQProducer($rabbitMQProducerInitializer, $config, $this->logger);
    }
}