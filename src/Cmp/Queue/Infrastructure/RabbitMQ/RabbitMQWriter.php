<?php

namespace Cmp\Queue\Infrastructure\RabbitMQ;


use Cmp\Queue\Domain\AbstractWriter;
use Cmp\Queue\Domain\Message;
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
     * @var RabbitMQWriterInitializer
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
     * @param RabbitMQWriterInitializer $rabbitMQInitializer
     * @param string                       $exchange
     * @param LoggerInterface              $logger
     */
    public function __construct(RabbitMQWriterInitializer $rabbitMQInitializer, $exchange, LoggerInterface $logger)
    {

        $this->rabbitMQInitializer = $rabbitMQInitializer;
        $this->exchange = $exchange;
        $this->logger = $logger;
    }

    /**
     * @param Message[] $messages
     *
     * @throws \Cmp\Queue\Domain\ConnectionException
     */
    public function writeSome(array $messages)
    {
        if (!$this->channel) {
            $this->channel = $this->rabbitMQInitializer->initialize();
        }

        foreach($messages as $message) {
            $this->logger->debug('Writing:' . json_encode($message));
            $msg = new AMQPMessage(json_encode($message), array('delivery_mode' => 2));
            $this->channel->batch_basic_publish($msg, $this->exchange, $message->getName());
        }

        $this->channel->publish_batch();
    }

    public function writeOne(Message $message)
    {
        if (!$this->channel) {
            $this->channel = $this->rabbitMQInitializer->initialize();
        }

        $this->logger->debug('Writing:' . json_encode($message));
        $msg = new AMQPMessage(json_encode($message), array('delivery_mode' => 2));
        $this->channel->basic_publish($msg, $this->exchange, $message->getName());
    }
}