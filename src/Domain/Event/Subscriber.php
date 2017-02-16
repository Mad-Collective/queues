<?php
namespace Domain\Event;

use Domain\Event\Exception\DomainEventException;
use Domain\Queue\QueueReader;
use Psr\Log\LoggerInterface;

class Subscriber
{
    /**
     * @var QueueReader
     */
    protected $queueReader;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EventSubscriptor[]
     */
    protected $subscriptors = [];

    /**
     * Subscriber constructor.
     * @param QueueReader $queueReader
     */
    public function __construct(QueueReader $queueReader, LoggerInterface $logger)
    {
        $this->queueReader = $queueReader;
        $this->logger = $logger;
    }

    /**
     * @param EventSubscriptor $eventSubscriptor
     * @return $this
     */
    public function subscribe(EventSubscriptor $eventSubscriptor)
    {
        $this->subscriptors[] = $eventSubscriptor;
        return $this;
    }

    public function start(callable $callback)
    {
        while(true) {
            $this->processOne($callback);
        }
    }

    public function processOne(callable $callback)
    {
        if(!isset($this->subscriptors[0])) {
            throw new DomainEventException('You must add at least 1 EventSubscriptor in order to publish start reading from queue.');
        }
        $this->queueReader->read($callback);
    }

    /**
     * @param DomainEvent $domainEvent
     */
    public function notify(DomainEvent $domainEvent)
    {
        $this->logger->info('Domain Event received, notifying subscribers');
        foreach($this->subscriptors as $subscriptor) {
            if($subscriptor->isSubscribed($domainEvent)) {
                $subscriptor->notify($domainEvent);
            }
        }
    }

    /**
     * @return EventSubscriptor[]
     */
    public function getSubscriptors()
    {
        return $this->subscriptors;
    }
}