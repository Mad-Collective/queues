<?php

namespace Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQSubscriberFactory
{

    public function create($config) {
        $amqpStreamConnection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $channel = $amqpStreamConnection->channel();

        $channel->exchange_declare($config['exchange'], 'fanout', false, false, false);

        list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

        $channel->queue_bind($queue_name, $config['exchange']);

        $jsonDomainEventFactory = new JSONDomainEventFactory();

        return new RabbitMQSubscriber($channel, $jsonDomainEventFactory, $queue_name);
    }

}