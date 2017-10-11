<?php

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Cmp\Queues\Domain\Event\DomainEvent;
use Cmp\Queues\Domain\Event\Publisher;
use Cmp\Queues\Domain\Event\Subscriber;
use Cmp\Queues\Domain\Queue\Exception\TimeoutReaderException;
use Cmp\Queues\Domain\Task\Task;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent\Publisher as RabbitMQPublisher;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent\Subscriber as RabbitMQSubscriber;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\BindConfig;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Task\Consumer;
use Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Task\Producer;
use Cmp\Queues\Infrastructure\AWS\v20121105\DomainEvent\Publisher as AWSPublisher;
use Cmp\Queues\Infrastructure\AWS\v20121105\DomainEvent\Subscriber as AWSSubscriber;
use Cmp\Queues\Infrastructure\AWS\v20121105\Queue\Queue;
use Psr\Log\NullLogger;

class DomainContext implements Context
{
    CONST DOMAIN_EVENT_QUEUE = 'behat-domain-event-queue-test';
    CONST DOMAIN_EVENT_EXCHANGE = 'behat-domain-event-exchange-test';

    const TASK_QUEUE = 'behat-task-queue-test';
    const TASK_EXCHANGE = 'behat-task-exchange-test';

    const VERSION = '1.0.0';

    const AWS_REGION = 'us-east-1';

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

    /**
     * @var string
     */
    protected $provider;

    protected $host;
    protected $port;
    protected $user;
    protected $password;
    protected $vHost;

    protected static $queueUrl;
    protected static $topicArn;

    public function __construct()
    {
        $this->host = getenv('RABBITMQ_HOST');
        $this->port = getenv('RABBITMQ_PORT');
        $this->user = getenv('RABBITMQ_USER');
        $this->password = getenv('RABBITMQ_PASSWORD');
        $this->vHost = getenv('RABBITMQ_VHOST');
    }

    /**
     * @BeforeSuite
     */
    public static function prepare()
    {
        $queue = Queue::create('us-east-1');
        $result = $queue->createQueueAndTopic(self::DOMAIN_EVENT_QUEUE, self::DOMAIN_EVENT_EXCHANGE);

        self::$queueUrl = $result['queueUrl'];
        self::$topicArn = $result['topicArn'];
    }

    /**
     * @Given I use :provider
     */
    public function iUseProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * @Given I send a random domain event
     */
    public function iSendARandomDomainEvent()
    {
        $this->startDomainEventConsumer();
        $this->domainEvent = new DomainEvent('behat', 'behat.test', self::VERSION, time(), array(1,2,3,4,5));
        $publisher = $this->getDomainEventPublisher();
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
        $publisher = $this->getDomainEventPublisher();
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

    protected function startDomainEventConsumer()
    {
        if (empty($this->provider)) {
            throw new \RuntimeException("You need to specify a provider");
        }

        $functionName = "Start".$this->provider."DomainEventConsumer";
        $this->$functionName();

        $this->subscriptor = new TestEventSubscriptor();
        $this->subscriber->subscribe($this->subscriptor);
        $this->subscriber->start(1);
    }

    protected function startRabbitMQDomainEventConsumer()
    {
        $bindConfig = new BindConfig();
        $bindConfig->addTopic('behat.test');
        $this->subscriber = new RabbitMQSubscriber(
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
    }

    protected function startAWSDomainEventConsumer()
    {
        $this->subscriber = new AWSSubscriber(
            self::AWS_REGION,
            self::$queueUrl,
            new NullLogger(),
            null,
            1
        );
    }

    /**
     * @return Publisher
     */
    protected function getDomainEventPublisher()
    {
        if (empty($this->provider)) {
            throw new \RuntimeException("You need to specify a provider");
        }

        $functionName = "get".$this->provider."DomainEventPublisher";
        return $this->$functionName();
    }

    /**
     * @return RabbitMQPublisher
     */
    protected function getRabbitMQDomainEventPublisher()
    {
        return new RabbitMQPublisher(
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->vHost,
            self::DOMAIN_EVENT_EXCHANGE,
            new NullLogger()
        );
    }

    /**
     * @return AWSPublisher
     */
    protected function getAWSDomainEventPublisher()
    {
        return new AWSPublisher(
            self::AWS_REGION,
            self::$topicArn,
            new NullLogger()
        );
    }
}