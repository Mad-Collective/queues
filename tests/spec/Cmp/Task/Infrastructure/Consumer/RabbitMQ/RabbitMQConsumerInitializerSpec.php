<?php

namespace spec\Cmp\Task\Infrastructure\Consumer\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class RabbitMQConsumerInitializerSpec extends ObjectBehavior
{

    private $config;

    public function let(AMQPLazyConnection $connection, LoggerInterface $logger)
    {
        $this->config = [
            'host' => 'a host',
            'port' => 'a port',
            'user' => 'a user',
            'exchange' => 'a exchange',
            'queue' => 'a queue'
        ];
        $this->beConstructedWith($connection, $this->config, $logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Task\Infrastructure\Consumer\RabbitMQ\RabbitMQConsumerInitializer');
    }

    public function it_should_initialize_the_channel(AMQPLazyConnection $connection, AMQPChannel $channel)
    {
        $callable = function() {};
        $connection->channel()->willReturn($channel);
        $channel->exchange_declare($this->config['exchange'], 'fanout', false, true, false)->shouldBeCalled();
        $channel->queue_declare($this->config['queue'], false, true, false, false)->willReturn([$this->config['queue'], '', ''])->shouldBeCalled();
        $channel->queue_bind($this->config['queue'], $this->config['exchange'])->shouldBeCalled();

        $channel->basic_consume($this->config['queue'], '', false, false, false, false, $callable)->shouldBeCalled();
        $this->initialize($callable)->shouldReturn($channel);
    }

    public function it_should_throw_ConnectionException_if_cant_connect(AMQPLazyConnection $connection)
    {
        $callable = function() {};
        $connection->channel()->willThrow(new \ErrorException());
        $this->shouldThrow(new ConnectionException('Error trying to connect to the queue backend'))->duringInitialize($callable);
    }

    public function it_should_log_an_error_if_cant_connect(AMQPLazyConnection $connection, LoggerInterface $logger)
    {
        $callable = function() {};
        $errorMessage = 'error message in test';
        $connection->channel()->willThrow(new \ErrorException($errorMessage));
        $logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Queue: %s',
            $this->config['host'], $this->config['port'], $this->config['user'], $this->config['queue']))->shouldBeCalled();
        $logger->error('Error trying to connect to rabbitMQ:' . $errorMessage)->shouldBeCalled();
        $this->shouldThrow(new ConnectionException('Error trying to connect to the queue backend'))->duringInitialize($callable);
    }


}
