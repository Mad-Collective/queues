<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;


use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\DomainEvent\Domain\Publisher\AbstractPublisher;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class RabbitMQPublisher extends AbstractPublisher
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
     * RabbitMQPublisher constructor.
     *
     * @param RabbitMQPublisherInitializer $rabbitMQPublisherInitializer
     * @param array                        $config
     * @param LoggerInterface              $logger
     */
    public function __construct(RabbitMQPublisherInitializer $rabbitMQPublisherInitializer, array $config, LoggerInterface $logger)
    {

        $this->rabbitMQPublisherInitializer = $rabbitMQPublisherInitializer;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @throws \Cmp\Queue\Domain\ConnectionException
     */
    public function publishSome(array $domainEvents)
    {
        if (!$this->channel) {
            $this->channel = $this->rabbitMQPublisherInitializer->initialize();
        }

        foreach($domainEvents as $domainEvent) {
            $this->logger->debug('Publishing Domain Event:' . json_encode($domainEvent));
            $msg = new AMQPMessage(json_encode($domainEvent));
            $this->channel->batch_basic_publish($msg, $this->config['exchange'], $domainEvent->getName());
        }

        $this->channel->publish_batch();
    }

    public function publishOne(DomainEvent $domainEvent)
    {
        if (!$this->channel) {
            $this->channel = $this->rabbitMQPublisherInitializer->initialize();
        }

        $this->logger->debug('Publishing Domain Event:' . json_encode($domainEvent));
        $msg = new AMQPMessage(json_encode($domainEvent));
        $this->channel->basic_publish($msg, $this->config['exchange'], $domainEvent->getName());
    }
}