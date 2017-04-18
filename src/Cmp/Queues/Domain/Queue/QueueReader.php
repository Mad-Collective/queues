<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 17:32
 */

namespace Cmp\Queues\Domain\Queue;

use Cmp\Queues\Domain\Queue\Exception\ReaderException;

interface QueueReader
{
    /**
     * @param callable $callback
     * @throws ReaderException
     * @return void
     */
    public function read(callable $callback, $timeout=0);

    /**
     * Deletes all messages from the queue
     * @return void
     */
    public function purge();
}