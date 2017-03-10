<?php

namespace Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent;

use Cmp\Queues\Domain\Event\JSONDomainEventFactory;
use Cmp\Queues\Domain\Event\Subscriber as DomainSubscriber;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\BindConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ConsumeConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ExchangeConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\QueueConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\MessageHandler;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\QueueReader;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class Subscriber extends DomainSubscriber
{
    /**
     * @param string                 $host
     * @param int                    $port
     * @param string                 $user
     * @param string                 $password
     * @param string                 $vHost
     * @param string                 $exchangeName
     * @param string                 $queueName
     * @param BindConfig             $bindConfig
     * @param LoggerInterface        $logger
     * @param JSONDomainEventFactory $factory
     */
    public function __construct(
        $host,
        $port,
        $user,
        $password,
        $vHost,
        $exchangeName,
        $queueName,
        BindConfig $bindConfig,
        LoggerInterface $logger,
        JSONDomainEventFactory $factory = null
    ) {
        $queueReader = new QueueReader(
            new AMQPLazyConnection($host, $port, $user, $password, $vHost),
            new QueueConfig($queueName, false, true, false, false),
            new ExchangeConfig($exchangeName, 'topic', false, true, false),
            $bindConfig,
            new ConsumeConfig(false, false, false, false),
            new MessageHandler($factory ?: new JSONDomainEventFactory()),
            $logger
        );
        parent::__construct($queueReader, $logger);
    }
}
