<?php

namespace Cmp\DomainEvent\Domain\Publisher;

use Cmp\Queue\Domain\Message\Message;

interface Publisher
{

    public function add(Message $message);

    public function publish();
}