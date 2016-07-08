<?php

namespace Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
use Cmp\DomainEvent\Domain\Subscriber\AbstractSubscriber;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
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
     * @var JSONDomainEventFactory
     */
    private $jsonDomainEventFactory;

    private $queueName;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    private $initialized = false;

    public function __construct(RabbitMQSubscriberInitializer $rabbitMQSubscriberInitializer, JSONDomainEventFactory $jsonDomainEventFactory, LoggerInterface $logger)
    {
        $this->rabbitMQSubscriberInitializer = $rabbitMQSubscriberInitializer;
        $this->jsonDomainEventFactory = $jsonDomainEventFactory;
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
        $callback = function($msg){
            $domainEvent = $this->jsonDomainEventFactory->create($msg->body);
            $this->notify($domainEvent);
        };

        $this->channel = $this->rabbitMQSubscriberInitializer->initialize($callback);
    }

    protected function isSubscribed(DomainEvent $domainEvent)
    {
        return true; // RabbitMQ Topic Exchanges are handling this
    }
}