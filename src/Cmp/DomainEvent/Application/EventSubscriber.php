<?php

namespace Cmp\DomainEvent\Application;

use Cmp\DomainEvent\Domain\Event\DomainEvent;

interface EventSubscriber
{

    public function notify(DomainEvent $event);

}