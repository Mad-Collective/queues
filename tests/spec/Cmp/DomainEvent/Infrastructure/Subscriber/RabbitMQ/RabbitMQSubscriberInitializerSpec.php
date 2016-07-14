<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;

class RabbitMQSubscriberInitializerSpec extends ObjectBehavior
{

    private $domainTopics;

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
        $this->domainTopics = ['user.created.#', 'user.email.#'];
        $this->beConstructedWith($connection, $this->config, $this->domainTopics, $logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriberInitializer');
    }

    public function it__should_bind_the_topics_to_the_queue_and_bind_the_provided_callback(AMQPLazyConnection $connection, AMQPChannel $channel)
    {
        $queueName = 'a queue';
        $callable = function() {};
        $connection->channel()->willReturn($channel);
        $channel->exchange_declare($this->config['exchange'], 'topic', false, true, false)->shouldBeCalled();
        $channel->queue_declare($this->config['queue'], false, true, false, false)->willReturn([$queueName, '', '']);
        $channel->queue_bind($queueName, $this->config['exchange'], $this->domainTopics[0])->shouldBeCalled();
        $channel->queue_bind($queueName, $this->config['exchange'], $this->domainTopics[1])->shouldBeCalled();
        $channel->basic_consume($queueName, '', false, false, false, false, $callable)->shouldBeCalled();

        $this->initialize($callable);
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
        $logger->info(sprintf('Connecting to RabbitMQ, Host: %s, Port: %s, User: %s, Exchange: %s, Queue: %s',
            $this->config['host'], $this->config['port'], $this->config['user'], $this->config['exchange'], $this->config['queue']))->shouldBeCalled();
        $logger->error('Error trying to connect to rabbitMQ:' . $errorMessage)->shouldBeCalled();
        $this->shouldThrow(new ConnectionException('Error trying to connect to the queue backend'))->duringInitialize($callable);
    }

}