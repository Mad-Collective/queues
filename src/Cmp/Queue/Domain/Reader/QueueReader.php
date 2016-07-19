<?php

namespace Cmp\Queue\Domain\Reader;

interface QueueReader
{

    public function process(callable $callback);

}