<?php
namespace Cmp\Queues\Domain\Queue;

use Cmp\Queues\Domain\Queue\Exception\WriterException;

interface QueueWriter
{
    /**
     * @param Message[] $message
     * @throws WriterException
     * @return mixed
     */
    public function write(array $message);
}