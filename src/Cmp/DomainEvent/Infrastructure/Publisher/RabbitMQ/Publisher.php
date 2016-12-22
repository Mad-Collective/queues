<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

use Cmp\Queue\Domain\Message\Message;
use Cmp\Queue\Infrastructure\RabbitMQ\AMQPLazyConnectionSingleton;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriter;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriterInitializer;
use Psr\Log\LoggerInterface;

class Publisher implements \Cmp\DomainEvent\Domain\Publisher\Publisher
{
    /**
     * @var RabbitMQWriter
     */
    private $writer;

    /**
     * Publisher constructor.
     *
     * @param RabbitMQConfig  $config
     * @param LoggerInterface $logger
     */
    public function __construct(RabbitMQConfig $config, LoggerInterface $logger)
    {
        $logger->info('Using RabbitMQ Publisher');
        $amqpLazyConnection = AMQPLazyConnectionSingleton::getInstance($config->getHost(), $config->getPort(), $config->getUser(), $config->getPassword(), $config->getVhost());
        $logger->info(sprintf('RabbitMQ Configuration, Host: %s, Port: %s, User: %s, Exchange: %s',
            $config->getHost(), $config->getPort(), $config->getUser(), $config->getExchange()));
        $rabbitMQWriterInitializer = new RabbitMQWriterInitializer($amqpLazyConnection, $config->getExchange(), 'topic', $logger);
        $this->writer = new RabbitMQWriter($rabbitMQWriterInitializer, $config->getExchange(), $logger);
    }

    public function add(Message $message)
    {
        $this->writer->add($message);
    }

    public function publish()
    {
        $this->writer->write();
    }
}