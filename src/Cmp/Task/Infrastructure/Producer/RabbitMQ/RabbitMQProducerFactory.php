<?php

namespace Cmp\Task\Infrastructure\Producer\RabbitMQ;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
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

    public function create(RabbitMQConfig $config)
    {
        $this->logger->info('Using RabbitMQ Writer');
        $amqpLazyConnection = new AMQPLazyConnection($config->getHost(), $config->getPort(), $config->getUser(), $config->getPassword());
        $this->logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Exchange: %s',
            $config->getHost(), $config->getPort(), $config->getUser(), $config->getExchange()));
        $rabbitMQProducerInitializer = new RabbitMQProducerInitializer($amqpLazyConnection, $config->getExchange(), $this->logger);
        return new RabbitMQWriter($rabbitMQProducerInitializer, $config->getExchange(), $this->logger);
    }
}