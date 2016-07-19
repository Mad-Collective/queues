<?php

namespace Cmp\Task\Application\Producer;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Cmp\Task\Infrastructure\Producer\RabbitMQ\RabbitMQProducerFactory;
use Psr\Log\LoggerInterface;

class ProducerFactory
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
        $rabbitMQProducerFactory = new RabbitMQProducerFactory($this->logger);
        return $rabbitMQProducerFactory->create($config);
    }
}