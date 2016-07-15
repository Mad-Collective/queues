<?php

namespace Cmp\Task\Domain\Producer;

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

    public function create($config)
    {
        $rabbitMQProducerFactory = new RabbitMQProducerFactory($this->logger);
        return $rabbitMQProducerFactory->create($config);
    }
}