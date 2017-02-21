<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 16/02/17
 * Time: 12:24
 */

namespace Tests\Behat\Context;

use Cmp\Queues\Domain\Event\DomainEvent;
use Cmp\Queues\Domain\Event\EventSubscriptor;

class TestEventSubscriptor implements EventSubscriptor
{
    /**
     * @var DomainEvent
     */
    protected $domainEvent;

    public function notify(DomainEvent $domainEvent)
    {
        $this->domainEvent = $domainEvent;
    }

    /**
     * @param DomainEvent $domainEvent
     * @return bool
     */
    public function isSubscribed(DomainEvent $domainEvent)
    {
        return true;
    }

    /**
     * @return DomainEvent
     */
    public function getDomainEvent()
    {
        return $this->domainEvent;
    }
}