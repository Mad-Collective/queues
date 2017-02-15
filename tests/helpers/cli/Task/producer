#!/usr/bin/env php
<?php

use Domain\Task\Task;
use Infrastructure\AmqpLib\v26\RabbitMQ\Task\Producer;

require_once '/app/queues/vendor/autoload.php';

$logger = new \Cmp\Queue\Infrastructure\Log\NaiveStdoutLogger();

$producer = new Producer(
    'rabbitmq',
    5672,
    'guest',
    'guest',
    '/',
    'test',
    $logger
);

$producer->add(new Task('test', array(1,2,3,4,5)));
$producer->produce();