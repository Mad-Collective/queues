<?php

namespace Cmp\Queue\Domain\Message;

interface Message extends \JsonSerializable
{
    public function getName();
}