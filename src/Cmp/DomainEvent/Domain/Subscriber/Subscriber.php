<?php

namespace Cmp\DomainEvent\Domain\Subscriber;

use Cmp\DomainEvent\Application\EventObserver;
use Cmp\DomainEvent\Domain\Event\DomainEvent;

interface Subscriber
{

    public function subscribe(EventObserver $observer);

    public function notify(DomainEvent $domainEvent);

    public function start();

    public function process();

}