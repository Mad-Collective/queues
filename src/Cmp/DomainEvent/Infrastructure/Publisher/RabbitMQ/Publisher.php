<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;


use Cmp\DomainEvent\Domain\Event\AbstractEvent;

class Publisher implements \Cmp\DomainEvent\Infrastructure\Publisher\Publisher
{
    public function publish(AbstractEvent $event)
    {
        // TODO: Implement publish() method.
    }
}