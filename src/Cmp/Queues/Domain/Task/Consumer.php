<?php
namespace Cmp\Queues\Domain\Task;

use Cmp\Queues\Domain\Queue\Exception\TimeoutReaderException;
use Cmp\Queues\Domain\Queue\QueueReader;

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
     * Consumes tasks indefinitely in a blocking manner
     * @param callable $callback Callable that'll be invoked when a message is received
     * @param int      $timeout (optional) If specified, the process will block a max of $timeout seconds. Indefinitely if 0
     */
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

    /**
     * Consumes a single task in a blocking manner
     * @param callable $callback Callable that'll be invoked when a message is received
     * @param int      $timeout The process will block a max of $timeout seconds, or indefinitely if 0
     */
    public function consumeOnce(callable $callback, $timeout)
    {
        $this->queueReader->read($callback, $timeout);
    }

    /**
     * Purges all messages from the queue
     */
    public function purge()
    {
        $this->queueReader->purge();
    }
}