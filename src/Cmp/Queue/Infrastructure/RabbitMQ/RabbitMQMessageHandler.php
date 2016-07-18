<?php

namespace Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\Queue\Domain\JSONDomainObjectFactory;
use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\Exception\Exception;

class RabbitMQMessageHandler
{
    /**
     * @var JSONDomainObjectFactory
     */
    private $jsonDomainObjectFactory;

    /**
     * @var callable
     */
    private $eventCallback;

    public function __construct(JSONDomainObjectFactory $jsonDomainObjectFactory)
    {
        $this->jsonDomainObjectFactory = $jsonDomainObjectFactory;
    }

    public function handleMessage(AMQPMessage $msg)
    {
        try {
            $task = $this->jsonDomainObjectFactory->create($msg->body);
            call_user_func_array($this->eventCallback, [$task]);
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        } catch (Exception $e) {
            throw $e;
        }

    }

    public function setEventCallback(callable $eventCallback)
    {
        $this->eventCallback = $eventCallback;
    }
}