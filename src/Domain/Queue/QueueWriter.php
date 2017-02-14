<?php
namespace Domain\Queue;

interface QueueWriter
{
    /**
     * @param Message[] $message
     * @return mixed
     */
    public function write(array $message);
}