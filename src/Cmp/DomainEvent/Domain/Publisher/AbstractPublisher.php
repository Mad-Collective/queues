<?php

namespace Cmp\DomainEvent\Domain\Publisher;

use Cmp\DomainEvent\Domain\Event\DomainEvent;

abstract class AbstractPublisher
{

    private $domainEvents = [];

    /**
     * @param DomainEvent $domainEvent
     *
     * @return mixed
     */
    abstract protected function publishOne(DomainEvent $domainEvent);

    /**
     * @param array $domainEvents
     *
     * @return mixed
     */
    abstract protected function publishSome(array $domainEvents);

    /**
     * @param DomainEvent $domainEvent
     */
    public function add(DomainEvent $domainEvent)
    {
        array_push($this->domainEvents, $domainEvent);
    }

    /**
     * @throws \Cmp\DomainEvent\Domain\ConnectionException
     */
    public function publish()
    {
        $numOfDomainEvents = count($this->domainEvents);

        if ($numOfDomainEvents === 1) {
            $this->publishOne($this->domainEvents[0]);
        } else if($numOfDomainEvents > 1) {
            $this->publishSome($this->domainEvents);
        }
    }

}