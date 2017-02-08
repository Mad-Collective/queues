<?php
namespace Cmp\Queue\Domain\Reader;

interface QueueReader
{
    /**
     * @param callable $callback
     * @param int      $timeout  Optional. If not specified or set to 0 it blocks indefinitely
     *
     * @throws ReadTimeoutException
     *
     * @return void
     */
    public function process(callable $callback, $timeout = 0);
}