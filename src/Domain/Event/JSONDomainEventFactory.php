<?php

namespace Domain\Event;

use Domain\Event\Exception\InvalidJSONDomainEventException;
use Domain\Queue\JSONMessageFactory;

class JSONDomainEventFactory implements JSONMessageFactory
{
    /**
     * @param $json
     * @return DomainEvent
     * @throws InvalidJSONDomainEventException
     */
    public function create($json)
    {
        try {
            $domainEventArray = json_decode($json, true);
            return new DomainEvent($domainEventArray['origin'], $domainEventArray['name'], $domainEventArray['occurredOn'], $domainEventArray['body']);
        } catch (\Exception $e) {
            throw new InvalidJSONDomainEventException();
        }
    }

}