<?php
namespace Domain\Task;

use Domain\Queue\Exception\TimeoutReaderException;
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

    public function consume(callable $callback, $timeout=0)
    {
        while(true) {
            try {
                $this->consumeOnce($callback, $timeout);
            } catch(TimeoutReaderException $e) {
                break;
            }
        }
    }

    public function consumeOnce(callable $callback, $timeout)
    {
        $this->queueReader->read($callback, $timeout);
    }
}