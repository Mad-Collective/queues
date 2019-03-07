<?php
namespace Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue;

use Cmp\Queues\Domain\Queue\Exception\InvalidJSONMessageException;
use Cmp\Queues\Domain\Queue\Exception\ReaderException;
use Cmp\Queues\Domain\Queue\JSONMessageFactory;
use Cmp\Queues\Domain\Task\Exception\ParseMessageException;
use Exception;
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

        try {
            $task = $this->jsonMessageFactory->create($message->body);
            $response = call_user_func($this->callback, $task);
            if(is_null($response) || !is_bool($response) || $response) {
                $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
            }
        } catch(InvalidJSONMessageException $e) {
            throw new ParseMessageException(json_encode($message->getBody()), 0, $e);
        }
    }

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }
}