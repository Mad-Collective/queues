<?php
namespace Factory\AmqpLib\v26\RabbitMQ;

use Domain\Event\Publisher;
use Domain\Event\Subscriber;
use Domain\Task\Consumer;
use Domain\Task\Producer;
use Infrastructure\AmqpLib\v26\BindConfig;
use Infrastructure\AmqpLib\v26\ConnectionConfig;
use Infrastructure\AmqpLib\v26\ConsumeConfig;
use Infrastructure\AmqpLib\v26\ExchangeConfig;
use Infrastructure\AmqpLib\v26\QueueConfig;
use Infrastructure\AmqpLib\v26\QueueReader;
use Infrastructure\AmqpLib\v26\QueueWriter;

class AmqpLibv26RabbitMq
{
    public function createTaskProducer($host, $port, $user, $password, $vHost, $exchangeName)
    {
        $queue = new QueueWriter(
            new ConnectionConfig($host, $port, $user, $password, $vHost),
            new ExchangeConfig($exchangeName, 'fanout', false, true, false)
        );
        return new Producer($queue);
    }

    public function createTaskConsumer($host, $port, $user, $password, $vHost, $exchangeName, $queueName, BindConfig $bindConfig)
    {
        $queue = new QueueReader(
            new ConnectionConfig($host, $port, $user, $password, $vHost),
            new QueueConfig($queueName, false, true, false, false),
            new ExchangeConfig($exchangeName, 'fanout', false, true, false),
            $bindConfig,
            new ConsumeConfig(false, false, false, false)
        );
        return new Consumer($queue);
    }

    public function createDomainEventPublisher($host, $port, $user, $password, $vHost, $exchangeName)
    {
        $queue = new QueueWriter(
            new ConnectionConfig($host, $port, $user, $password, $vHost),
            new ExchangeConfig($exchangeName, 'topic', false, true, false)
        );
        return new Publisher($queue);
    }

    public function createDomainEventSubscriber($host, $port, $user, $password, $vHost, $exchangeName, $queueName, BindConfig $bindConfig)
    {
        $queue = new QueueReader(
            new ConnectionConfig($host, $port, $user, $password, $vHost),
            new QueueConfig($queueName, false, false, true, true),
            new ExchangeConfig($exchangeName, 'topic', false, true, false),
            $bindConfig,
            new ConsumeConfig(false, false, true, false)
        );
        return new Subscriber($queue);
    }
}