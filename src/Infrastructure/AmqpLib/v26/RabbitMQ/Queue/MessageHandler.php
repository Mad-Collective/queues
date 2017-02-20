<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 19:13
 */

namespace Infrastructure\AmqpLib\v26\RabbitMQ\Queue;

use Domain\Queue\JSONMessageFactory;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

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
        try {
            $task = $this->jsonMessageFactory->create($message->body);
            call_user_func_array($this->callback, [$task]);
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        } catch (\Exception $e) {
            $this->logger->error('Could not process the message: '. $e->getMessage(), $message);
            throw $e;
        }
    }

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }
}