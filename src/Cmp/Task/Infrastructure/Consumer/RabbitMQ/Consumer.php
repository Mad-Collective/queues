<?php

namespace Cmp\Task\Infrastructure\Consumer\RabbitMQ;

use Cmp\Queue\Infrastructure\RabbitMQ\AMQPLazyConnectionSingleton;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQMessageHandler;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQReader;
use Cmp\Task\Domain\Task\JSONTaskFactory;
use Cmp\Task\Domain\Consumer\Consumer as DomainConsumer;
use Psr\Log\LoggerInterface;

class Consumer
{

    private $consumer;

    public function __construct(RabbitMQConfig $config, LoggerInterface $logger)
    {
        $logger->info('Using RabbitMQ Consumer');

        $amqpLazyConnection = AMQPLazyConnectionSingleton::getInstance($config->getHost(), $config->getPort(), $config->getUser(), $config->getPassword(), $config->getVhost());
        $logger->info(sprintf('RabbitMQ Configuration, Host: %s, Port: %s, User: %s, Exchange: %s, Queue: %s',
            $config->getHost(), $config->getPort(), $config->getUser(), $config->getExchange(), $config->getQueue()));
        $rabbitMQConsumerInitializer = new RabbitMQConsumerInitializer($amqpLazyConnection, $config->getExchange(), $config->getQueue(), $logger);

        $jsonTaskFactory = new JSONTaskFactory();
        $rabbitMQMessageHandler = new RabbitMQMessageHandler($jsonTaskFactory);

        $rabbitMQReader = new RabbitMQReader($rabbitMQConsumerInitializer, $rabbitMQMessageHandler, $logger);

        $this->consumer = new DomainConsumer($rabbitMQReader, $logger);
    }

    public function consume(callable $consumeCallback)
    {
        $this->consumer->consume($consumeCallback);
    }

    public function consumeOnce(callable $consumeCallback)
    {
        $this->consumer->consumeOnce($consumeCallback);
    }
}