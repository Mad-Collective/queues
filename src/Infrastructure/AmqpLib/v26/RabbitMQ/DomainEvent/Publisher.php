<?php

namespace Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent;

use \Domain\Event\Publisher as DomainPublisher;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ConnectionConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ExchangeConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\QueueWriter;
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
            new ConnectionConfig($host, $port, $user, $password, $vHost),
            new ExchangeConfig($exchangeName, 'topic', false, true, false),
            $logger
        );
        parent::__construct($queueWriter);
    }
}