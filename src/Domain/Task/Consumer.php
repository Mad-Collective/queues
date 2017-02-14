<?php
namespace Domain\Task;

use Domain\Queue\QueueReader;

class Consumer
{
    /**
     * @var QueueReader
     */
    protected $queueReader;

    /**
     * Consumer constructor.
     * @param QueueReader $queueReader
     */
    public function __construct(QueueReader $queueReader)
    {
        $this->queueReader = $queueReader;
    }

    public function consume()
    {
        while(true) {
            $this->consumeOnce();
        }
    }

    public function consumeOnce()
    {
        $this->queueReader->read();
    }
}