<?php

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Cmp\Queues\Domain\Event\DomainEvent;
use Cmp\Queues\Domain\Queue\Exception\TimeoutReaderException;
use Cmp\Queues\Domain\Task\Task;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent\Publisher;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent\Subscriber;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\BindConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Task\Consumer;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Task\Producer;
use Psr\Log\NullLogger;

class DomainContext implements Context
{
    CONST DOMAIN_EVENT_QUEUE = 'behat-domain-event-queue-test';
    CONST DOMAIN_EVENT_EXCHANGE = 'behat-domain-event-exchange-test';

    const TASK_QUEUE = 'behat-task-queue-test';
    const TASK_EXCHANGE = 'behat-task-exchange-test';

    const VERSION = '1.0.0';

    /**
     * @var DomainEvent
     */
    protected $domainEvent;

    /**
     * @var Task
     */
    protected $task;

    /**
     * @var Subscriber
     */
    protected $subscriber;

    /**
     * @var TestEventSubscriptor
     */
    protected $subscriptor;

    /**
     * @var Consumer
     */
    protected $consumer;

    /**
     * @var TestDelayedTaskCallback
     */
    protected $delayedTaskCallback;

    protected $host;
    protected $port;
    protected $user;
    protected $password;
    protected $vHost;

    public function __construct()
    {
        $this->host = getenv('RABBITMQ_HOST');
        $this->port = getenv('RABBITMQ_PORT');
        $this->user = getenv('RABBITMQ_USER');
        $this->password = getenv('RABBITMQ_PASSWORD');
        $this->vHost = getenv('RABBITMQ_VHOST');
    }

    /**
     * @Given I send a random domain event
     */
    public function iSendARandomDomainEvent()
    {
        $this->startDomainEventConsumer();
        $this->domainEvent = new DomainEvent('behat', 'behat.test', self::VERSION, time(), array(1,2,3,4,5));
        $publisher = new Publisher(
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->vHost,
            self::DOMAIN_EVENT_EXCHANGE,
            new NullLogger()
        );

        $publisher->add($this->domainEvent);
        $publisher->publish();
    }

    /**
     * @Then I should consume the random domain event
     */
    public function iShouldConsumeTheRandomDomainEvent()
    {
        $this->subscriber->start(2);
        $incomingDomainEvent = $this->subscriptor->getDomainEvent();
        assert($incomingDomainEvent instanceof DomainEvent, 'No domain event received');
        assert($this->domainEvent->getOrigin() === $incomingDomainEvent->getOrigin(), 'Origin doesnt match');
        assert($this->domainEvent->getName() === $incomingDomainEvent->getName(), 'Name doesnt match');
        assert($this->domainEvent->getBody() === $incomingDomainEvent->getBody(), 'Body doesnt match');
        assert($this->domainEvent->getVersion() === $incomingDomainEvent->getVersion(), 'Version doesnt match');
        assert($this->domainEvent->getOccurredOn() === $incomingDomainEvent->getOccurredOn(), 'OccurredOn doesnt match');
    }

    /**
     * @Given I send a random domain event with an unwanted topic
     */
    public function iSendARandomDomainEventWithAnUnwantedTopic()
    {
        $this->startDomainEventConsumer();
        $this->domainEvent = new DomainEvent('behat', 'unwanted.topic', self::VERSION, time(), array(1,2,3,4,5));
        $publisher = new Publisher(
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->vHost,
            self::DOMAIN_EVENT_EXCHANGE,
            new NullLogger()
        );
        $publisher->add($this->domainEvent);
        $publisher->publish();
    }

    /**
     * @Then I should not consume the random domain event
     */
    public function iShouldNotConsumeTheRandomDomainEvent()
    {
        $this->subscriber->start(2);
        $incomingDomainEvent = $this->subscriptor->getDomainEvent();
        assert($incomingDomainEvent === null);
    }

    protected function startDomainEventConsumer()
    {
        $bindConfig = new BindConfig();
        $bindConfig->addTopic('behat.test');
        $this->subscriber = new Subscriber(
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->vHost,
            self::DOMAIN_EVENT_EXCHANGE,
            self::DOMAIN_EVENT_QUEUE,
            $bindConfig,
            new NullLogger()
        );

        $this->subscriptor = new TestEventSubscriptor();
        $this->subscriber->subscribe($this->subscriptor);
        $this->subscriber->start(1);
    }

    /**
     * @Given I send a random task
     */
    public function iSendARandomTask()
    {
        $this->startTaskConsumer();
        $this->task = new Task('name', array(1,2,3,4,5));
        $producer = new Producer(
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->vHost,
            self::TASK_EXCHANGE,
            new NullLogger()
        );
        $producer->add($this->task);
        $producer->produce();
    }

    /**
     * @When I purge the queue
     */
    public function iPurgeTheQueue()
    {
        $this->consumer->purge();
    }

    /**
     * @Then I should consume the random task
     */
    public function iShouldConsumeTheRandomTask()
    {
        $taskCallback = new TestTaskCallback();
        $this->consumer->consume(array($taskCallback, 'setTask'), 2);
        $consumedTask = $taskCallback->getTask();

        assert($consumedTask instanceof Task, 'No task consumed');
        assert($this->task->getName() === $consumedTask->getName(), 'Name doesnt match');
        assert($this->task->getBody() === $consumedTask->getBody(), 'Body doesnt match');
        assert($this->task->getDelay() === $consumedTask->getDelay(), 'Delay doesnt match');
    }

    /**
     * @Then I should not consume any task
     */
    public function iShouldNotConsumeAnyTask()
    {
        $taskCallback = new TestTaskCallback();

        try {
            $this->consumer->consume(array($taskCallback, 'setTask'), 2);
        } catch(TimeoutReaderException $e) {
        }

        assert($taskCallback->getTask() === null, "Expected no task but got one");
    }

    /**
     * @Given I send a random delayed task
     */
    public function iSendARandomDelayedTask()
    {
        $this->startTaskConsumer();
        $this->task = new Task('name', array(1,2,3,4,5), 1);
        $producer = new Producer(
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->vHost,
            self::TASK_EXCHANGE,
            new NullLogger()
        );
        $producer->add($this->task);
        $this->delayedTaskCallback = new TestDelayedTaskCallback();
        $this->delayedTaskCallback->sent();
        $producer->produce();
    }

    /**
     * @Then I should consume the random delayed task on time
     */
    public function iShouldConsumeTheRandomDelayedTaskOnTime()
    {
        $this->consumer->consume(array($this->delayedTaskCallback, 'setTask'), $this->task->getDelay()+2);
        $consumedTask = $this->delayedTaskCallback->getTask();

        assert($consumedTask instanceof Task, 'No task consumed');
        assert($this->delayedTaskCallback->getRealDelay() >= $this->task->getDelay(), 'Task delay didnt work as expected.');
        assert($this->task->getName() === $consumedTask->getName(), 'Name doesnt match');
        assert($this->task->getBody() === $consumedTask->getBody(), 'Body doesnt match');
        assert($this->task->getDelay() === $consumedTask->getDelay(), 'Delay doesnt match');
    }

    protected function startTaskConsumer()
    {
        $this->consumer = new Consumer(
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->vHost,
            self::TASK_EXCHANGE,
            self::TASK_QUEUE,
            new NullLogger()
        );

        $this->consumer->purge();
    }
}