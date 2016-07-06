<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQPublisherFactory
{

    public function create($config)
    {
        $amqpStreamConnection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $channel = $amqpStreamConnection->channel();
        $channel->exchange_declare($config['exchange'], 'fanout', false, false, false);
        return new RabbitMQPublisher($channel, $config);
    }

}