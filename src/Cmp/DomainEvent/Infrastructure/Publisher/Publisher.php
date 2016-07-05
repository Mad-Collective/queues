<?php

namespace Cmp\DomainEvent\Infrastructure\Publisher;

use Cmp\DomainEvent\Domain\Event\AbstractEvent;

interface Publisher
{

    public function publish(AbstractEvent $event);

}