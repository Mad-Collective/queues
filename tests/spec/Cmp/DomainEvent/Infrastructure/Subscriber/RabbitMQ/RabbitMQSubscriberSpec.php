<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Application\EventSubscriptor;
use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class RabbitMQSubscriberSpec extends ObjectBehavior
{

    private $queueName = 'a queue name';

    public function let(AMQPChannel $channel, JSONDomainEventFactory $jsonDomainEventFactory, LoggerInterface $logger)
    {
        $this->beConstructedWith($channel, $jsonDomainEventFactory, $this->queueName, $logger);
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

    public function it_should_notify_the_subscribed_eventsubscriptors_when_notify_is_called(
        EventSubscriptor $eventSubscriptor1,
        EventSubscriptor $eventSubscriptor2,
        DomainEvent $domainEvent)
    {
        $eventSubscriptor1->notify($domainEvent)->shouldBeCalled();
        $this->subscribe($eventSubscriptor1);

        $eventSubscriptor2->notify($domainEvent)->shouldBeCalled();
        $this->subscribe($eventSubscriptor2);

        $this->notify($domainEvent);
    }


}