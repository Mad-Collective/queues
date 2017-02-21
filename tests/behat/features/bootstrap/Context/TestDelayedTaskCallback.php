<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 20/02/17
 * Time: 11:49
 */

namespace Tests\Behat\Context;


use Cmp\Queues\Domain\Task\Task;

class TestDelayedTaskCallback
{
    /**
     * @var Task
     */
    protected $task;

    /**
     * @var int
     */
    protected $sentAt;

    /**
     * @var int
     */
    protected $consumedAt;

    /**
     * @param Task $task
     */
    public function setTask(Task $task)
    {
        $this->consumed();
        $this->task = $task;
    }

    public function sent()
    {
        $this->sentAt = time();
    }

    protected function consumed()
    {
        $this->consumedAt = time();
    }

    /**
     * @return int
     */
    public function getRealDelay()
    {
        return $this->consumedAt - $this->sentAt;
    }

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }
}