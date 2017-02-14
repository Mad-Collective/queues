<?php

namespace Factory\AmqpLib\v26\RabbitMQ\DomainEvent;

use \Domain\Event\Publisher as DomainPublisher;
use Infrastructure\AmqpLib\v26\ConnectionConfig;
use Infrastructure\AmqpLib\v26\ExchangeConfig;
use Infrastructure\AmqpLib\v26\QueueWriter;

class Publisher extends DomainPublisher
{
    /**
     * Publisher constructor.
     * @param QueueWriter $host
     * @param $port
     * @param $user
     * @param $password
     * @param $vHost
     * @param $exchangeName
     */
    public function __construct(
        $host,
        $port,
        $user,
        $password,
        $vHost,
        $exchangeName
    )
    {
        $queueWriter = new QueueWriter(
            new ConnectionConfig($host, $port, $user, $password, $vHost),
            new ExchangeConfig($exchangeName, 'topic', false, true, false)
        );
        parent::__construct($queueWriter);
    }
}