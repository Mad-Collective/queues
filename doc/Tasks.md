# Tasks

## Producer

Example code to produce a Task:

````php

$config = [
    'host' => 'rabbit_host',
    'port' => '5672',
    'user' => 'rabbitmq-server',
    'password' => 'teamcmp',
    'exchange' => 'tasksExchange3',
];

// Dont use this naive logger in production, inject your application logger ;)
$logger = new \Cmp\Queue\Infrastructure\Log\NaiveStdoutLogger();

$config = new Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig($config['host'], $config['port'], $config['user'], $config['password'], $config['exchange']);

$producer = new \Cmp\Task\Infrastructure\Producer\RabbitMQ\Producer($config, $logger);

$task1 = new \Cmp\Task\Domain\Task\Task('id', 'Request');
$task2 = new \Cmp\Task\Domain\Task\Task('id2', 'Request2');

$producer->add($task1);
$producer->add($task2);
$producer->produce();

````

## Consumer

Example code to consume Tasks:

````php

$config = [
    'host' => 'rabbit_host',
    'port' => '5672',
    'user' => 'rabbitmq-server',
    'password' => 'teamcmp',
    'exchange' => 'tasksExchange3',
    'queue' => 'tasksQueue3',
];

// Dont use this naive logger in production, inject your application logger ;)
$logger = new \Cmp\Queue\Infrastructure\Log\NaiveStdoutLogger();

$config = new Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig($config['host'], $config['port'], $config['user'], $config['password'], $config['exchange'], $config['queue']);

$consumer = new \Cmp\Task\Infrastructure\Consumer\RabbitMQ\Consumer($config, $logger);

$consumer->consume(function($task) {
    var_dump($task);
});

````