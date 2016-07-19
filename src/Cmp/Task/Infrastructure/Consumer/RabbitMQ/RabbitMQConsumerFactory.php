<?php

namespace Cmp\Task\Infrastructure\Consumer\RabbitMQ;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQMessageHandler;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQReader;
use Cmp\Task\Domain\Consumer\Consumer;
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

    public function create($host, $port, $user, $password, $exchange, $queue)
    {
        $this->logger->info('Using RabbitMQ Consumer');

        $amqpLazyConnection = new AMQPLazyConnection($host, $port, $user, $password);
        $this->logger->info(sprintf('RabbitMQ Configuration, Host: %s, Port: %s, User: %s, Exchange: %s, Queue: %s',
            $host, $port, $user, $exchange, $queue));
        $rabbitMQConsumerInitializer = new RabbitMQConsumerInitializer($amqpLazyConnection, $exchange, $queue, $this->logger);

        $jsonTaskFactory = new JSONTaskFactory();
        $rabbitMQMessageHandler = new RabbitMQMessageHandler($jsonTaskFactory);

        $rabbitMQReader = new RabbitMQReader($rabbitMQConsumerInitializer, $rabbitMQMessageHandler, $this->logger);

        return new Consumer($rabbitMQReader, $this->logger);
    }
}