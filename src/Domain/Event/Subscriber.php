<?php
namespace Domain\Event;

use Domain\Queue\QueueReader;

class Subscriber
{
    /**
     * @var QueueReader
     */
    protected $queueReader;

    /**
     * @var EventSubscriptor[]
     */
    protected $subscriptors = [];

    /**
     * Subscriber constructor.
     * @param QueueReader $queueReader
     */
    public function __construct(QueueReader $queueReader)
    {
        $this->queueReader = $queueReader;
    }

    /**
     * @param EventSubscriptor $eventSubscriptor
     */
    public function subscribe(EventSubscriptor $eventSubscriptor)
    {
        $this->subscriptors[] = $eventSubscriptor;
    }

    /**
     * @param $callback
     */
    public function start($callback)
    {
        while(true) {
            $this->processOne($callback);
        }
    }

    /**
     * @param $callback
     */
    public function processOne($callback)
    {
        $this->queueReader->read($callback);
    }
}