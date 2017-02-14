<?php
namespace Domain\Task;

use Domain\Queue\QueueWriter;

class Producer
{
    /**
     * @var QueueWriter
     */
    protected $queueWriter;

    /**
     * @var Task[]
     */
    protected $tasks = [];

    /**
     * Producer constructor.
     * @param QueueWriter $queueWriter
     */
    public function __construct(QueueWriter $queueWriter)
    {
        $this->queueWriter = $queueWriter;
    }

    /**
     * @param Task $task
     * @return $this
     */
    public function add(Task $task)
    {
        $this->tasks[] = $task;
        return $this;
    }

    public function produce()
    {
        $this->queueWriter->write($this->tasks);
    }
}