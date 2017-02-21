<?php

namespace Cmp\Queues\Domain\Queue;

use Cmp\Queues\Domain\Queue\Exception\InvalidJSONMessageException;

interface JSONMessageFactory
{
    /**
     * @param $json
     *
     * @throws InvalidJSONMessageException
     */
    public function create($json);
}