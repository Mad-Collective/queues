<?php

namespace Cmp\DomainEvent\Domain\Event;

interface EventSubscribable
{

    public function subscribe(EventSubscriptor $eventSubscriber);

}