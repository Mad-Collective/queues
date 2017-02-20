<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 18:33
 */

namespace Domain\Event;

interface EventSubscriptor
{
    /**
     * @param DomainEvent $domainEvent
     * @return mixed
     */
    public function notify(DomainEvent $domainEvent);

    /**
     * @param DomainEvent $domainEvent
     * @return bool
     */
    public function isSubscribed(DomainEvent $domainEvent);
}