<?php

namespace Cmp\DomainEvent\Domain\Publisher;

use Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\RabbitMQPublisherFactory;
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

    public function create($config)
    {
        $rabbitMQPublisherFactory = new RabbitMQPublisherFactory($this->logger);
        return $rabbitMQPublisherFactory->create($config);
    }

}