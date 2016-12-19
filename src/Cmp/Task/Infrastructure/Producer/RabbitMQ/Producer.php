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
     * @var RabbitMQWriter
     */
    private $writer;

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
     * Producer constructor.
     * @param RabbitMQConfig $config
     * @param LoggerInterface $logger
     */
    public function __construct(RabbitMQConfig $config, LoggerInterface $logger)
    {
        $logger->info('Using RabbitMQ Writer');

        $this->config = $config;
        $this->logger = $logger;
        $this->connection = AMQPLazyConnectionSingleton::getInstance($config->getHost(), $config->getPort(), $config->getUser(), $config->getPassword());

        $logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Exchange: %s',
            $config->getHost(), $config->getPort(), $config->getUser(), $config->getExchange()));

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

        $exchange = $this->config->getExchange();
        if ($delay > 0) {
            $exchange = $rabbitMQProducerInitializer->initializeDelayQueue($delay);
        }
        $this->writer = new RabbitMQWriter($rabbitMQProducerInitializer, $exchange, $this->logger);
    }
}