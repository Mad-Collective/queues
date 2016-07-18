<?php

namespace Cmp\Queue\Domain;

interface WritableDomainObject
{

    public function getName();

    public function jsonSerialize();

}