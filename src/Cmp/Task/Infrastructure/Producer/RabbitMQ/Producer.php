<?php

namespace Cmp\Task\Infrastructure\Producer\RabbitMQ;

use Cmp\Queue\Domain\Message\Message;
use Cmp\Queue\Infrastructure\RabbitMQ\AMQPLazyConnectionSingleton;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriter;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriterInitializer;
use Psr\Log\LoggerInterface;

class Producer implements \Cmp\Task\Domain\Producer\Producer
{

    private $writer;

    public function __construct(RabbitMQConfig $config, LoggerInterface $logger, $delay = 0)
    {
        $logger->info('Using RabbitMQ Writer');
        $amqpLazyConnection = AMQPLazyConnectionSingleton::getInstance($config->getHost(), $config->getPort(), $config->getUser(), $config->getPassword());
        $logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Exchange: %s',
            $config->getHost(), $config->getPort(), $config->getUser(), $config->getExchange()));

        $rabbitMQProducerInitializer = new RabbitMQWriterInitializer($amqpLazyConnection, $config->getExchange(), 'fanout', $logger);

        $exchange = $config->getExchange();
        if ($delay > 0) {
            $exchange = $rabbitMQProducerInitializer->initializeDelayQueue($delay);
        }
        $this->writer = new RabbitMQWriter($rabbitMQProducerInitializer, $exchange, $logger);
    }

    public function add(Message $message)
    {
        $this->writer->add($message);
    }

    public function produce()
    {
        $this->writer->write();
    }
}