<?php
namespace Cmp\DomainEvent\Domain\Subscriber;

use Cmp\DomainEvent\Domain\Event\EventSubscribable;
use Cmp\DomainEvent\Domain\Event\EventSubscriptor;
use Cmp\DomainEvent\Domain\Event\DomainEvent;
use Cmp\Queue\Domain\Reader\QueueReader;
use Cmp\Queue\Domain\Reader\ReadTimeoutException;
use Psr\Log\LoggerInterface;

class Subscriber implements EventSubscribable
{
    /**
     * @var EventSubscriptor[]
     */
    private $subscriptors = [];

    /**
     * @var QueueReader
     */
    private $queueReader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Subscriber constructor.
     *
     * @param QueueReader     $queueReader
     * @param LoggerInterface $logger
     */
    public function __construct(QueueReader $queueReader, LoggerInterface $logger)
    {
        $this->queueReader = $queueReader;
        $this->logger = $logger;
    }

    public function subscribe(EventSubscriptor $eventSubscriptor)
    {
        array_push($this->subscriptors, $eventSubscriptor);
    }

    public function notify(DomainEvent $domainEvent)
    {
        $this->logger->debug('Domain Event received, notifying subscribers');
        foreach($this->subscriptors as $subscriptor) {
            if ($subscriptor->isSubscribed($domainEvent)) {
                $subscriptor->notify($domainEvent);
            }
        }
    }

    /**
     * @param int $timeout If set to 0, it waits indefinitely
     *
     * @throws ReadTimeoutException
     */
    public function start($timeout = 0)
    {
        while(true) {
            $this->queueReader->process(array($this, 'notify'), $timeout);
        }
    }

    /**
     * @param int $timeout If set to 0, it waits indefinitely
     *
     * @throws ReadTimeoutException
     */
    public function processOnce($timeout = 0)
    {
        $this->queueReader->process(array($this, 'notify'), $timeout);
    }
}