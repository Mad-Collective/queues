<?php

namespace Cmp\Task\Domain\Task;

interface TaskConsumible
{

    public function consume(callable $consumeCallback);

}