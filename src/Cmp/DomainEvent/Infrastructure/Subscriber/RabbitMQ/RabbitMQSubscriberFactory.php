<?php

namespace Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
use Cmp\DomainEvent\Domain\Subscriber\Subscriber;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQMessageHandler;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQReader;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Log\LoggerInterface;

class RabbitMQSubscriberFactory
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function create($config, $domainTopics)
    {
        $this->logger->info('Using RabbitMQ Subscriber');

        $jsonDomainEventFactory = new JSONDomainEventFactory();

        $amqpLazyConnection = new AMQPLazyConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $rabbitMQSubscriberInitializer = new RabbitMQSubscriberInitializer($amqpLazyConnection, $config, $domainTopics, $this->logger);

        $rabbitMQMessageHandler = new RabbitMQMessageHandler($jsonDomainEventFactory);

        $rabbitMQReader = new RabbitMQReader($rabbitMQSubscriberInitializer, $rabbitMQMessageHandler, $this->logger);
        return new Subscriber($rabbitMQReader, $this->logger);
    }

}


