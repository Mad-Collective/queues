<?php

namespace Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
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

    public function create($config, $domainTopics) {
        $this->logger->info('Using RabbitMQ Subscriber');
        $this->logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Exchange: %s', $config['host'], $config['port'], $config['user'], $config['exchange']));
        $amqpStreamConnection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $channel = $amqpStreamConnection->channel();

        $channel->exchange_declare($config['exchange'], 'topic', false, false, false);

        list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

        foreach($domainTopics as $domainTopic) {
            $channel->queue_bind($queue_name, $config['exchange'], $domainTopic);
        }

        $jsonDomainEventFactory = new JSONDomainEventFactory();

        return new RabbitMQSubscriber($channel, $jsonDomainEventFactory, $queue_name, $this->logger);
    }

}