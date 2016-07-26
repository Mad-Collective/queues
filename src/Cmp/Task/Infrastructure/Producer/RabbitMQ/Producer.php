<?php

namespace Cmp\Task\Infrastructure\Producer\RabbitMQ;

use Cmp\Queue\Domain\Message\Message;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriter;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriterInitializer;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class Producer
{

    private $writer;

    public function __construct(RabbitMQConfig $config, LoggerInterface $logger)
    {
        $logger->info('Using RabbitMQ Writer');
        $amqpLazyConnection = new AMQPLazyConnection($config->getHost(), $config->getPort(), $config->getUser(), $config->getPassword());
        $logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Exchange: %s',
            $config->getHost(), $config->getPort(), $config->getUser(), $config->getExchange()));
        $rabbitMQProducerInitializer = new RabbitMQWriterInitializer($amqpLazyConnection, $config->getExchange(), 'fanout', $logger);
        $this->writer = new RabbitMQWriter($rabbitMQProducerInitializer, $config->getExchange(), $logger);
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