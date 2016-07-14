<?php

namespace spec\Cmp\Task\Infrastructure\Producer;

use Cmp\Queue\Domain\ConnectionException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class RabbitMQProducerInitializerSpec extends ObjectBehavior
{

    private $config;

    public function let(AMQPLazyConnection $connection, LoggerInterface $logger)
    {
        $this->config = [
            'host' => 'a host',
            'port' => 'a port',
            'user' => 'a user',
            'queue' => 'a queue'
        ];
        $this->beConstructedWith($connection, $this->config, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Task\Infrastructure\Producer\RabbitMQProducerInitializer');
    }

    public function it_should_declare_the_rabbit_queue(AMQPLazyConnection $connection, AMQPChannel $channel)
    {
        $connection->channel()->willReturn($channel);
        $channel->queue_declare($this->config['queue'], false, true, false, false);
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
        $logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Queue: %s',
            $this->config['host'], $this->config['port'], $this->config['user'], $this->config['queue']))->shouldBeCalled();
        $logger->error('Error trying to connect to rabbitMQ:' . $errorMessage)->shouldBeCalled();
        $this->shouldThrow(new ConnectionException('Error trying to connect to the queue backend'))->duringInitialize($callable);
    }

}
