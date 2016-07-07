<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\DomainEvent;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RabbitMQPublisherSpec extends ObjectBehavior
{

    public function let(AMQPChannel $channel)
    {
        $config = ['exchange' => 'test'];
        $this->beConstructedWith($channel, $config);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\RabbitMQPublisher');
    }

    public function it_calls_basic_publish_with_a_message(AMQPChannel $channel, DomainEvent $event)
    {
        $body = ['test' => 'hello'];
        $name = 'test_domain_event_name';
        $event->jsonSerialize()->willReturn($body); // Serialize function is mocked so we need to set the return
        $event->getName()->willReturn($name);
        $msg = new AMQPMessage(json_encode($body));

        $channel->basic_publish(Argument::exact($msg), 'test', $name)->shouldBeCalled();
        $this->publish($event);
    }
}