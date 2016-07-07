<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpSpec\ObjectBehavior;

class RabbitMQSubscriberSpec extends ObjectBehavior
{

    public function let(AMQPChannel $channel, JSONDomainEventFactory $jsonDomainEventFactory)
    {
        $queueName = 'a queue name';
        $this->beConstructedWith($channel, $jsonDomainEventFactory, $queueName);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriber');
    }

}