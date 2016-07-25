<?php

namespace Cmp\DomainEvent\Application\Subscriber;

use Cmp\DomainEvent\Domain\Event\EventSubscriptor;
use Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriberFactory;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Psr\Log\LoggerInterface;

class Subscriber
{
    /**
     * @var \Cmp\DomainEvent\Domain\Subscriber\Subscriber
     */
    private $subscriber;

    public function __construct(RabbitMQConfig $config, array $domainTopics, LoggerInterface $logger)
    {
        $subscriberFactory = new SubscriberFactory($logger);
        $this->subscriber = $subscriberFactory->create($config, $domainTopics);
    }

    public function subscribe(EventSubscriptor $eventSubscriptor)
    {
        $this->subscriber->subscribe($eventSubscriptor);
    }

    public function start()
    {
        $this->subscriber->start();
    }

    /**
     * Process just once. This method will not block the execution.
     *
     * If you want it to keep processing call it in a loop or use the start() method.
     */
    public function processOnce()
    {
        $this->subscriber->processOnce();
    }
}