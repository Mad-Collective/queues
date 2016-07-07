<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Log\LoggerInterface;

class RabbitMQPublisherFactory
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function create($config)
    {
        $this->logger->info('Using RabbitMQ Publisher');
        $this->logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Exchange: %s', $config['host'], $config['port'], $config['user'], $config['exchange']));
        $amqpStreamConnection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $channel = $amqpStreamConnection->channel();
        $channel->exchange_declare($config['exchange'], 'topic', false, false, false);
        return new RabbitMQPublisher($channel, $config, $this->logger);
    }

}