<?php

namespace Cmp\DomainEvent\Application;

interface EventSubscribable
{

    public function subscribe(EventSubscriber $eventSubscriber);

}