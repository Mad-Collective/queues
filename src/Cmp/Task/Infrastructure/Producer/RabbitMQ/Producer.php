<?php

namespace Cmp\Task\Infrastructure\Producer\RabbitMQ;

use Cmp\Queue\Domain\Message\Message;
use Cmp\Queue\Infrastructure\RabbitMQ\AMQPLazyConnectionSingleton;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriter;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriterInitializer;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class Producer implements \Cmp\Task\Domain\Producer\Producer
{
    /**
     * @var RabbitMQConfig
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AMQPLazyConnection
     */
    private $connection;

    /**
     * @var RabbitMQWriter
     */
    private $writer;

    /**
     * Producer constructor.
     * @param RabbitMQConfig $config
     * @param LoggerInterface $logger
     */
    public function __construct(RabbitMQConfig $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        $this->logger->info('Using RabbitMQ Writer');
        $this->connection = AMQPLazyConnectionSingleton::getInstance(
            $config->getHost(),
            $config->getPort(),
            $config->getUser(),
            $config->getPassword(),
            $config->getVhost()
        );

        $logger->info(sprintf(
            'Connecting to RabbitMQ, Host: %s, Port: %s, VHost: %s, User: %s, Exchange: %s',
            $config->getHost(),
            $config->getPort(),
            $config->getVhost(),
            $config->getUser(),
            $config->getExchange()
        ));

        $this->generateWriter();
    }

    /**
     * @param Message $message
     * @param int $delay
     */
    public function add(Message $message, $delay = 0)
    {
        $this->generateWriter($delay);
        $this->writer->add($message);
    }

    public function produce()
    {
        $this->writer->write();
    }

    /**
     * @param int $delay
     */
    protected function generateWriter($delay = 0)
    {
        $rabbitMQProducerInitializer = new RabbitMQWriterInitializer($this->connection, $this->config->getExchange(), 'fanout', $this->logger);
        $exchange = $delay > 0
            ? $rabbitMQProducerInitializer->initializeDelayQueue($delay)
            : $this->config->getExchange();
        
        $this->writer = new RabbitMQWriter($rabbitMQProducerInitializer, $exchange, $this->logger);
    }
}