<?php

namespace Cmp\DomainEvent\Application\Publisher;

use Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\RabbitMQPublisherFactory;
use Cmp\Queue\Domain\Writer\AbstractWriter;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Psr\Log\LoggerInterface;

class PublisherFactory
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param RabbitMQConfig $config
     *
     * @return Cmp\DomainEvent\Domain\Publisher\Publisher
     */
    public function create(RabbitMQConfig $config)
    {
        $rabbitMQPublisherFactory = new RabbitMQPublisherFactory($this->logger);
        return $rabbitMQPublisherFactory->create($config);
    }

}