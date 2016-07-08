<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

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
            'exchange' => 'a exchange'
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
        $queueName = 'aqueuename';
        $callable = function() {};
        $connection->channel()->willReturn($channel);
        $channel->exchange_declare($this->config['exchange'], 'topic', false, true, false)->shouldBeCalled();
        $channel->queue_declare("", false, false, true, true)->willReturn([$queueName, '', '']);
        $channel->queue_bind($queueName, $this->config['exchange'], $this->domainTopics[0])->shouldBeCalled();
        $channel->queue_bind($queueName, $this->config['exchange'], $this->domainTopics[1])->shouldBeCalled();
        $channel->basic_consume($queueName, '', false, true, false, false, $callable)->shouldBeCalled();

        $this->initialize($callable);
    }

}