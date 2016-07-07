<?php

namespace Cmp\DomainEvent\Domain\Subscriber;

use Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriberFactory;

class SubscriberFactory
{
    public function create($config, $domainTopics) {
        $rabbitMQSubscriberFactory = new RabbitMQSubscriberFactory();
        return $rabbitMQSubscriberFactory->create($config, $domainTopics);
    }
}