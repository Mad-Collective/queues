<?php
namespace Domain\Event;

use Infrastructure\AmqpLib\v26\QueueReader;

class Subscriber
{
    protected $queueReader;

    protected $subscriptors = [];

    public function __construct(QueueReader $queueReader)
    {
        $this->queueReader = $queueReader;
    }

    public function subscribe(EventSubscriptor $eventSubscriptor)
    {
        $this->subscriptors[] = $eventSubscriptor;
    }

    public function start()
    {
        while(true) {
            
        }
    }
}