<?php

require_once '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$config = [
    'host' => getenv('QUEUES_RABBITMQ_HOST'),
    'port' => getenv('QUEUES_RABBITMQ_PORT'),
    'user' => getenv('QUEUES_RABBITMQ_USER'),
    'password' => getenv('QUEUES_RABBITMQ_PASS'),
    'exchange' => getenv('QUEUES_RABBITMQ_EXCHANGE')
];

$logger = new \Cmp\DomainEvent\Infrastructure\Log\NullLogger();
$config = new Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig($config['host'], $config['port'], $config['user'], $config['password'], $config['exchange']);

$producer = new \Cmp\Task\Application\Producer\Producer($config, $logger);
$publisher = new \Cmp\DomainEvent\Application\Publisher\Publisher($config, $logger);

$app = new Silex\Application();

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->post('/task', function(Request $request) use ($producer) {
    $task1 = new \Cmp\Task\Domain\Task\Task($request->request->get('id'), $request->request->get('body'));
    $producer->add($task1);
    $producer->produce();

    return new Response('Processed!', 200);
});

$app->post('/domainevent', function(Request $request) use ($publisher) {
    $ocurredOn = microtime(true);
    $domainEvent1 = new Cmp\DomainEvent\Domain\Event\DomainEvent($request->request->get('origin'), $request->request->get('name'), $ocurredOn, $request->request->get('body'));
    $publisher->add($domainEvent1);
    $publisher->publish();

    return new Response('Processed!', 200);
});

$app->run();