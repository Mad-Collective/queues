<?php

namespace Cmp\DomainEvent\Domain\Subscriber;

use Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriberFactory;

class SubscriberFactory
{
    public function create($config) {
        $rabbitMQSubscriberFactory = new RabbitMQSubscriberFactory();
        return $rabbitMQSubscriberFactory->create($config);
    }
}