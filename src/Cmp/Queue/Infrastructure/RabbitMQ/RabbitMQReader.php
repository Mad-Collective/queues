<?php

namespace Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\DomainEvent\Domain\Subscriber\AbstractSubscriber;
use Cmp\Queue\Domain\QueueReader;
use Psr\Log\LoggerInterface;

class RabbitMQReader implements QueueReader
{
    /**
     * @var RabbitMQInitializer
     */
    private $rabbitMQInitializer;

    /**
     * @var AMQPChannel
     */
    private $channel = null;

    /**
     * @var RabbitMQMessageHandler
     */
    private $rabbitMQMessageHandler;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    private $initialized = false;

    public function __construct(RabbitMQReaderInitializer $rabbitMQInitializer, RabbitMQMessageHandler $rabbitMQMessageHandler, LoggerInterface $logger)
    {
        $this->rabbitMQInitializer = $rabbitMQInitializer;
        $this->rabbitMQMessageHandler = $rabbitMQMessageHandler;
        $this->logger = $logger;
    }

    public function process(callable $callback)
    {

        if (!$this->channel) {
            $this->initialize($callback);
        }

        $this->channel->wait();
    }

    private function initialize($callback)
    {
        $this->rabbitMQMessageHandler->setEventCallback($callback);
        $this->channel = $this->rabbitMQInitializer->initialize(array($this->rabbitMQMessageHandler, 'handleMessage'));
    }

    protected function isSubscribed(DomainEvent $domainEvent)
    {
        return true; // RabbitMQ Topic Exchanges are handling this
    }
}