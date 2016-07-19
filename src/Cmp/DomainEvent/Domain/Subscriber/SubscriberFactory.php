<?php

namespace Cmp\DomainEvent\Domain\Subscriber;

use Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriberFactory;
use Psr\Log\LoggerInterface;

class SubscriberFactory
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function create($host, $port, $user, $password, $exchange, $queue, $domainTopics) {
        $rabbitMQSubscriberFactory = new RabbitMQSubscriberFactory($this->logger);
        return $rabbitMQSubscriberFactory->create($host, $port, $user, $password, $exchange, $queue, $domainTopics);
    }
}