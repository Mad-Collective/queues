<?php

namespace Cmp\Queues\Domain\Queue;

use Cmp\Queues\Domain\Queue\Exception\ReaderException;

interface QueueReader
{
    /**
     * @param callable $callback
     * @param int $timeout
     * @return void
     * @throws ReaderException
     */
    public function read(callable $callback, $timeout=0);

    /**
     * Deletes all messages from the queue
     * @return void
     */
    public function purge();
}