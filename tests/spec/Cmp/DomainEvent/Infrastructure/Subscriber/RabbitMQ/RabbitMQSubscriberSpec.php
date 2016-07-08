<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
use Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriberInitializer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class RabbitMQSubscriberSpec extends ObjectBehavior
{

    public function let(RabbitMQSubscriberInitializer $rabbitMQSubscriberInitializer, JSONDomainEventFactory $jsonDomainEventFactory, LoggerInterface $logger)
    {
        $this->beConstructedWith($rabbitMQSubscriberInitializer, $jsonDomainEventFactory, $logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriber');
    }

    public function it_should_call_rabbitmqinitializer_if_not_initialized(RabbitMQSubscriberInitializer $rabbitMQSubscriberInitializer, AMQPChannel $channel)
    {
        $rabbitMQSubscriberInitializer->initialize(Argument::type('callable'))->willReturn($channel);
        $channel->wait()->shouldBeCalled();
        $this->process();
    }

    public function it_should_not_call_rabbitmqinitializer_if_already_initialized(RabbitMQSubscriberInitializer $rabbitMQSubscriberInitializer, AMQPChannel $channel)
    {
        $rabbitMQSubscriberInitializer->initialize(Argument::type('callable'))->willReturn($channel)->shouldBeCalledTimes(1);
        $channel->wait()->shouldBeCalled();
        $this->process();
        $this->process();
    }

}