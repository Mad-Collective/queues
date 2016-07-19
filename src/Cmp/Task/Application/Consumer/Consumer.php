<?php

namespace Cmp\Task\Application\Consumer;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Cmp\Task\Infrastructure\Consumer\RabbitMQ\RabbitMQConsumerFactory;
use Psr\Log\LoggerInterface;

class Consumer
{

    private $consumer;

    public function __construct(RabbitMQConfig $config, LoggerInterface $logger)
    {

        $rabbitMQConsumerFactory = new RabbitMQConsumerFactory($logger);
        $this->consumer = $rabbitMQConsumerFactory->create($config);
    }

    public function consume(callable $callback)
    {
        $this->consumer->consume($callback);
    }

}