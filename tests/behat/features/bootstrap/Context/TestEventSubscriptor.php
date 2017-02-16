<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 16/02/17
 * Time: 12:24
 */

namespace Tests\Behat\Context;

use Domain\Event\DomainEvent;
use Domain\Event\EventSubscriptor;

class TestEventSubscriptor implements EventSubscriptor
{
    protected $domainEvent;

    public function notify(DomainEvent $domainEvent)
    {
        $this->domainEvent = $domainEvent;
    }

    public function isSubscribed(DomainEvent $domainEvent)
    {
        return true;
    }

    public function getDomainEvent()
    {
        return $this->domainEvent;
    }
}