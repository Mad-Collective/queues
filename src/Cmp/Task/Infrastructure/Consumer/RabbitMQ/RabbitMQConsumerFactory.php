<?php

namespace Cmp\Task\Infrastructure\Consumer\RabbitMQ;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
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

    public function create(RabbitMQConfig $config)
    {
        $this->logger->info('Using RabbitMQ Consumer');

        $amqpLazyConnection = new AMQPLazyConnection($config->getHost(), $config->getPort(), $config->getUser(), $config->getPassword());
        $this->logger->info(sprintf('RabbitMQ Configuration, Host: %s, Port: %s, User: %s, Exchange: %s, Queue: %s',
            $config->getHost(), $config->getPort(), $config->getUser(), $config->getExchange(), $config->getQueue()));
        $rabbitMQConsumerInitializer = new RabbitMQConsumerInitializer($amqpLazyConnection, $config->getExchange(), $config->getQueue(), $this->logger);

        $jsonTaskFactory = new JSONTaskFactory();
        $rabbitMQMessageHandler = new RabbitMQMessageHandler($jsonTaskFactory);

        $rabbitMQReader = new RabbitMQReader($rabbitMQConsumerInitializer, $rabbitMQMessageHandler, $this->logger);

        return new Consumer($rabbitMQReader, $this->logger);
    }
}