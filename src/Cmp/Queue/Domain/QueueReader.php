<?php

namespace Cmp\Queue\Domain;

interface QueueReader
{

    public function process(callable $callback);

}