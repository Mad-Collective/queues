<?php

namespace Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent;

use Cmp\Queues\Domain\Event\Publisher as DomainPublisher;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ExchangeConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\QueueWriter;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;


class Publisher extends DomainPublisher
{
    /**
     * Publisher constructor.
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $password
     * @param string $vHost
     * @param string $exchangeName
     * @param LoggerInterface $logger
     */
    public function __construct(
        $host,
        $port,
        $user,
        $password,
        $vHost,
        $exchangeName,
        LoggerInterface $logger
    )
    {
        $queueWriter = new QueueWriter(
            new AMQPLazyConnection($host, $port, $user, $password, $vHost),
            new ExchangeConfig($exchangeName, 'topic', false, true, false),
            $logger
        );
        parent::__construct($queueWriter);
    }
}