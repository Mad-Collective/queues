<?php

namespace Cmp\Queue\Domain;

interface JSONDomainObjectFactory
{
    /**
     * @param $json
     *
     * @throws InvalidJSONDomainObjectException
     */
    public function create($json);
}