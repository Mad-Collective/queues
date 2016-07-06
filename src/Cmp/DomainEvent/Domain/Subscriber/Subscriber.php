<?php

namespace Cmp\DomainEvent\Domain\Subscriber;

use Cmp\DomainEvent\Application\EventObserver;

interface Subscriber
{

    public function subscribe(EventObserver $observer);

    public function start();

}