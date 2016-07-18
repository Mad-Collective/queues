<?php

namespace Cmp\Task\Infrastructure\Consumer\RabbitMQ;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQMessageHandler;
use Cmp\Task\Domain\Consumer\AbstractConsumer;
use PhpAmqpLib\Channel\AMQPChannel;
use Psr\Log\LoggerInterface;

class RabbitMQConsumer extends AbstractConsumer
{
    /**
     * @var RabbitMQConsumerInitializer
     */
    private $rabbitMQConsumerInitializer;

    private $rabbitMQMessageHandler;

    /**
     * @var AMQPChannel
     */
    private $channel = null;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(RabbitMQConsumerInitializer $rabbitMQConsumerInitializer, RabbitMQMessageHandler $rabbitMQMessageHandler, LoggerInterface $logger)
    {
        $this->rabbitMQConsumerInitializer = $rabbitMQConsumerInitializer;
        $this->rabbitMQMessageHandler = $rabbitMQMessageHandler;
        $this->logger = $logger;
    }

    public function process()
    {
        if (!$this->channel) {
            $this->initialize();
        }

        $this->channel->wait();
    }

    private function initialize()
    {
        $this->rabbitMQMessageHandler->setEventCallback(array($this, 'notify'));
        $this->channel = $this->rabbitMQConsumerInitializer->initialize(array($this->rabbitMQMessageHandler, 'handleMessage'));
    }
}