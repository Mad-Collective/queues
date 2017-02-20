# Tasks

## Producer

Example code to produce a Task:

````php

use Domain\Task\Task;
use Infrastructure\AmqpLib\v26\RabbitMQ\Task\Producer;
use Infrastructure\Logger\NaiveStdoutLogger;

// Replace for your app logger!!
$logger = new NaiveStdoutLogger();

$producer = new Producer(
    'localhost', //host
    5672, //port
    'guest', //username
    'guest', //password
    '/, //vhost
    'test', //exchange name
    $logger
);

$producer->add(new Task('direct1', array(1,2,3,4,5))); //direct
$producer->add(new Task('10sec', array(1,2,3,4,5), 10)); // 10sec delay
$producer->add(new Task('5sec', array(1,2,3,4,5), 5)); //5sec delay
$producer->produce();

````

## Consumer

Example code to consume Tasks:

````php

use Domain\Task\Task;
use Infrastructure\AmqpLib\v26\RabbitMQ\Task\Consumer;
use Infrastructure\Logger\NaiveStdoutLogger;

// Replace for your app logger!!
$logger = new NaiveStdoutLogger();

$consumer = new Consumer(
    'localhost', //host
    5672, //port
    'guest', //username
    'guest', //password
    '/', //vhost
    'test', //exchange
    'test', //queue
    $logger
);

$consumer->consume(function(Task $task){
    var_dump($task);
});

````