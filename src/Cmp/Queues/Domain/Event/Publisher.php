<?php
namespace Cmp\Queues\Domain\Event;

use Cmp\Queues\Domain\Event\Exception\DomainEventException;
use Cmp\Queues\Domain\Queue\QueueWriter;

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
     * @return $this
     */
    public function add(DomainEvent $event)
    {
        $this->events[] = $event;
        return $this;
    }
    
    /**
     * @throws DomainEventException
     */
    public function publish()
    {
        if(!isset($this->events[0])) {
            throw new DomainEventException('You must add at least 1 DomainEvent in order to publish to queue.');
        }
        $this->queueWriter->write($this->events);
        $this->events = [];
    }

    /**
     * @return DomainEvent[]
     */
    public function getEvents()
    {
        return $this->events;
    }
}