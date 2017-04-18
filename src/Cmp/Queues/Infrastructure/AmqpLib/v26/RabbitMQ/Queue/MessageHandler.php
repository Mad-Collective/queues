<?php
namespace Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue;

use Cmp\Queues\Domain\Queue\Exception\ReaderException;
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
        if (!isset($this->callback)) {
            throw new ReaderException("Handling a message with no callback set");
        }

        $task = $this->jsonMessageFactory->create($message->body);
        call_user_func($this->callback, $task);
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }
}