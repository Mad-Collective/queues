<?php

namespace Cmp\DomainEvent\Application;

use Cmp\DomainEvent\Domain\Event\DomainEvent;

interface EventSubscriptor
{

    public function notify(DomainEvent $event);

}