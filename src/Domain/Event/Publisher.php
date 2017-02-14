<?php
namespace Domain\Event;

use Infrastructure\AmqpLib\v26\QueueWriter;

class Publisher
{
    protected $queueWriter;

    protected $events = [];

    public function __construct(QueueWriter $queueWriter)
    {
        $this->queueWriter = $queueWriter;
    }

    public function add(DomainEvent $event)
    {
        $this->events[] = $event;
    }

    public function publish()
    {
        $this->queueWriter->write($this->events);
    }
}