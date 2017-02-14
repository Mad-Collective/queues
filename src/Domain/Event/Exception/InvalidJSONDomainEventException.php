<?php

namespace Domain\Event\Exception;

use Domain\Queue\InvalidJSONMessageException;

class InvalidJSONDomainEventException extends InvalidJSONMessageException
{
}