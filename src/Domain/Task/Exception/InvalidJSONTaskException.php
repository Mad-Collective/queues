<?php

namespace Domain\Task\Exception;

use Domain\Queue\InvalidJSONMessageException;

class InvalidJSONTaskException extends InvalidJSONMessageException
{
}