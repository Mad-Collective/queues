<?php

namespace Cmp\DomainEvent\Domain\Subscriber;

use Cmp\DomainEvent\Domain\Event\EventSubscribable;
use Cmp\DomainEvent\Domain\Event\EventSubscriptor;
use Cmp\DomainEvent\Domain\Event\DomainEvent;

abstract class AbstractSubscriber implements EventSubscribable
{
    /**
     * @var array
     */
    private $subscriptors = [];

    abstract public function process();

    abstract protected function isSubscribed(DomainEvent $domainEvent);

    public function subscribe(EventSubscriptor $eventSubscriptor)
    {
        array_push($this->subscriptors, $eventSubscriptor);
    }

    public function notify(DomainEvent $domainEvent)
    {
        $this->logger->debug('Domain Event received, notifying subscribers');
        foreach($this->subscriptors as $subscriptor) {
            if ($this->isSubscribed($domainEvent)) {
                $subscriptor->notify($domainEvent);
            }
        }
    }

    public function start()
    {
        while(true) {
            $this->process();
        }
    }

}