<?php

namespace Cmp\Task\Domain\Consumer;


use Cmp\Queue\Domain\Reader\QueueReader;
use Cmp\Task\Domain\Task\Task;
use Cmp\Task\Domain\Task\TaskConsumible;
use Psr\Log\LoggerInterface;

class Consumer implements TaskConsumible
{
    /**
     * @var callable
     */
    private $consumeCallback;

    /**
     * @var QueueReader
     */
    private $queueReader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(QueueReader $queueReader, LoggerInterface $logger)
    {
        $this->queueReader = $queueReader;
        $this->logger = $logger;
    }

    public function consume(callable $consumeCallback)
    {
        $this->consumeCallback = $consumeCallback;
        while(true) {
            $this->queueReader->process(array($this, 'notify'));
        }
    }

    public function notify(Task $task)
    {
        $this->logger->debug('Task received, calling consume callback');
        call_user_func($this->consumeCallback, $task);
    }

}