<?php
//
//namespace Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;
//
//use Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory;
//use PhpAmqpLib\Message\AMQPMessage;
//use PhpSpec\Exception\Exception;
//
//class RabbitMQMessageHandler
//{
//    /**
//     * @var JSONDomainEventFactory
//     */
//    private $jsonDomainEventFactory;
//
//    /**
//     * @var callable
//     */
//    private $eventCallback;
//
//    public function __construct(JSONDomainEventFactory $domainEventFactory)
//    {
//        $this->jsonDomainEventFactory = $domainEventFactory;
//    }
//
//    public function handleMessage(AMQPMessage $msg)
//    {
//        try {
//            $domainEvent = $this->jsonDomainEventFactory->create($msg->body);
//            call_user_func_array($this->eventCallback, [$domainEvent]);
//            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
//        } catch (Exception $e) {
//            throw $e;
//        }
//
//    }
//
//    public function setEventCallback(callable $eventCallback)
//    {
//        $this->eventCallback = $eventCallback;
//    }
//}