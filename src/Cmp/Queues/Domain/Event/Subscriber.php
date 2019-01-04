<?php
namespace Cmp\Queues\Domain\Event;

use Cmp\Queues\Domain\Event\Exception\DomainEventException;
use Cmp\Queues\Domain\Queue\Exception\GracefulStopException;
use Cmp\Queues\Domain\Queue\Exception\TimeoutReaderException;
use Cmp\Queues\Domain\Queue\QueueReader;
use Cmp\Queues\Domain\Task\Exception\ParseMessageException;
use Psr\Log\LoggerInterface;

class Subscriber
{
    /**
     * @var QueueReader
     */
    protected $queueReader;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EventSubscriptor[]
     */
    protected $subscriptors = [];

    /**
     * Subscriber constructor.
     * @param QueueReader $queueReader
     * @param LoggerInterface $logger
     */
    public function __construct(QueueReader $queueReader, LoggerInterface $logger)
    {
        $this->queueReader = $queueReader;
        $this->logger = $logger;
    }

    /**
     * @param EventSubscriptor $eventSubscriptor
     * @return $this
     */
    public function subscribe(EventSubscriptor $eventSubscriptor)
    {
        $this->subscriptors[] = $eventSubscriptor;
        return $this;
    }

    /**
     * @param int $timeout
     * @throws DomainEventException
     */
    public function start($timeout=0)
    {
        if(!isset($this->subscriptors[0])) {
            throw new DomainEventException('You must add at least 1 EventSubscriptor in order to publish start reading from queue.');
        }

        while(true) {
            try {
                $this->queueReader->read(array($this, 'notify'), $timeout);
            } catch(ParseMessageException $e){
                break;
            } catch(TimeoutReaderException $e) {
                break;
            } catch(GracefulStopException $e) {
                break;
            }
        }
    }

    /**
     * @param int $timeout
     * @throws DomainEventException
     */
    public function batch($timeout=0)
    {
        if(!isset($this->subscriptors[0])) {
            throw new DomainEventException('You must add at least 1 EventSubscriptor in order to publish start reading from queue.');
        }

        $this->queueReader->read(array($this, 'notify'), $timeout);
    }

    /**
     * @param DomainEvent $domainEvent
     */
    public function notify(DomainEvent $domainEvent)
    {
        $this->logger->debug('Domain Event received, notifying subscribers');
        foreach($this->subscriptors as $subscriptor) {
            if($subscriptor->isSubscribed($domainEvent)) {
                $subscriptor->notify($domainEvent);
            }
        }
    }

    /**
     * @return EventSubscriptor[]
     */
    public function getSubscriptors()
    {
        return $this->subscriptors;
    }
}