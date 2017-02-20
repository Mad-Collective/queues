<?php

namespace Domain\Event\Exception;

use Domain\Queue\Exception\InvalidJSONMessageException;

class InvalidJSONDomainEventException extends InvalidJSONMessageException
{
}