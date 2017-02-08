<?php
namespace Cmp\Task\Domain\Consumer;

use Cmp\Queue\Domain\Reader\QueueReader;
use Cmp\Queue\Domain\Reader\ReadTimeoutException;
use Cmp\Task\Domain\Task\Task;
use Psr\Log\LoggerInterface;

class Consumer
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

    /**
     * @param callable $consumeCallback
     * @param int      $timeout In seconds. If set to 0 it waits indefinitely
     *
     * @throws ReadTimeoutException
     */
    public function consume(callable $consumeCallback, $timeout = 0)
    {
        $this->consumeCallback = $consumeCallback;
        while(true) {
            $this->queueReader->process(array($this, 'notify'), $timeout);
        }
    }

    /**
     * @param callable $consumeCallback
     * @param int      $timeout In seconds. If set to 0 it waits indefinitely
     *
     * @throws ReadTimeoutException
     */
    public function consumeOnce(callable $consumeCallback, $timeout = 0)
    {
        $this->consumeCallback = $consumeCallback;
        $this->queueReader->process(array($this, 'notify'), $timeout);
    }

    public function notify(Task $task)
    {
        $this->logger->debug('Task received, calling consume callback');
        call_user_func($this->consumeCallback, $task);
    }

}