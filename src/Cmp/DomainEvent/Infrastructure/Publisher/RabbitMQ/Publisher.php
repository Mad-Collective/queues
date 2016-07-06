<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;


use Cmp\DomainEvent\Domain\Event\AbstractEvent;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Publisher implements \Cmp\DomainEvent\Infrastructure\Publisher\Publisher
{

    private $config;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * Publisher constructor.
     *
     * @param AMQPChannel $channel
     * @param array       $config
     */
    public function __construct(AMQPChannel $channel, array $config)
    {

        $this->channel = $channel;
        $this->config = $config;
    }

    public function publish(AbstractEvent $event)
    {
        $msg = new AMQPMessage(json_encode($event));
        $this->channel->basic_publish($msg, $this->config['exchange']);
    }

}