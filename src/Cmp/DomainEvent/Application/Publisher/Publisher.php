<?php

namespace Cmp\DomainEvent\Application\Publisher;

use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\RabbitMQPublisherFactory;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Psr\Log\LoggerInterface;

class Publisher
{
    /**
     * @var \Cmp\DomainEvent\Domain\Publisher\Publisher
     */
    private $publisher;

    public function __construct(RabbitMQConfig $config, LoggerInterface $logger)
    {
        $publisherFactory = new PublisherFactory($logger);
        $this->publisher = $publisherFactory->create($config);
    }

    public function publish()
    {
        $this->publisher->publish();
    }

    public function add(DomainEvent $domainEvent)
    {
        $this->publisher->add($domainEvent);
    }

}