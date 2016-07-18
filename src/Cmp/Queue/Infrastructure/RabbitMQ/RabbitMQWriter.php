<?php

namespace Cmp\Queue\Infrastructure\RabbitMQ;


use Cmp\Queue\Domain\AbstractWriter;
use Cmp\Queue\Domain\WritableDomainObject;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class RabbitMQWriter extends AbstractWriter
{
    /**
     * @var string
     */
    private $exchange;

    /**
     * @var RabbitMQInitializer
     */
    private $rabbitMQInitializer;

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
     * @param RabbitMQInitializer $rabbitMQInitializer
     * @param string                       $exchange
     * @param LoggerInterface              $logger
     */
    public function __construct(RabbitMQInitializer $rabbitMQInitializer, $exchange, LoggerInterface $logger)
    {

        $this->rabbitMQInitializer = $rabbitMQInitializer;
        $this->exchange = $exchange;
        $this->logger = $logger;
    }

    /**
     * @param WritableDomainObject[] $writableDomainObjects
     *
     * @throws \Cmp\Queue\Domain\ConnectionException
     */
    public function writeSome(array $writableDomainObjects)
    {
        if (!$this->channel) {
            $this->channel = $this->rabbitMQInitializer->initialize();
        }

        foreach($writableDomainObjects as $writableDomainObject) {
            $this->logger->debug('Writing:' . json_encode($writableDomainObject));
            $msg = new AMQPMessage(json_encode($writableDomainObject), array('delivery_mode' => 2));
            $this->channel->batch_basic_publish($msg, $this->exchange, $writableDomainObject->getName());
        }

        $this->channel->publish_batch();
    }

    public function writeOne(WritableDomainObject $writableDomainObject)
    {
        if (!$this->channel) {
            $this->channel = $this->rabbitMQInitializer->initialize();
        }

        $this->logger->debug('Writing:' . json_encode($writableDomainObject));
        $msg = new AMQPMessage(json_encode($writableDomainObject), array('delivery_mode' => 2));
        $this->channel->basic_publish($msg, $this->exchange, $writableDomainObject->getName());
    }
}