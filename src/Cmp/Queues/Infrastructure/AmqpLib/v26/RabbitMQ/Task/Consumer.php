<?php

namespace Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Task;

use Cmp\Queues\Domain\Queue\JSONMessageFactory;
use Cmp\Queues\Domain\Task\Consumer as DomainConsumer;
use Cmp\Queues\Domain\Task\JSONTaskFactory;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\BindConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ConsumeConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ExchangeConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\QueueConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\MessageHandler;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\QueueReader;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class Consumer extends DomainConsumer
{
    /**
     * @param string             $host
     * @param int                $port
     * @param string             $user
     * @param string             $password
     * @param string             $vHost
     * @param string             $exchangeName
     * @param string             $queueName
     * @param LoggerInterface    $logger
     * @param JSONMessageFactory $factory
     */
    public function __construct(
        $host,
        $port,
        $user,
        $password,
        $vHost,
        $exchangeName,
        $queueName,
        LoggerInterface $logger,
        JSONMessageFactory $factory = null
    ) {
        $queueReader = new QueueReader(
            new AMQPLazyConnection($host, $port, $user, $password, $vHost),
            new QueueConfig($queueName, false, true, false, false),
            new ExchangeConfig($exchangeName, 'fanout', false, true, false),
            new BindConfig(),
            new ConsumeConfig(false, false, false, false),
            new MessageHandler($factory ?: new JSONTaskFactory()),
            $logger
        );
        parent::__construct($queueReader);
    }
}
