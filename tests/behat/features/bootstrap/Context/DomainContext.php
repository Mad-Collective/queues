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
use Infrastructure\Logger\NullLogger;

class DomainContext implements Context
{
    /**
     * @var DomainEvent
     */
    protected $domainEvent;

    /**
     * @var Task
     */
    protected $task;

    /**
     * @Given I send a random domain event
     */
    public function iSendARandomDomainEvent()
    {
        $this->domainEvent = new DomainEvent('behat', 'behat.test', microtime(true), array(1,2,3,4,5));
        $publisher = new Publisher(
            'rabbitmq',
            5672,
            'guest',
            'guest',
            '/',
            'behat-test-domain-event',
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
        $bindConfig = new BindConfig();
        $bindConfig->addTopic('behat.test');

        $subscriber = new Subscriber(
            'rabbitmq',
            5672,
            'guest',
            'guest',
            '/',
            'behat-test-domain-event',
            'behat-test-domain-event',
            $bindConfig,
            new NullLogger()
        );
        $subscriptor = new TestEventSubscriptor();
        $subscriber->subscribe($subscriptor);
        $subscriber->processOne();
        var_dump($subscriptor->getDomainEvent()); exit;
    }

    /**
     * @Given I send a random task
     */
    public function iSendARandomTask()
    {
        $this->task = new Task('name', array(1,2,3,4,5));
        $producer = new Producer(
            'rabbitmq',
            5672,
            'guest',
            'guest',
            '/',
            'behat-test',
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
        $consumer = new Consumer(
            'rabbitmq',
            5672,
            'guest',
            'guest',
            '/',
            'behat-test',
            'behat-test',
            new NullLogger()
        );

        $producedTask = $this->task;
        $consumer->consume(function(Task $task) use ($producedTask) {
            assert($task->getName() === $producedTask->getName());
            assert($task->getBody() === $producedTask->getBody());
            assert($task->getDelay() === $producedTask->getDelay());
        }, 2);

    }

}