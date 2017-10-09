#!/usr/bin/env php
<?php

use Cmp\Queues\Domain\Event\DomainEvent;
use Cmp\Queues\Domain\Event\EventSubscriptor;
use Cmp\Queues\Infrastructure\AWS\v20121105\DomainEvent\Subscriber;
use Cmp\Queues\Infrastructure\Logger\NaiveStdoutLogger;

require_once '../../../../../vendor/autoload.php';

class TestEventSubscriptor implements EventSubscriptor
{
    public function isSubscribed(DomainEvent $domainEvent)
    {
        return true;
    }

    public function notify(DomainEvent $domainEvent)
    {
        var_dump($domainEvent);
    }
}

$subscriber = new Subscriber('us-east-1', 'test', new NaiveStdoutLogger());
$subscriber->subscribe(new TestEventSubscriptor());
$subscriber->start();