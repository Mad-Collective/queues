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

    public function create($config)
    {
        $this->logger->info('Using RabbitMQ Writer');
        $amqpLazyConnection = new AMQPLazyConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $rabbitMQProducerInitializer = new RabbitMQProducerInitializer($amqpLazyConnection, $config, $this->logger);
        return new RabbitMQWriter($rabbitMQProducerInitializer, $config['exchange'], $this->logger);
    }
}