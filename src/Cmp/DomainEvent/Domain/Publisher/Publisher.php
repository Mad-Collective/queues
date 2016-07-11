<?php

namespace Cmp\DomainEvent\Domain\Publisher;

use Cmp\DomainEvent\Domain\Event\DomainEvent;

interface Publisher
{

    /**
     * @param DomainEvent $domainEvent
     *
     * @throws \Cmp\DomainEvent\Domain\Publisher\ConnectionException
     */
    public function publish(DomainEvent $event);

}