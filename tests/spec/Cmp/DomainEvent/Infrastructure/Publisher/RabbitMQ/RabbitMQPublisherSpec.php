<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\RabbitMQPublisherInitializer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class RabbitMQPublisherSpec extends ObjectBehavior
{

    public function let(RabbitMQPublisherInitializer $rabbitMQPublisherInitializer, LoggerInterface $logger)
    {
        $config = ['exchange' => 'test'];
        $this->beConstructedWith($rabbitMQPublisherInitializer, $config, $logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\RabbitMQPublisher');
    }

    public function it_calls_rabbitmqpublisherInitializer_if_not_initialized(RabbitMQPublisherInitializer $rabbitMQPublisherInitializer, AMQPChannel $channel, DomainEvent $event)
    {
        $rabbitMQPublisherInitializer->initialize()->willReturn($channel)->shouldBeCalled();

        $this->publish($event);
    }

    public function it_should_not_call_rabbitmqpublisherInitializer_if_already_initialized(RabbitMQPublisherInitializer $rabbitMQPublisherInitializer, AMQPChannel $channel, DomainEvent $event)
    {
        $rabbitMQPublisherInitializer->initialize()->willReturn($channel)->shouldBeCalledTimes(1);

        $this->publish($event);
        $this->publish($event);
    }

    public function it_calls_basic_publish_with_a_message(RabbitMQPublisherInitializer $rabbitMQPublisherInitializer, AMQPChannel $channel, DomainEvent $event)
    {
        $body = ['test' => 'hello'];
        $name = 'test_domain_event_name';
        $rabbitMQPublisherInitializer->initialize()->willReturn($channel);
        $event->jsonSerialize()->willReturn($body); // Serialize function is mocked so we need to set the return
        $event->getName()->willReturn($name);
        $msg = new AMQPMessage(json_encode($body));

        $channel->basic_publish(Argument::exact($msg), 'test', $name)->shouldBeCalled();
        $this->publish($event);
    }
}