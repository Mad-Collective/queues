<?php

namespace Cmp\DomainEvent\Domain\Event;

use Cmp\Queue\Domain\JSONDomainObjectFactory;

class JSONDomainEventFactory implements JSONDomainObjectFactory
{

    public function create($json)
    {
        try {
            $domainEventArray = json_decode($json, true);
            return new DomainEvent($domainEventArray['origin'], $domainEventArray['name'], $domainEventArray['ocurredOn'], $domainEventArray['extra']);
        } catch (\Exception $e) {
            throw new InvalidJSONDomainEventException();
        }
    }

}