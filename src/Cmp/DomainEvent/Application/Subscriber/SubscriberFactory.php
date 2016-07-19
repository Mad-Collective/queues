<?php

namespace Cmp\DomainEvent\Application\Subscriber;

use Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriberFactory;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
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

    public function create(RabbitMQConfig $config, $domainTopics) {
        $rabbitMQSubscriberFactory = new RabbitMQSubscriberFactory($this->logger);
        return $rabbitMQSubscriberFactory->create($config, $domainTopics);
    }
}