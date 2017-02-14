<?php

// Datadog Metrics
$datadogAgent = ['ip' => '127.0.0.1', 'port' => 8125];

$metricFactory = new \Cmp\Application\Monitoring\Metric\MetricFactory();
$eventFactory = new Cmp\Application\Monitoring\Event\EventFactory('queues-testhelper');

$log = new \Monolog\Logger('queues-testhelper');
$log->pushHandler(new \Monolog\Handler\NullHandler());
$monitor = new \Cmp\Application\Monitoring\Monitor($metricFactory, $eventFactory, $log);

$socket = new \Cmp\Infrastructure\Application\Monitoring\DataDog\Metric\Socket();
$metricSender = new \Cmp\Infrastructure\Application\Monitoring\DataDog\Metric\Sender($socket, $datadogAgent['ip'], $datadogAgent['port']);

$monitor->pushMetricSender($metricSender);