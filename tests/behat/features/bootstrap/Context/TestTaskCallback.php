<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 17/02/17
 * Time: 14:42
 */

namespace Tests\Behat\Context;


use Domain\Task\Task;

class TestTaskCallback
{
    /**
     * @var Task
     */
    protected $task;

    /**
     * @param Task $task
     */
    public function setTask(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }
}