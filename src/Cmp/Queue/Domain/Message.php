<?php

namespace Cmp\Queue\Domain;

interface Message extends \JsonSerializable
{

    public function getName();

}