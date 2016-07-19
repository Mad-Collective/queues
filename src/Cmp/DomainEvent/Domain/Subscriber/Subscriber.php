<?php

namespace Cmp\DomainEvent\Domain\Subscriber;

use Cmp\DomainEvent\Domain\Event\EventSubscribable;
use Cmp\DomainEvent\Domain\Event\EventSubscriptor;
use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\Queue\Domain\QueueReader;
use Psr\Log\LoggerInterface;

class Subscriber implements EventSubscribable
{
    /**
     * @var array
     */
    private $subscriptors = [];

    /**
     * @var QueueReader
     */
    private $queueReader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(QueueReader $queueReader, LoggerInterface $logger)
    {
        $this->queueReader = $queueReader;
        $this->logger = $logger;
    }

    public function subscribe(EventSubscriptor $eventSubscriptor)
    {
        array_push($this->subscriptors, $eventSubscriptor);
    }

    public function notify(DomainEvent $domainEvent)
    {
        $this->logger->debug('Domain Event received, notifying subscribers');
        foreach($this->subscriptors as $subscriptor) {
            $subscriptor->notify($domainEvent);
        }
    }

    public function start()
    {
        while(true) {
            $this->queueReader->process(array($this, 'notify'));
        }
    }

}