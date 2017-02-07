<?php
namespace Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\Queue\Domain\Reader\QueueReader;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQReader implements QueueReader
{
    /**
     * @var RabbitMQReaderInitializer
     */
    private $rabbitMQInitializer;

    /**
     * @var AMQPChannel
     */
    private $channel = null;

    /**
     * @var RabbitMQMessageHandler
     */
    private $rabbitMQMessageHandler;

    public function __construct(RabbitMQReaderInitializer $rabbitMQInitializer, RabbitMQMessageHandler $rabbitMQMessageHandler)
    {
        $this->rabbitMQInitializer = $rabbitMQInitializer;
        $this->rabbitMQMessageHandler = $rabbitMQMessageHandler;
    }

    public function process(callable $callback)
    {

        if (!$this->channel) {
            $this->initialize($callback);
        }

        $this->channel->wait();
    }

    private function initialize(callable $callback)
    {
        $this->rabbitMQMessageHandler->setEventCallback($callback);
        $this->channel = $this->rabbitMQInitializer->initialize(array($this->rabbitMQMessageHandler, 'handleMessage'));
    }
}