<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher;

use Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\Publisher;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class PublisherFactory
{

    public function create($config) {
        $amqpStreamConnection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $channel = $amqpStreamConnection->channel();
        $channel->exchange_declare($config['exchange'], 'fanout', false, false, false);
        return new Publisher($channel, $config);
    }

}