<?php

namespace Cmp\DomainEvent\Domain\Event;

use Cmp\DomainEvent\Domain\Event\Exception\InvalidJSONDomainEventException;
use Cmp\Queue\Domain\JSONMessageFactory;


class JSONDomainEventFactory implements JSONMessageFactory
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