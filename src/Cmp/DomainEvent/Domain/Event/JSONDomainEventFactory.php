<?php

namespace Cmp\DomainEvent\Domain\Event;

use PhpSpec\Exception\Exception;

class JSONDomainEventFactory
{

    public function create($json)
    {
        try {
            $domainEventArray = json_decode($json, true);
            return new DomainEvent($domainEventArray['origin'], $domainEventArray['name'], $domainEventArray['ocurredOn'], $domainEventArray['extra']);
        } catch (Exception $e) {
            throw new InvalidJSONDomainEventException();
        }
    }

}