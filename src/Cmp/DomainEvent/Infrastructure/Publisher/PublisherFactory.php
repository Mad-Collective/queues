<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher;

use Cmp\DomainEvent\Infrastructure\Exception\BackendNotImplementedException;
use Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\Publisher;

class PublisherFactory
{

    public function create($config) {
        if ($config['backend'] == 'rabbitmq') {
            return new Publisher();
        } else {
            throw new BackendNotImplementedException();
        }
    }

}