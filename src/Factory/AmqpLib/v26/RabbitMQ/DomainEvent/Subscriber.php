<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 18:59
 */

namespace Factory\AmqpLib\v26\RabbitMQ\DomainEvent;

use \Domain\Event\Subscriber as DomainSubscriber;
use Infrastructure\AmqpLib\v26\BindConfig;
use Infrastructure\AmqpLib\v26\ConnectionConfig;
use Infrastructure\AmqpLib\v26\ConsumeConfig;
use Infrastructure\AmqpLib\v26\ExchangeConfig;
use Infrastructure\AmqpLib\v26\QueueConfig;
use Infrastructure\AmqpLib\v26\QueueReader;

class Subscriber extends DomainSubscriber
{
    /**
     * Subscriber constructor.
     * @param QueueReader $host
     * @param $port
     * @param $user
     * @param $password
     * @param $vHost
     * @param $exchangeName
     * @param $queueName
     * @param BindConfig $bindConfig
     */
    public function __construct(
        $host,
        $port,
        $user,
        $password,
        $vHost,
        $exchangeName,
        $queueName,
        BindConfig $bindConfig
    )
    {
        $queueReader = new QueueReader(
            new ConnectionConfig($host, $port, $user, $password, $vHost),
            new QueueConfig($queueName, false, false, true, true),
            new ExchangeConfig($exchangeName, 'topic', false, true, false),
            $bindConfig,
            new ConsumeConfig(false, false, true, false)
        );
        parent::__construct($queueReader);
    }
}