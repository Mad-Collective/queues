<?php

namespace Cmp\Task\Domain\Consumer;


use Cmp\Task\Domain\Task\Task;
use Cmp\Task\Domain\Task\TaskConsumible;

abstract class AbstractConsumer implements TaskConsumible
{
    /**
     * @var callable
     */
    private $consumeCallback;

    abstract public function process();

    public function consume(callable $consumeCallback)
    {
        $this->consumeCallback = $consumeCallback;
        while(true) {
            $this->process();
        }
    }

    public function notify(Task $task)
    {
        $this->logger->debug('Task received, calling consume callback');
        call_user_func($this->consumeCallback, $task);
    }

    public function start()
    {
        while(true) {
            $this->process();
        }
    }

}