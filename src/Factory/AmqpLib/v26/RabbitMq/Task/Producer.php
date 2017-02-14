<?php

namespace Factory\AmqpLib\v26\RabbitMQ\Task;

use \Domain\Task\Producer as DomainProducer;
use Infrastructure\AmqpLib\v26\ConnectionConfig;
use Infrastructure\AmqpLib\v26\ExchangeConfig;
use Infrastructure\AmqpLib\v26\QueueWriter;

class Producer extends DomainProducer
{
    /**
     * Producer constructor.
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
            new ExchangeConfig($exchangeName, 'fanout', false, true, false)
        );
        parent::__construct($queueWriter);
    }
}