#!/usr/bin/env php
<?php

use Cmp\Queues\Domain\Event\DomainEvent;
use Cmp\Queues\Infrastructure\AWS\v20121105\DomainEvent\Publisher;
use Cmp\Queues\Infrastructure\Logger\NaiveStdoutLogger;

require_once '../../../../../vendor/autoload.php';

$publisher = new Publisher('us-east-1', 'test', new NaiveStdoutLogger());
$publisher->add(new DomainEvent('queues.helper', 'test', '1.0.0', microtime(true), array(1,2,3,4,5)));
$publisher->publish();