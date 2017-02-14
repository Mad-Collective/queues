<?php

namespace Domain\Queue;

interface JSONMessageFactory
{
    /**
     * @param $json
     *
     * @throws InvalidJSONMessageException
     */
    public function create($json);
}