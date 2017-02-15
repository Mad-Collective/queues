<?php
namespace Domain\Event;

use Domain\Queue\QueueWriter;

class Publisher
{
    /**
     * @var QueueWriter
     */
    protected $queueWriter;

    /**
     * @var DomainEvent[]
     */
    protected $events = [];

    /**
     * Publisher constructor.
     * @param QueueWriter $queueWriter
     */
    public function __construct(QueueWriter $queueWriter)
    {
        $this->queueWriter = $queueWriter;
    }

    /**
     * Add Domain Events to buffer
     * @param DomainEvent $event
     */
    public function add(DomainEvent $event)
    {
        $this->events[] = $event;
    }

    /**
     * Publishes Domain Events from buffer
     */
    public function publish()
    {
        $this->queueWriter->write($this->events);
    }
}