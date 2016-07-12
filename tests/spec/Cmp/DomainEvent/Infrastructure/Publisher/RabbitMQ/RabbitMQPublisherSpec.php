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

        $this->add($event);
        $this->publish();
    }

    public function it_should_not_call_rabbitmqpublisherInitializer_if_already_initialized(RabbitMQPublisherInitializer $rabbitMQPublisherInitializer, AMQPChannel $channel, DomainEvent $event)
    {
        $rabbitMQPublisherInitializer->initialize()->willReturn($channel)->shouldBeCalledTimes(1);

        $this->add($event);
        $this->publish();
        $this->add($event);
        $this->publish();
    }

    public function it_calls_basic_publish_with_a_message_when_there_is_only_one_domain_event_added(RabbitMQPublisherInitializer $rabbitMQPublisherInitializer, AMQPChannel $channel, DomainEvent $event)
    {
        $body = ['test' => 'hello'];
        $name = 'test_domain_event_name';
        $rabbitMQPublisherInitializer->initialize()->willReturn($channel);
        $event->jsonSerialize()->willReturn($body); // Serialize function is mocked so we need to set the return
        $event->getName()->willReturn($name);
        $msg = new AMQPMessage(json_encode($body));

        $channel->basic_publish(Argument::exact($msg), 'test', $name)->shouldBeCalled();

        $this->add($event);
        $this->publish();
    }

    public function it_calls_batch_basic_publish_with_a_message_for_every_domain_event_added(RabbitMQPublisherInitializer $rabbitMQPublisherInitializer, AMQPChannel $channel, DomainEvent $event, DomainEvent $event2)
    {
        $rabbitMQPublisherInitializer->initialize()->willReturn($channel);

        $body = ['test' => 'hello'];
        $name = 'test_domain_event_name';
        $event->jsonSerialize()->willReturn($body); // Serialize function is mocked so we need to set the return
        $event->getName()->willReturn($name);
        $msg = new AMQPMessage(json_encode($body));

        $body2 = ['test2' => 'hello2'];
        $name2 = 'test_domain_event_name2';
        $event2->jsonSerialize()->willReturn($body2); // Serialize function is mocked so we need to set the return
        $event2->getName()->willReturn($name2);
        $msg2 = new AMQPMessage(json_encode($body2));

        $channel->batch_basic_publish(Argument::exact($msg), 'test', $name)->shouldBeCalled();
        $channel->batch_basic_publish(Argument::exact($msg2), 'test', $name2)->shouldBeCalled();
        $channel->publish_batch()->shouldBeCalled();

        $this->add($event);
        $this->add($event2);
        $this->publish();
    }
}