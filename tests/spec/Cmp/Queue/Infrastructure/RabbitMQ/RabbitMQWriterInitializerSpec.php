<?php

namespace spec\Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class RabbitMQWriterInitializerSpec extends ObjectBehavior
{

    private $exchangeName;

    private $exchangeType;

    public function let(AMQPLazyConnection $connection, LoggerInterface $logger)
    {
        $this->exchangeName = 'a exchange';
        $this->exchangeType = 'fanout';
        $this->beConstructedWith($connection, $this->exchangeName, $this->exchangeType, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriterInitializer');
    }

    public function it_should_declare_the_rabbit_exchange(AMQPLazyConnection $connection, AMQPChannel $channel)
    {
        $connection->channel()->willReturn($channel);
        $channel->exchange_declare($this->exchangeName, $this->exchangeType, false, false, false);
        $this->initialize()->shouldReturn($channel);
    }

    public function it_should_throw_ConnectionException_if_cant_connect(AMQPLazyConnection $connection)
    {
        $connection->channel()->willThrow(new \ErrorException());
        $this->shouldThrow(new ConnectionException('Error trying to connect to the queue backend'))->duringInitialize();
    }

    public function it_should_log_an_error_if_cant_connect(AMQPLazyConnection $connection, LoggerInterface $logger)
    {
        $callable = function() {};
        $errorMessage = 'error message in test';
        $connection->channel()->willThrow(new \ErrorException($errorMessage));
        $logger->info('Connecting to RabbitMQ')->shouldBeCalled();
        $logger->error('Error trying to connect to rabbitMQ:' . $errorMessage)->shouldBeCalled();
        $this->shouldThrow(new ConnectionException('Error trying to connect to the queue backend'))->duringInitialize($callable);
    }

    public function it_should_create_a_delay_queue_if_initialize_delay_queue_is_called(AMQPLazyConnection $connection, AMQPChannel $channel)
    {
        $connection->channel()->willReturn($channel);
        $channel->exchange_declare('Delay60a exchange', 'fanout', false, true, true)->shouldBeCalled();
        $channel->queue_declare('Delay60Queue', false, true, false, true, false, [
            'x-expires' => array('I', 65000),
            'x-message-ttl' => array('I', 60000),
            'x-dead-letter-exchange' => array('S', 'a exchange')
        ])->shouldBeCalled();
        $channel->queue_bind('Delay60Queue', 'Delay60a exchange')->shouldBeCalled();
        $this->initializeDelayQueue(60);
    }
}
