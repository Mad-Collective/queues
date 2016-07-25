<?php

namespace Cmp\Queue\Infrastructure\RabbitMQ;

use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class RabbitMQWriterFactory
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
        $this->logger->info(sprintf('RabbitMQ Configuration, Host: %s, Port: %s, User: %s, Exchange: %s',
            $config->getHost(), $config->getPort(), $config->getUser(), $config->getExchange()));
        $rabbitMQWriterInitializer = new RabbitMQWriterInitializer($amqpLazyConnection, $config->getExchange(), 'topic', $this->logger);
        return new RabbitMQWriter($rabbitMQWriterInitializer, $config->getExchange(), $this->logger);

    }

}