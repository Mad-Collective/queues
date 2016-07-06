<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\AbstractEvent;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PublisherSpec extends ObjectBehavior
{

    public function let(AMQPChannel $channel)
    {
        $config = ['exchange' => 'test'];
        $this->beConstructedWith($channel, $config);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\Publisher');
    }

    public function it_calls_basic_publish_with_a_message(AMQPChannel $channel, AbstractEvent $event)
    {
        $body = ['test' => 'hello'];
        $event->jsonSerialize()->willReturn($body); // Serialize function is mocked so we need to set the return
        $msg = new AMQPMessage(json_encode($body));

        $channel->basic_publish(Argument::exact($msg), 'test')->shouldBeCalled();
        $this->publish($event);
    }
}