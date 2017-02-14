<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 18:33
 */

namespace Domain\Event;


use Cmp\DomainEvent\Domain\Event\DomainEvent;

interface EventSubscriptor
{
    public function notify(DomainEvent $domainEvent);
}