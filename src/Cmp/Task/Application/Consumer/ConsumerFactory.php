<?php

namespace Cmp\Task\Application\Consumer;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Cmp\Task\Infrastructure\Consumer\RabbitMQ\RabbitMQConsumerFactory;
use Psr\Log\LoggerInterface;

class ConsumerFactory
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
        $rabbitMQConsumerFactory = new RabbitMQConsumerFactory($this->logger);
        return $rabbitMQConsumerFactory->create($config);
    }

}