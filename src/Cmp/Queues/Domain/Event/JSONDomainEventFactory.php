<?php

namespace Cmp\Queues\Domain\Event;

use Cmp\Queues\Domain\Event\Exception\DomainEventException;
use Cmp\Queues\Domain\Event\Exception\InvalidJSONDomainEventException;
use Cmp\Queues\Domain\Queue\JSONMessageFactory;

class JSONDomainEventFactory implements JSONMessageFactory
{
    /**
     * @param string $json
     *
     * @return DomainEvent
     * @throws InvalidJSONDomainEventException
     */
    public function create($json)
    {
        $domainEventArray = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJSONDomainEventException("String is not valid JSON");
        }

        if (!isset($domainEventArray['origin'], $domainEventArray['name'], $domainEventArray['version'], $domainEventArray['occurredOn'])) {
            throw new InvalidJSONDomainEventException("Cannot reconstruct domain event. Origin, name, version, occurredOn or body fields are missing");
        }

        try {
            return new DomainEvent(
                $domainEventArray['origin'],
                $domainEventArray['name'],
                $domainEventArray['version'],
                $domainEventArray['occurredOn'],
                isset($domainEventArray['body']) ? $domainEventArray['body'] : [],
                isset($domainEventArray['id']) ? $domainEventArray['id'] : null,
                isset($domainEventArray['isDeprecated']) ? $domainEventArray['isDeprecated'] : false,
                isset($domainEventArray['correlationId']) ? $domainEventArray['correlationId'] : null,
                isset($domainEventArray['context']) ? $domainEventArray['context'] : ""
            );
        } catch (DomainEventException $e) {
            throw new InvalidJSONDomainEventException("Failed creating DomainEvent instance", 0, $e);
        }
    }
}
