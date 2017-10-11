#!/usr/bin/env php
<?php

use Cmp\Queues\Domain\Event\DomainEvent;
use Cmp\Queues\Infrastructure\AWS\v20121105\DomainEvent\Publisher;
use Cmp\Queues\Infrastructure\Logger\NaiveStdoutLogger;
use Cmp\Queues\Infrastructure\AWS\v20121105\Queue\Queue;

require_once '../../../../../vendor/autoload.php';

$queue = Queue::create('us-east-1');
$result = $queue->createQueueAndTopic('test', 'test');

$publisher = new Publisher('us-east-1', $result['topicArn'], new NaiveStdoutLogger());
$publisher->add(new DomainEvent('queues.helper', 'test', '1.0.0', time(), array(1,2,3,4,5)));
$publisher->publish();