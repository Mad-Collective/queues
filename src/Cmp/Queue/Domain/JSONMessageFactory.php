<?php

namespace Cmp\Queue\Domain;

interface JSONMessageFactory
{
    /**
     * @param $json
     *
     * @throws InvalidJSONDomainObjectException
     */
    public function create($json);
}