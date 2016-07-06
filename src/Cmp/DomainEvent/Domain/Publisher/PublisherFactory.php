<?php

namespace Cmp\DomainEvent\Domain\Publisher;

use Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\RabbitMQPublisherFactory;

class PublisherFactory
{

    public function create($config) {
        $rabbitMQPublisherFactory = new RabbitMQPublisherFactory();
        return $rabbitMQPublisherFactory->create($config);
    }

}