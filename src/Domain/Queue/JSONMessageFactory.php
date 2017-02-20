<?php

namespace Domain\Queue;

use Domain\Queue\Exception\InvalidJSONMessageException;

interface JSONMessageFactory
{
    /**
     * @param $json
     *
     * @throws InvalidJSONMessageException
     */
    public function create($json);
}