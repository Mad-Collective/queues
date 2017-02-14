<?php

namespace Domain\Task\Exception;

use Domain\Queue\Exception\InvalidJSONMessageException;

class InvalidJSONTaskException extends InvalidJSONMessageException
{
}