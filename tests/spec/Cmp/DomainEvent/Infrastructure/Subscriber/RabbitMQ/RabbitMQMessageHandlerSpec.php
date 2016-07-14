<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
use Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriber;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;

class RabbitMQMessageHandlerSpec extends ObjectBehavior
{

    private $deliveryTag = "test tag";

    public function let(JSONDomainEventFactory $jsonDomainEventFactory)
    {
        $this->beConstructedWith($jsonDomainEventFactory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQMessageHandler');
    }

    public function it_should_call_the_eventCallback_with_a_domain_object(
        AMQPMessage $amqpMessage,
        JSONDomainEventFactory $jsonDomainEventFactory,
        DomainEvent $domainEvent,
        RabbitMQSubscriber $rabbitMQSubscriber,
        AMQPChannel $amqpChannel
    ) {
        $amqpMessage->delivery_info = ['channel' => $amqpChannel, 'delivery_tag' => $this->deliveryTag];
        $jsonDomainEventFactory->create($amqpMessage->body)->shouldBeCalled()->willReturn($domainEvent);
        $rabbitMQSubscriber->notify($domainEvent)->shouldBeCalled();

        $this->setEventCallback(array($rabbitMQSubscriber, 'notify'));
        $this->handleMessage($amqpMessage);
    }

    public function it_should_send_ACK_after_processing_the_message(
        AMQPMessage $amqpMessage,
        JSONDomainEventFactory $jsonDomainEventFactory,
        DomainEvent $domainEvent,
        RabbitMQSubscriber $rabbitMQSubscriber,
        AMQPChannel $amqpChannel
    ) {
        $amqpMessage->delivery_info = ['channel' => $amqpChannel, 'delivery_tag' => $this->deliveryTag];
        $jsonDomainEventFactory->create($amqpMessage->body)->shouldBeCalled()->willReturn($domainEvent);
        $amqpChannel->basic_ack($this->deliveryTag)->shouldBeCalled();

        $this->setEventCallback(array($rabbitMQSubscriber, 'notify'));
        $this->handleMessage($amqpMessage);
    }

}