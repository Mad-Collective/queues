<?php

namespace spec\Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriterInitializer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class RabbitMQWriterSpec extends ObjectBehavior
{

    private $exchange;

    public function let(RabbitMQWriterInitializer $rabbitMQWriterInitializer, LoggerInterface $logger)
    {
        $this->exchange = 'test';
        $this->beConstructedWith($rabbitMQWriterInitializer, $this->exchange, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriter');
    }



    public function it_calls_rabbitmqpublisherInitializer_if_not_initialized(RabbitMQWriterInitializer $rabbitMQWriterInitializer, AMQPChannel $channel, DomainEvent $event)
    {
        $rabbitMQWriterInitializer->initialize()->willReturn($channel)->shouldBeCalled();

        $this->add($event);
        $this->write();
    }

    public function it_should_not_call_rabbitmqpublisherInitializer_if_already_initialized(RabbitMQWriterInitializer $rabbitMQWriterInitializer, AMQPChannel $channel, DomainEvent $event)
    {
        $rabbitMQWriterInitializer->initialize()->willReturn($channel)->shouldBeCalledTimes(1);

        $this->add($event);
        $this->write();
        $this->add($event);
        $this->write();
    }

    public function it_calls_basic_publish_with_a_message_when_there_is_only_one_domain_event_added(RabbitMQWriterInitializer $rabbitMQWriterInitializer, AMQPChannel $channel, DomainEvent $event)
    {
        $body = ['test' => 'hello'];
        $name = 'test_domain_event_name';
        $rabbitMQWriterInitializer->initialize()->willReturn($channel);
        $event->jsonSerialize()->willReturn($body); // Serialize function is mocked so we need to set the return
        $event->getName()->willReturn($name);
        $msg = new AMQPMessage(json_encode($body), array('delivery_mode' => 2));

        $channel->basic_publish(Argument::exact($msg), $this->exchange, $name)->shouldBeCalled();

        $this->add($event);
        $this->write();
    }

    public function it_calls_batch_basic_publish_with_a_message_for_every_domain_event_added(RabbitMQWriterInitializer $rabbitMQWriterInitializer, AMQPChannel $channel, DomainEvent $event, DomainEvent $event2)
    {
        $rabbitMQWriterInitializer->initialize()->willReturn($channel);

        $body = ['test' => 'hello'];
        $name = 'test_domain_event_name';
        $event->jsonSerialize()->willReturn($body); // Serialize function is mocked so we need to set the return
        $event->getName()->willReturn($name);
        $msg = new AMQPMessage(json_encode($body), array('delivery_mode' => 2));

        $body2 = ['test2' => 'hello2'];
        $name2 = 'test_domain_event_name2';
        $event2->jsonSerialize()->willReturn($body2); // Serialize function is mocked so we need to set the return
        $event2->getName()->willReturn($name2);
        $msg2 = new AMQPMessage(json_encode($body2), array('delivery_mode' => 2));

        $channel->batch_basic_publish(Argument::exact($msg), $this->exchange, $name)->shouldBeCalled();
        $channel->batch_basic_publish(Argument::exact($msg2), $this->exchange, $name2)->shouldBeCalled();
        $channel->publish_batch()->shouldBeCalled();

        $this->add($event);
        $this->add($event2);
        $this->write();
    }

    public function it_process_messages_just_once(RabbitMQWriterInitializer $rabbitMQWriterInitializer, AMQPChannel $channel, DomainEvent $event)
    {
        $body = ['test' => 'hello'];
        $name = 'test_domain_event_name';
        $rabbitMQWriterInitializer->initialize()->willReturn($channel);
        $event->jsonSerialize()->willReturn($body); // Serialize function is mocked so we need to set the return
        $event->getName()->willReturn($name);
        $msg = new AMQPMessage(json_encode($body), array('delivery_mode' => 2));

        $channel->basic_publish(Argument::exact($msg), $this->exchange, $name)->shouldBeCalledTimes(1);

        $this->add($event);
        $this->write();
        $this->write();
    }
}
