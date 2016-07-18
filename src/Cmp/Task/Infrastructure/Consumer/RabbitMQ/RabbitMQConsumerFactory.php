<?php

namespace Cmp\Task\Infrastructure\Consumer\RabbitMQ;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQMessageHandler;
use Cmp\Task\Domain\Task\JSONTaskFactory;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class RabbitMQConsumerFactory
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
        $this->logger->info('Using RabbitMQ Consumer');

        $amqpLazyConnection = new AMQPLazyConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $rabbitMQConsumerInitializer = new RabbitMQConsumerInitializer($amqpLazyConnection, $config, $this->logger);

        $jsonTaskFactory = new JSONTaskFactory();
        $rabbitMQMessageHandler = new RabbitMQMessageHandler($jsonTaskFactory);

        return new RabbitMQConsumer($rabbitMQConsumerInitializer, $rabbitMQMessageHandler, $this->logger);
    }
}