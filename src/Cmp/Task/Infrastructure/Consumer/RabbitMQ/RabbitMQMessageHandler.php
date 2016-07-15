<?php

namespace Cmp\Task\Infrastructure\Consumer\RabbitMQ;

use Cmp\Task\Domain\Task\JSONTaskFactory;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQMessageHandler
{
    /**
     * @var JSONTaskFactory
     */
    private $jsonTaskFactory;

    /**
     * @var callable
     */
    private $eventCallback;

    public function __construct(JSONTaskFactory $jsonTaskFactory)
    {
        $this->jsonTaskFactory = $jsonTaskFactory;
    }

    public function handleMessage(AMQPMessage $msg)
    {
        $task = $this->jsonTaskFactory->create($msg->body);
        call_user_func_array($this->eventCallback, [$task]);
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }

    public function setEventCallback(callable $eventCallback)
    {
        $this->eventCallback = $eventCallback;
    }
}