<?php
namespace Domain\Queue;

use Domain\Queue\Exception\WriterException;

interface QueueWriter
{
    /**
     * @param Message[] $message
     * @throws WriterException
     * @return mixed
     */
    public function write(array $message);
}