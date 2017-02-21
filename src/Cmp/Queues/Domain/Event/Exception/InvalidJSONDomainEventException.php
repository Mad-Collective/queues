<?php

namespace Cmp\Queues\Domain\Event\Exception;

use Cmp\Queues\Domain\Queue\Exception\InvalidJSONMessageException;

class InvalidJSONDomainEventException extends InvalidJSONMessageException
{
}