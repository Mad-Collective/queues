<?php

namespace Cmp\Task\Domain\Producer;

use Cmp\Queue\Domain\Message\Message;

interface Producer
{

    public function add(Message $message);

    public function produce();

}