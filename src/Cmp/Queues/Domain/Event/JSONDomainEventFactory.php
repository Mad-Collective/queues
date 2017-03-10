<?php

namespace Cmp\Queues\Domain\Event;

use Cmp\Queues\Domain\Event\Exception\DomainEventException;
use Cmp\Queues\Domain\Event\Exception\InvalidJSONDomainEventException;
use Cmp\Queues\Domain\Queue\JSONMessageFactory;

class JSONDomainEventFactory implements JSONMessageFactory
{
    /**
     * @param string $json
     * @return DomainEvent
     * @throws InvalidJSONDomainEventException
     */
    public function create($json)
    {
        $domainEventArray = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJSONDomainEventException("String is not valid JSON");
        }

        if (!isset($domainEventArray['origin'], $domainEventArray['name'], $domainEventArray['occurredOn'], $domainEventArray['body'])) {
            throw new InvalidJSONDomainEventException("At least one of origin, name, occurredOn and body keys are not set");
        }

        try {
            return new DomainEvent($domainEventArray['origin'], $domainEventArray['name'], $domainEventArray['occurredOn'], $domainEventArray['body']);
        } catch (DomainEventException $e) {
            throw new InvalidJSONDomainEventException("Failed creating DomainEvent instance", 0, $e);
        }
    }
}