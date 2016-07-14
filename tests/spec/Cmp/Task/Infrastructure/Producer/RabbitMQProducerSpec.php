<?php

namespace spec\Cmp\Task\Infrastructure\Producer;

use Cmp\Task\Domain\Task\Task;
use Cmp\Task\Infrastructure\Producer\RabbitMQProducerInitializer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class RabbitMQProducerSpec extends ObjectBehavior
{
    private $config;

    public function let(RabbitMQProducerInitializer $rabbitMQProducerInitializer, LoggerInterface $logger)
    {
        $this->config = ['queue' => 'a queue'];
        $this->beConstructedWith($rabbitMQProducerInitializer, $this->config, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Task\Infrastructure\Producer\RabbitMQProducer');
    }

    public function it_calls_rabbitmqProducerInitializer_if_not_initialized(RabbitMQProducerInitializer $rabbitMQProducerInitializer, AMQPChannel $channel, Task $task)
    {
        $rabbitMQProducerInitializer->initialize()->willReturn($channel)->shouldBeCalled();

        $this->produce($task);
    }

    public function it_should_not_call_rabbitmqProducerInitializer_if_already_initialized(RabbitMQProducerInitializer $rabbitMQProducerInitializer, AMQPChannel $channel, Task $task)
    {
        $rabbitMQProducerInitializer->initialize()->willReturn($channel)->shouldBeCalledTimes(1);

        $this->produce($task);
        $this->produce($task);
    }

    public function it_calls_basic_publish(RabbitMQProducerInitializer $rabbitMQProducerInitializer, AMQPChannel $channel, Task $task)
    {
        $body = ['test' => 'test'];
        $task->jsonSerialize()->willReturn($body);
        $msg = new AMQPMessage(json_encode($body), array('delivery_mode' => 2));
        $rabbitMQProducerInitializer->initialize()->willReturn($channel);

        $channel->basic_publish(Argument::exact($msg), '', $this->config['queue'])->shouldBeCalled();

        $this->produce($task);
    }

}
