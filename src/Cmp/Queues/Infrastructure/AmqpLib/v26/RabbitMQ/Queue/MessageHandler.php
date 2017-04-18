<?php
namespace Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue;

use Cmp\Queues\Domain\Queue\JSONMessageFactory;
use PhpAmqpLib\Message\AMQPMessage;

class MessageHandler
{
    /**
     * @var JSONMessageFactory
     */
    private $jsonMessageFactory;

    /**
     * @var callable
     */
    private $callback;

    /**
     * MessageHandler constructor.
     * @param JSONMessageFactory $jsonMessageFactory
     */
    public function __construct(JSONMessageFactory $jsonMessageFactory)
    {
        $this->jsonMessageFactory = $jsonMessageFactory;
    }

    /**
     * @param AMQPMessage $message
     * @throws \Exception
     */
    public function handleMessage(AMQPMessage $message)
    {
        $task = $this->jsonMessageFactory->create($message->body);
        if (isset($this->callback)) {
            call_user_func($this->callback, $task);
        }
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }
}