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
     * @param callable $callback
     */
    public function __construct(JSONMessageFactory $jsonMessageFactory, callable $callback)
    {
        $this->jsonMessageFactory = $jsonMessageFactory;
        $this->callback = $callback;
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
            throw $e;
        }
    }
}