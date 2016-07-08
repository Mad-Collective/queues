<?php

namespace Cmp\DomainEvent\Domain\Event;

use Cmp\DomainEvent\Domain\Event\DomainEvent;

interface EventSubscriptor
{

    public function notify(DomainEvent $event);

}