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

    public function create($host, $port, $user, $password, $exchange, $queue, $domainTopics)
    {
        $this->logger->info('Using RabbitMQ Subscriber');

        $jsonDomainEventFactory = new JSONDomainEventFactory();

        $amqpLazyConnection = new AMQPLazyConnection($host, $port, $user, $password);
        $this->logger->info(sprintf('RabbitMQ Configuration, Host: %s, Port: %s, User: %s, Exchange: %s, Queue: %s',
            $host, $port, $user, $exchange, $queue));
        $rabbitMQSubscriberInitializer = new RabbitMQSubscriberInitializer($amqpLazyConnection, $exchange, $queue, $domainTopics, $this->logger);

        $rabbitMQMessageHandler = new RabbitMQMessageHandler($jsonDomainEventFactory);

        $rabbitMQReader = new RabbitMQReader($rabbitMQSubscriberInitializer, $rabbitMQMessageHandler, $this->logger);
        return new Subscriber($rabbitMQReader, $this->logger);
    }

}


