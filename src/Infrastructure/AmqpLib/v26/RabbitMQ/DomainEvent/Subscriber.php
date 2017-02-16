<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 18:59
 */

namespace Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent;

use Domain\Event\JSONDomainEventFactory;
use Domain\Event\Subscriber as DomainSubscriber;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\BindConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ConsumeConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\ExchangeConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\QueueConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\MessageHandler;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\QueueReader;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class Subscriber extends DomainSubscriber
{
    /**
     * Subscriber constructor.
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $password
     * @param string $vHost
     * @param string $exchangeName
     * @param string $queueName
     * @param BindConfig $bindConfig
     * @param LoggerInterface $logger
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
        LoggerInterface $logger
    )
    {
        $messageHandler = new MessageHandler(new JSONDomainEventFactory());
        $messageHandler->setCallback(array($this, 'notify'));
        $queueReader = new QueueReader(
            new AMQPLazyConnection($host, $port, $user, $password, $vHost),
            new QueueConfig(uniqid($queueName.'_', true), false, false, true, true),
            new ExchangeConfig($exchangeName, 'topic', false, true, false),
            $bindConfig,
            new ConsumeConfig(false, false, true, false),
            $messageHandler,
            $logger
        );
        parent::__construct($queueReader, $logger);
    }
}