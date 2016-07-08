<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;

class RabbitMQPublisherInitializer extends ObjectBehavior
{

    private $config;

    public function let(AMQPLazyConnection $connection, LoggerInterface $logger)
    {
        $this->config = [
            'host' => 'a host',
            'port' => 'a port',
            'user' => 'a user',
            'exchange' => 'a exchange'
        ];
        $this->beConstructedWith($connection, $this->config, $logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\RabbitMQPublisherInitializer');
    }

    public function it_should_declare_the_rabbit_exchange(AMQPLazyConnection $connection, AMQPChannel $channel)
    {
        $connection->channel()->willReturn($channel);
        $channel->exchange_declare($this->config['exchange'], 'topic', false, false, false);
        $this->initialize()->shouldReturn($channel);
    }



}