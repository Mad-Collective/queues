<?php

namespace spec\Cmp\Task\Infrastructure\Consumer\RabbitMQ;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQMessageHandler;
use Cmp\Task\Infrastructure\Consumer\RabbitMQ\RabbitMQConsumerInitializer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class RabbitMQConsumerSpec extends ObjectBehavior
{

    public function let(RabbitMQConsumerInitializer $rabbitMQConsumerInitializer, RabbitMQMessageHandler $rabbitMQMessageHandler, LoggerInterface $logger)
    {
        $this->beConstructedWith($rabbitMQConsumerInitializer, $rabbitMQMessageHandler, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Task\Infrastructure\Consumer\RabbitMQ\RabbitMQConsumer');
    }

    public function it_should_call_rabbitmqinitializer_if_not_initialized(RabbitMQConsumerInitializer $rabbitMQConsumerInitializer, AMQPChannel $channel)
    {
        $rabbitMQConsumerInitializer->initialize(Argument::type('callable'))->willReturn($channel);
        $channel->wait()->shouldBeCalled();
        $this->process();
    }

    public function it_should_not_call_rabbitmqinitializer_if_already_initialized(RabbitMQConsumerInitializer $rabbitMQConsumerInitializer, AMQPChannel $channel)
    {
        $rabbitMQConsumerInitializer->initialize(Argument::type('callable'))->willReturn($channel)->shouldBeCalledTimes(1);
        $channel->wait()->shouldBeCalled();
        $this->process();
        $this->process();
    }

    public function it_should_call_setEventCallback_if_not_initialized(RabbitMQConsumerInitializer $rabbitMQConsumerInitializer, RabbitMQMessageHandler $rabbitMQMessageHandler, AMQPChannel $channel)
    {

        $rabbitMQMessageHandler->setEventCallback(Argument::type('callable'))->shouldBeCalled();
        $rabbitMQConsumerInitializer->initialize(Argument::type('callable'))->willReturn($channel);
        $channel->wait()->shouldBeCalled();
        $this->process();
    }


}
