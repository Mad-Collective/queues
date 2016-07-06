<?php

namespace Cmp\DomainEvent\Domain\Publisher;

use Cmp\DomainEvent\Domain\Event\DomainEvent;

interface Publisher
{

    public function publish(DomainEvent $event);

}