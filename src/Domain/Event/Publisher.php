<?php
namespace Domain\Event;

use Domain\Queue\QueueWriter;

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