<?php

namespace Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
use Cmp\DomainEvent\Domain\Subscriber\AbstractSubscriber;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQMessageHandler;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class RabbitMQSubscriber extends AbstractSubscriber
{
    /**
     * @var RabbitMQSubscriberInitializer
     */
    private $rabbitMQSubscriberInitializer;

    /**
     * @var AMQPChannel
     */
    private $channel = null;

    /**
     * @var RabbitMQMessageHandler
     */
    private $rabbitMQMessageHandler;

    private $queueName;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    private $initialized = false;

    public function __construct(RabbitMQSubscriberInitializer $rabbitMQSubscriberInitializer, RabbitMQMessageHandler $rabbitMQMessageHandler, LoggerInterface $logger)
    {
        $this->rabbitMQSubscriberInitializer = $rabbitMQSubscriberInitializer;
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
        $this->channel = $this->rabbitMQSubscriberInitializer->initialize(array($this->rabbitMQMessageHandler, 'handleMessage'));
    }

    protected function isSubscribed(DomainEvent $domainEvent)
    {
        return true; // RabbitMQ Topic Exchanges are handling this
    }
}