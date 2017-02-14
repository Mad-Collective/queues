<?php

namespace Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent;

use \Domain\Event\Publisher as DomainPublisher;
use Infrastructure\AmqpLib\v26\Queue\Config\ConnectionConfig;
use Infrastructure\AmqpLib\v26\Queue\Config\ExchangeConfig;
use Infrastructure\AmqpLib\v26\Queue\QueueWriter;
use Psr\Log\LoggerInterface;


class Publisher extends DomainPublisher
{
    /**
     * Publisher constructor.
     * @param \Domain\Queue\QueueWriter $host
     * @param $port
     * @param $user
     * @param $password
     * @param $vHost
     * @param $exchangeName
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