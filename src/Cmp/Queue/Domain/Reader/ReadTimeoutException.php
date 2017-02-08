<?php
namespace Cmp\Queue\Domain\Reader;

use Exception;

class ReadTimeoutException extends \RuntimeException
{
    public function __construct($message = "", Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
