<?php

namespace Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
use Cmp\DomainEvent\Domain\Subscriber\AbstractSubscriber;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQSubscriber extends AbstractSubscriber
{
    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @var JSONDomainEventFactory
     */
    private $jsonDomainEventFactory;

    private $queueName;

    /**
     * @var bool
     */
    private $initialized = false;

    public function __construct(AMQPChannel $channel, JSONDomainEventFactory $jsonDomainEventFactory, $queueName)
    {
        $this->channel = $channel;
        $this->jsonDomainEventFactory = $jsonDomainEventFactory;
        $this->queueName = $queueName;
    }

    public function process()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        $this->channel->wait();
    }

    private function initialize() {
        $callback = function($msg){
            $domainEvent = $this->jsonDomainEventFactory->create($msg->body);
            $this->notify($domainEvent);
        };
        $this->channel->basic_consume($this->queueName, '', false, true, false, false, $callback);
        $this->initialized = true;
    }

    protected function isSubscribed()
    {
        return true; // RabbitMQ Topic Exchanges are handling this
    }
}