<?php

namespace Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
use Cmp\DomainEvent\Infrastructure\Subscriber\AbstractSubscriber;
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

    public function __construct(AMQPChannel $channel, JSONDomainEventFactory $jsonDomainEventFactory, $queueName)
    {
        $this->channel = $channel;
        $this->jsonDomainEventFactory = $jsonDomainEventFactory;
        $this->queueName = $queueName;
    }

    public function start()
    {
        $callback = function($msg){
            $domainEvent = $this->jsonDomainEventFactory->create($msg->body);
            $this->notify($domainEvent);
        };

        $this->channel->basic_consume($this->queueName, '', false, true, false, false, $callback);

        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }
}