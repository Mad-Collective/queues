# queues

This is the Queues Abstraction Library. For the moment it will allow you to publish and subscribe to Domain Events.

In the future it will allow you to handle Task Queues also.

## Installation

Add this repo to your composer.json 

````json
"repositories": {
  "cmp/queues": {
    "type": "vcs",
    "url": "git@github.com:CMProductions/queues.git"
  }
}
````

Then require it as usual:

``` bash
composer require "cmp/queues"
```

## Domain Events



### Publisher

Example code:

````php

$config = [
    'host' => 'rabbitmq_host',
    'port' => '5672',
    'user' => 'rabbitmq_user',
    'password' => 'rabbitmq_password',
    'exchange' => 'rabbitmq_exchange',
];

$logger = new \Cmp\DomainEvent\Application\Log\NullLogger();

$publisherFactory = new \Cmp\DomainEvent\Domain\Publisher\PublisherFactory($logger);
$publisher = $publisherFactory->create($config);

$domainEvent = new \Cmp\DomainEvent\Domain\Event\DomainEvent('origin', 'wh.email.send', '1467905896', ['extraData1' => 'extraValue1', 'extraData2' => 'extraValue2']);

$publisher->publish($domainEvent);

````

### Subscriber

Example code:

````php

class TestEventSubscriptor implements \Cmp\DomainEvent\Application\EventSubscriptor
{

    public function notify(\Cmp\DomainEvent\Domain\Event\DomainEvent $event)
    {
        var_dump($event);
    }

    public function isSubscribed(\Cmp\DomainEvent\Domain\Event\DomainEvent $event)
    {
        return true;
    }
}

$logger = new \Cmp\DomainEvent\Application\Log\NullLogger();

$config = [
    'host' => 'rabbitmq_host',
    'port' => '5672',
    'user' => 'rabbitmq_user',
    'password' => 'rabbitmq_password',
    'exchange' => 'rabbitmq_exchange',
];

$domainTopics = ['wh.email.#', 'wh.user.#'];

$subscriberFactory = new \Cmp\DomainEvent\Domain\Subscriber\SubscriberFactory($logger);
$subscriber = $subscriberFactory->create($config, $domainTopics);

$testEventSubscriptor = new TestEventSubscriptor();

$subscriber->subscribe($testEventSubscriptor);

$subscriber->start();

````
