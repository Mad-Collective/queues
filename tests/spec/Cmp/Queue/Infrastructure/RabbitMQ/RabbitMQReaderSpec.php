<?php
namespace spec\Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQMessageHandler;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQReader;
use Cmp\Task\Infrastructure\Consumer\RabbitMQ\RabbitMQConsumerInitializer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

/**
 * Class RabbitMQReaderSpec
 *
 * @package spec\Cmp\Queue\Infrastructure\RabbitMQ
 * @mixin RabbitMQReader
 */
class RabbitMQReaderSpec extends ObjectBehavior
{

    public function let(RabbitMQConsumerInitializer $rabbitMQConsumerInitializer, RabbitMQMessageHandler $rabbitMQMessageHandler, LoggerInterface $logger)
    {
        $this->beConstructedWith($rabbitMQConsumerInitializer, $rabbitMQMessageHandler, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQReader');
    }

    public function it_should_call_rabbitmqinitializer_if_not_initialized(RabbitMQConsumerInitializer $rabbitMQConsumerInitializer, AMQPChannel $channel)
    {
        $callback = function() {};
        $rabbitMQConsumerInitializer->initialize(Argument::type('callable'))->shouldBeCalled()->willReturn($channel);
        $channel->wait(null, false, 0)->shouldBeCalled();
        $this->process($callback);
    }

    public function it_should_not_call_rabbitmqinitializer_if_already_initialized(RabbitMQConsumerInitializer $rabbitMQConsumerInitializer, AMQPChannel $channel)
    {
        $callback = function() {};
        $rabbitMQConsumerInitializer->initialize(Argument::type('callable'))->willReturn($channel)->shouldBeCalledTimes(1);
        $channel->wait(null, false, 0)->shouldBeCalled();
        $this->process($callback);
        $this->process($callback);
    }

    public function it_should_call_setEventCallback_if_not_initialized(RabbitMQConsumerInitializer $rabbitMQConsumerInitializer, RabbitMQMessageHandler $rabbitMQMessageHandler, AMQPChannel $channel)
    {
        $callback = function() {};
        $rabbitMQMessageHandler->setEventCallback(Argument::type('callable'))->shouldBeCalled();
        $rabbitMQConsumerInitializer->initialize(Argument::type('callable'))->willReturn($channel);
        $channel->wait(null, false, 0)->shouldBeCalled();
        $this->process($callback);
    }

    public function it_should_pass_timeout_argument(RabbitMQConsumerInitializer $rabbitMQConsumerInitializer, AMQPChannel $channel)
    {
        $callback = function() {};
        $rabbitMQConsumerInitializer->initialize(Argument::type('callable'))->willReturn($channel);
        $timeoutAmount = 123;

        $channel->wait(null, false, $timeoutAmount)->shouldBeCalled();
        $this->process($callback, $timeoutAmount);
    }
}
