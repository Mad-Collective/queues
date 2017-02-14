<?php
namespace Domain\Task;

use Infrastructure\AmqpLib\v26\QueueReader;

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

    /**
     * @param callable $callback
     */
    public function consume(callable $callback)
    {
        $this->queueReader->read($callback);
    }
}