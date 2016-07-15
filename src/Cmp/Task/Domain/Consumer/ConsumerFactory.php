<?php

namespace Cmp\Task\Domain\Consumer;

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

    public function create($config)
    {
        $rabbitMQConsumerFactory = new RabbitMQConsumerFactory($this->logger);
        return $rabbitMQConsumerFactory->create($config);
    }

}