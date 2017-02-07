<?php
namespace Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\Queue\Domain\Reader\QueueReader;
use Cmp\Queue\Domain\Reader\ReadTimeoutException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;

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

    public function process(callable $callback, $timeout = 0)
    {
        if (!$this->channel) {
            $this->initialize($callback);
        }

        try {
            $this->channel->wait(null, false, $timeout);
        } catch(AMQPTimeoutException $e) {
            throw new ReadTimeoutException("Reading timed out", $e);
        }
    }

    private function initialize(callable $callback)
    {
        $this->rabbitMQMessageHandler->setEventCallback($callback);
        $this->channel = $this->rabbitMQInitializer->initialize(array($this->rabbitMQMessageHandler, 'handleMessage'));
    }
}