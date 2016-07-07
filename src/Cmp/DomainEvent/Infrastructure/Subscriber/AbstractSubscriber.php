<?php

namespace Cmp\DomainEvent\Infrastructure\Subscriber;

use Cmp\DomainEvent\Application\EventObserver;
use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\DomainEvent\Domain\Subscriber\Subscriber;

abstract class AbstractSubscriber implements Subscriber
{
    /**
     * @var array
     */
    private $observers = [];

    public function subscribe(EventObserver $eventObserver)
    {
        array_push($this->observers, $eventObserver);
    }

    public function notify(DomainEvent $domainEvent)
    {
        foreach($this->observers as $observer) {
            if ($observer->isSubscribed($domainEvent)) {
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