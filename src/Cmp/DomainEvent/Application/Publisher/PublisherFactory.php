<?php

namespace Cmp\DomainEvent\Application\Publisher;

use Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\RabbitMQPublisherFactory;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Psr\Log\LoggerInterface;

class PublisherFactory
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
        $rabbitMQPublisherFactory = new RabbitMQPublisherFactory($this->logger);
        return $rabbitMQPublisherFactory->create($config);
    }

}