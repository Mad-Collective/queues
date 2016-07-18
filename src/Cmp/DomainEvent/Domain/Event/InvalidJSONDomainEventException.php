<?php

namespace Cmp\DomainEvent\Domain\Event;

use Cmp\Queue\Domain\InvalidJSONDomainObjectException;

class InvalidJSONDomainEventException extends InvalidJSONDomainObjectException
{
}