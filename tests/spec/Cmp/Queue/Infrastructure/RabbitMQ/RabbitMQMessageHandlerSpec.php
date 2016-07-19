<?php

namespace spec\Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQReader;
use Cmp\Task\Domain\Consumer\Consumer;
use Cmp\Task\Domain\Task\JSONTaskFactory;
use Cmp\Task\Domain\Task\Task;
use Cmp\Task\Infrastructure\Consumer\RabbitMQ\RabbitMQConsumer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RabbitMQMessageHandlerSpec extends ObjectBehavior
{

    private $deliveryTag = "test tag";

    public function let(JSONTaskFactory $jsonTaskFactory)
    {
        $this->beConstructedWith($jsonTaskFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQMessageHandler');
    }

    public function it_should_call_the_eventCallback_with_a_domain_object(
        AMQPMessage $amqpMessage,
        JSONTaskFactory $jsonTaskFactory,
        Task $task,
        Consumer $consumer,
        AMQPChannel $amqpChannel
    ) {
        $amqpMessage->delivery_info = ['channel' => $amqpChannel, 'delivery_tag' => $this->deliveryTag];
        $jsonTaskFactory->create($amqpMessage->body)->shouldBeCalled()->willReturn($task);
        $consumer->notify($task)->shouldBeCalled();

        $this->setEventCallback(array($consumer, 'notify'));
        $this->handleMessage($amqpMessage);
    }

    public function it_should_send_ACK_after_processing_the_message(
        AMQPMessage $amqpMessage,
        JSONTaskFactory $jsonTaskFactory,
        Task $task,
        Consumer $consumer,
        AMQPChannel $amqpChannel
    ) {
        $amqpMessage->delivery_info = ['channel' => $amqpChannel, 'delivery_tag' => $this->deliveryTag];
        $jsonTaskFactory->create($amqpMessage->body)->shouldBeCalled()->willReturn($task);
        $amqpChannel->basic_ack($this->deliveryTag)->shouldBeCalled();

        $this->setEventCallback(array($consumer, 'notify'));
        $this->handleMessage($amqpMessage);
    }

}
