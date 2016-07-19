<?php

namespace Cmp\DomainEvent\Application\Publisher;

use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\RabbitMQPublisherFactory;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Psr\Log\LoggerInterface;

class Publisher
{

    private $publisher;

    public function __construct(RabbitMQConfig $config, LoggerInterface $logger)
    {
        $rabbitMQPublisherFactory = new RabbitMQPublisherFactory($logger);
        $this->publisher = $rabbitMQPublisherFactory->create($config);
    }

    public function publish()
    {
        $this->publisher->write();
    }

    public function add(DomainEvent $domainEvent)
    {
        $this->publisher->add($domainEvent);
    }

}