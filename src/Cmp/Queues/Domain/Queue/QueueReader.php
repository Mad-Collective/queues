<?php

namespace Cmp\Queues\Domain\Queue;

interface QueueReader
{
    /**
     * @param callable $callback
     * @param int $timeout
     * @return void
     */
    public function read(callable $callback, $timeout=0);

    /**
     * Deletes all messages from the queue
     * @return void
     */
    public function purge();
}