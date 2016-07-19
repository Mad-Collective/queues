<?php

namespace Cmp\Task\Application\Producer;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Cmp\Task\Domain\Task\Task;
use Cmp\Task\Infrastructure\Producer\RabbitMQ\RabbitMQProducerFactory;
use Psr\Log\LoggerInterface;

class Producer
{

    private $producer;

    public function __construct(RabbitMQConfig $config, LoggerInterface $logger)
    {
        $rabbitMQProducerFactory = new RabbitMQProducerFactory($logger);
        $this->producer = $rabbitMQProducerFactory->create($config);
    }

    public function add(Task $task)
    {
        $this->producer->add($task);
    }

    public function produce()
    {
        $this->producer->write();
    }
}