<?php

namespace Cmp\DomainEvent\Infrastructure\Subscriber;

use Cmp\DomainEvent\Application\EventObserver;
use Cmp\DomainEvent\Domain\Subscriber;

abstract class AbstractSubscriber implements Subscriber
{
    /**
     * @var array
     */
    private $observers;

    public function subscribe(EventObserver $eventObserver)
    {
        array_push($this->observers, $eventObserver);
    }

    public function notify(DomainEvent $event)
    {
        foreach($this->observers as $observer) {
            if ($observer->isSubscribed($event)) {
                $observer->notify($event);
            }
        }
    }

}