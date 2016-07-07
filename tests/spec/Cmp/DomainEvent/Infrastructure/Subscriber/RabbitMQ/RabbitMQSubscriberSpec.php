<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Application\EventObserver;
use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RabbitMQSubscriberSpec extends ObjectBehavior
{

    private $queueName = 'a queue name';

    public function let(AMQPChannel $channel, JSONDomainEventFactory $jsonDomainEventFactory)
    {
        $this->beConstructedWith($channel, $jsonDomainEventFactory, $this->queueName);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriber');
    }

    public function it_should_call_basic_consume_if_not_initialized(AMQPChannel $channel)
    {
        $channel->wait()->shouldBeCalled();
        $channel->basic_consume($this->queueName, '', false, true, false, false, Argument::type('callable'))->shouldBeCalled();
        $this->process();
    }

    public function it_should_not_call_basic_consume_if_already_initialized(AMQPChannel $channel)
    {
        $channel->wait()->shouldBeCalled();
        $channel->basic_consume($this->queueName, '', false, true, false, false, Argument::type('callable'))->shouldBeCalledTimes(1);
        $this->process();
        $this->process();
    }

    public function it_should_notify_the_subscribed_eventobservers_when_notify_is_called(
        EventObserver $eventObserver1,
        EventObserver $eventObserver2,
        DomainEvent $domainEvent)
    {
        $eventObserver1->isSubscribed($domainEvent)->shouldBeCalled()->willReturn(true);
        $eventObserver1->notify($domainEvent)->shouldBeCalled();
        $this->subscribe($eventObserver1);

        $eventObserver2->isSubscribed($domainEvent)->shouldBeCalled()->willReturn(true);
        $eventObserver2->notify($domainEvent)->shouldBeCalled();
        $this->subscribe($eventObserver2);

        $this->notify($domainEvent);
    }


}