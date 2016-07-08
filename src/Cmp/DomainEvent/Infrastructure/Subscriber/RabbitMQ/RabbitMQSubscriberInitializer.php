<?php

namespace Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Log\LoggerInterface;

class RabbitMQSubscriberInitializer
{
    /**
     * @var AMQPLazyConnection
     */
    private $connection;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $domainTopics;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(AMQPLazyConnection $connection, array $config, array $domainTopics, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->config = $config;
        $this->domainTopics = $domainTopics;
        $this->logger = $logger;
    }

    public function initialize(callable $msgCallback)
    {
        $this->logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Exchange: %s', $this->config['host'], $this->config['port'], $this->config['user'], $this->config['exchange']));

        $channel = $this->connection->channel(); // this is the one starting the connection

        $channel->exchange_declare($this->config['exchange'], 'topic', false, false, false);

        list($queueName, ,) = $channel->queue_declare("", false, false, true, false);

        foreach($this->domainTopics as $domainTopic) {
            $this->logger->info('Binding Topic:' . $domainTopic);
            $channel->queue_bind($queueName, $this->config['exchange'], $domainTopic);
        }

        $this->logger->info('Starting to consume RabbitMQ Queue:' . $queueName);
        $channel->basic_consume($queueName, '', false, true, false, false, $msgCallback);

        return $channel;
    }

}