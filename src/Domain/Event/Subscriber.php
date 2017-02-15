<?php
namespace Domain\Event;

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
     */
    public function subscribe(EventSubscriptor $eventSubscriptor)
    {
        $this->subscriptors[] = $eventSubscriptor;
    }

    public function start()
    {
        while(true) {
            $this->processOne();
        }
    }

    public function processOne()
    {
        $this->queueReader->read();
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
}