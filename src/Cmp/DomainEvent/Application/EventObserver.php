<?php

namespace Cmp\DomainEvent\Application;

use Cmp\DomainEvent\Domain\Event\DomainEvent;

interface EventObserver
{

    public function notify(DomainEvent $event);

    public function isSubscribed(DomainEvent $event);

}