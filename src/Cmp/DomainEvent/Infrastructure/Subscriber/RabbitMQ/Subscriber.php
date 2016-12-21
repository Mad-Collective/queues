<?php

namespace Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\EventSubscriptor;
use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
use Cmp\Queue\Infrastructure\RabbitMQ\AMQPLazyConnectionSingleton;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQMessageHandler;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQReader;
use Psr\Log\LoggerInterface;

class Subscriber
{

    private $subscriber;

    public function __construct(RabbitMQConfig $config, $domainTopics, LoggerInterface $logger)
    {
        $logger->info('Using RabbitMQ Subscriber');

        $jsonDomainEventFactory = new JSONDomainEventFactory();

        $amqpLazyConnection = AMQPLazyConnectionSingleton::getInstance($config->getHost(), $config->getPort(), $config->getUser(), $config->getPassword(), $config->getVhost());
        $logger->info(sprintf('RabbitMQ Configuration, Host: %s, Port: %s, User: %s, Exchange: %s, Queue: %s',
            $config->getHost(), $config->getPort(), $config->getUser(), $config->getExchange(), $config->getQueue()));
        $rabbitMQSubscriberInitializer = new RabbitMQSubscriberInitializer($amqpLazyConnection, $config->getExchange(), $config->getQueue(), $domainTopics, $logger);

        $rabbitMQMessageHandler = new RabbitMQMessageHandler($jsonDomainEventFactory);

        $rabbitMQReader = new RabbitMQReader($rabbitMQSubscriberInitializer, $rabbitMQMessageHandler, $logger);
        $this->subscriber = new \Cmp\DomainEvent\Domain\Subscriber\Subscriber($rabbitMQReader, $logger);
    }

    public function subscribe(EventSubscriptor $eventSubscriptor)
    {
        $this->subscriber->subscribe($eventSubscriptor);
    }

    public function start()
    {
        $this->subscriber->start();
    }


}