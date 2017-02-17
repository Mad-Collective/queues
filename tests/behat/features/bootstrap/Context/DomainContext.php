<?php

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Domain\Event\DomainEvent;
use Domain\Task\Task;
use Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent\Publisher;
use Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent\Subscriber;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\BindConfig;
use Infrastructure\AmqpLib\v26\RabbitMQ\Task\Consumer;
use Infrastructure\AmqpLib\v26\RabbitMQ\Task\Producer;
use Psr\Log\NullLogger;

class DomainContext implements Context
{
    CONST DOMAIN_EVENT_QUEUE = 'behat-domain-event-queue-test';
    CONST DOMAIN_EVENT_EXCHANGE = 'behat-domain-event-exchange-test';

    const TASK_QUEUE = 'behat-task-queue-test';
    const TASK_EXCHANGE = 'behat-task-exchange-test';

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
     * @Given I send a random domain event
     */
    public function iSendARandomDomainEvent()
    {
        $this->startDomainEventConsumer();
        $this->domainEvent = new DomainEvent('behat', 'behat.test', time(), array(1,2,3,4,5));
        $publisher = new Publisher(
            'rabbitmq',
            5672,
            'guest',
            'guest',
            '/',
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
        assert($this->domainEvent->getOccurredOn() === $incomingDomainEvent->getOccurredOn(), 'OccurredOn doesnt match');
    }

    /**
     * @Given I send a random domain event with an unwanted topic
     */
    public function iSendARandomDomainEventWithAnUnwantedTopic()
    {
        $this->startDomainEventConsumer();
        $this->domainEvent = new DomainEvent('behat', 'unwanted.topic', time(), array(1,2,3,4,5));
        $publisher = new Publisher(
            'rabbitmq',
            5672,
            'guest',
            'guest',
            '/',
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
            'rabbitmq',
            5672,
            'guest',
            'guest',
            '/',
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
            'rabbitmq',
            5672,
            'guest',
            'guest',
            '/',
            self::TASK_EXCHANGE,
            new NullLogger()
        );
        $producer->add($this->task);
        $producer->produce();
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

    protected function startTaskConsumer()
    {
        $this->consumer = new Consumer(
            'rabbitmq',
            5672,
            'guest',
            'guest',
            '/',
            self::TASK_EXCHANGE,
            self::TASK_QUEUE,
            new NullLogger()
        );
        $this->consumer->consume(function(){},1);
    }
}