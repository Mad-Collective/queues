<?php

namespace Cmp\DomainEvent\Domain\Subscriber;

use Cmp\DomainEvent\Application\EventSubscribable;
use Cmp\DomainEvent\Application\EventSubscriber;
use Cmp\DomainEvent\Domain\Event\DomainEvent;

abstract class AbstractSubscriber implements EventSubscribable
{
    /**
     * @var array
     */
    private $subscribers = [];

    abstract public function process();

    abstract protected function isSubscribed();

    public function subscribe(EventSubscriber $eventSubscriber)
    {
        array_push($this->subscribers, $eventSubscriber);
    }

    public function notify(DomainEvent $domainEvent)
    {
        foreach($this->subscribers as $observer) {
            if ($this->isSubscribed($domainEvent)) {
                $observer->notify($domainEvent);
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