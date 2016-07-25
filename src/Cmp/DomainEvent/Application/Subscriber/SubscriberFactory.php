<?php

namespace Cmp\DomainEvent\Application\Subscriber;

use Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriberFactory;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Psr\Log\LoggerInterface;

class SubscriberFactory
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param RabbitMQConfig $config
     * @param array          $domainTopics
     *
     * @return \Cmp\DomainEvent\Domain\Subscriber\Subscriber
     */
    public function create(RabbitMQConfig $config, array $domainTopics)
    {
        $rabbitMQSubscriberFactory = new RabbitMQSubscriberFactory($this->logger);
        return $rabbitMQSubscriberFactory->create($config, $domainTopics);
    }

}