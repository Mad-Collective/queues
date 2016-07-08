<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;


use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\DomainEvent\Domain\Publisher\Publisher;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class RabbitMQPublisher implements Publisher
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var RabbitMQPublisherInitializer
     */
    private $rabbitMQPublisherInitializer;

    /**
     * @var AMQPChannel
     */
    private $channel = null;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Publisher constructor.
     *
     * @param AMQPChannel $channel
     * @param array       $config
     */
    public function __construct(RabbitMQPublisherInitializer $rabbitMQPublisherInitializer, array $config, LoggerInterface $logger)
    {

        $this->rabbitMQPublisherInitializer = $rabbitMQPublisherInitializer;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function publish(DomainEvent $domainEvent)
    {
        if (!$this->channel) {
            $this->channel = $this->rabbitMQPublisherInitializer->initialize();
        }

        $this->logger->debug('Publishing Domain Event:' . json_encode($domainEvent));
        $msg = new AMQPMessage(json_encode($domainEvent));
        $this->channel->basic_publish($msg, $this->config['exchange'], $domainEvent->getName());
    }

}