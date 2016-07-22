<?php

namespace Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQInitializer;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQReaderInitializer;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Log\LoggerInterface;

class RabbitMQSubscriberInitializer implements RabbitMQReaderInitializer
{
    /**
     * @var AMQPLazyConnection
     */
    private $connection;

    /**
     * @var string
     */
    private $queue;

    /**
     * @var string
     */
    private $exchange;

    /**
     * @var string[]
     */
    private $domainTopics;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(AMQPLazyConnection $connection, $exchange, $queue, array $domainTopics, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->exchange = $exchange;
        $this->queue = $queue;
        $this->domainTopics = $domainTopics;
        $this->logger = $logger;
    }

    public function initialize(callable $msgCallback)
    {
        $this->logger->info('Connecting to RabbitMQ');

        try {
            $channel = $this->connection->channel(); // this is the one starting the connection

            $channel->exchange_declare($this->exchange, 'topic', false, true, false);

            list($queueName, ,) = $channel->queue_declare($this->queue, false, false, true, true);

            foreach($this->domainTopics as $domainTopic) {
                $this->logger->info('Binding Topic:' . $domainTopic);
                $channel->queue_bind($queueName, $this->exchange, $domainTopic);
            }

            $this->logger->info('Starting to consume RabbitMQ Queue:' . $queueName);
            $channel->basic_consume($queueName, '', false, false, true, false, $msgCallback);

            return $channel;
        } catch (\ErrorException $e) {
            $this->logger->error('Error trying to connect to rabbitMQ:' . $e->getMessage());
            throw new ConnectionException('Error trying to connect to the queue backend');
        }

    }

}