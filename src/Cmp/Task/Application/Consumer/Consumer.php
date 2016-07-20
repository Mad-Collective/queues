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

    /**
     * Consume just once. This method will not block the execution.
     *
     * If you want it to keep consuming call it in a loop or use the consume() method.
     */
    public function consumeOnce(callable $callback)
    {
        $this->consumer->consumeOnce($callback);
    }

}