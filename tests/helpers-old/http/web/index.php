<?php

require_once '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$config = [
    'host' => getenv('QUEUES_RABBITMQ_HOST'),
    'port' => getenv('QUEUES_RABBITMQ_PORT'),
    'user' => getenv('QUEUES_RABBITMQ_USER'),
    'password' => getenv('QUEUES_RABBITMQ_PASS'),
    'tasks_exchange' => getenv('QUEUES_RABBITMQ_TASKS_EXCHANGE'),
    'domainevents_exchange' => getenv('QUEUES_RABBITMQ_DOMAINEVENTS_EXCHANGE'),
];

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

// Queues library
$logger = new \Cmp\Queue\Infrastructure\Log\NullLogger();

$tasksConfig = new Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig($config['host'], $config['port'], $config['user'], $config['password'], $config['tasks_exchange']);
$domainEventsConfig = new Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig($config['host'], $config['port'], $config['user'], $config['password'], $config['domainevents_exchange']);

$producer = new \Cmp\Task\Infrastructure\Producer\RabbitMQ\Producer($tasksConfig, $logger);
$publisher = new Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\Publisher($domainEventsConfig, $logger);

// Silex HTTP
$app = new Silex\Application();

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->post('/task', function(Request $request) use ($producer, $monitor) {
    $monitor->increment('queues.testhelper.task.producing');
    $task1 = new \Cmp\Task\Domain\Task\Task($request->request->get('id'), $request->request->get('body'));
    $producer->add($task1);
    $producer->produce();
    $monitor->increment('queues.testhelper.task.produced');

    return new Response('Processed!', 200);
});

$app->post('/domainevent', function(Request $request) use ($publisher, $monitor) {
    $monitor->increment('queues.testhelper.domainevent.publishing');
    $ocurredOn = microtime(true);
    $domainEvent1 = new Cmp\DomainEvent\Domain\Event\DomainEvent($request->request->get('origin'), $request->request->get('name'), $ocurredOn, $request->request->get('body'));
    $publisher->add($domainEvent1);
    $publisher->publish();
    $monitor->increment('queues.testhelper.domainevent.published');

    return new Response('Processed!', 200);
});

$app->run();