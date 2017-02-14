<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 18:59
 */

namespace Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent;

use Domain\Event\Subscriber as DomainSubscriber;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\BindConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ConnectionConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ConsumeConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ExchangeConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\QueueConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\QueueReader;
use Psr\Log\LoggerInterface;

class Subscriber extends DomainSubscriber
{
    /**
     * Subscriber constructor.
     * @param string $host
     * @param $port
     * @param $user
     * @param $password
     * @param $vHost
     * @param $exchangeName
     * @param $queueName
     * @param BindConfig $bindConfig
     * @param LoggerInterface $logger
     * @param callable $callback
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
        callable $callback
    )
    {
        $queueReader = new QueueReader(
            new ConnectionConfig($host, $port, $user, $password, $vHost),
            new QueueConfig($queueName, false, false, true, true),
            new ExchangeConfig($exchangeName, 'topic', false, true, false),
            $bindConfig,
            new ConsumeConfig(false, false, true, false),
            $logger,
            $callback
        );
        parent::__construct($queueReader);
    }
}