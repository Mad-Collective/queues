#!/usr/bin/env php
<?php

use Cmp\Queues\Domain\Event\DomainEvent;
use Cmp\Queues\Domain\Event\EventSubscriptor;
use Cmp\Queues\Infrastructure\AWS\v20121105\DomainEvent\Subscriber;
use Cmp\Queues\Infrastructure\AWS\v20121105\Queue\Queue;
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

$queue = Queue::create('us-east-1');
$result = $queue->createQueueAndTopic('test', 'test');

$subscriber = new Subscriber('us-east-1', $result['queueUrl'], new NaiveStdoutLogger());
$subscriber->subscribe(new TestEventSubscriptor());
$subscriber->start();