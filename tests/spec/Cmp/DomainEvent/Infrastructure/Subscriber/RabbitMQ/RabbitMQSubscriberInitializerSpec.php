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

    private $queue;

    private $exchange;

    public function let(AMQPLazyConnection $connection, LoggerInterface $logger)
    {
        $this->domainTopics = ['user.created.#', 'user.email.#'];
        $this->beConstructedWith($connection, $this->exchange, $this->queue, $this->domainTopics, $logger);
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
        $channel->exchange_declare($this->exchange, 'topic', false, true, false)->shouldBeCalled();
        $channel->queue_declare($this->queue, false, false, true, true)->willReturn([$queueName, '', '']);
        $channel->queue_bind($queueName, $this->exchange, $this->domainTopics[0])->shouldBeCalled();
        $channel->queue_bind($queueName, $this->exchange, $this->domainTopics[1])->shouldBeCalled();
        $channel->basic_consume($queueName, '', false, false, true, false, $callable)->shouldBeCalled();

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
        $logger->info('Connecting to RabbitMQ')->shouldBeCalled();
        $logger->error('Error trying to connect to rabbitMQ:' . $errorMessage)->shouldBeCalled();
        $this->shouldThrow(new ConnectionException('Error trying to connect to the queue backend'))->duringInitialize($callable);
    }

}