# Domain Events

## Publisher

Example code to publish Domain Events:

````php

use Domain\Event\DomainEvent;
use Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent\Publisher;
use Infrastructure\Logger\NaiveStdoutLogger;

// Replace for your app logger!!
$logger = new NaiveStdoutLogger();

$publisher = new Publisher(
    '127.0.0.1', //host
    5672, //port
    'guest', //username
    'guest', //password
    '/', //vhost
    'domain-events', //queue
    $logger
);

$publisher->add(new DomainEvent('queues.helper', 'test', microtime(true), array(1,2,3,4,5)));
$publisher->publish();

````

## Subscriber

Example code to Subscribe your clases to Domain Events:

````php

use Domain\Event\DomainEvent;
use Domain\Event\EventSubscriptor;
use Infrastructure\AmqpLib\v26\RabbitMQ\DomainEvent\Subscriber;
use Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config\BindConfig;
use Infrastructure\Logger\NaiveStdoutLogger;

// Subscriptor clases need to implement EventSubscriptor
class TestEventSubscriptor implements EventSubscriptor
{
    public function isSubscribed(DomainEvent $domainEvent)
    {
        return true;
    }

    public function notify(DomainEvent $domainEvent)
    {
        var_dump($domainEvent);
    }
}

// Replace for your app logger!!
$logger = new NaiveStdoutLogger();

// Bind topics you want to listen
$bindConfig = new BindConfig();
$bindConfig->addTopic('test');

$subscriber = new Subscriber(
    '127.0.0.1', //host
    5672, //port
    'guest', //username
    'guest', //password
    '/', //vhost
    'domain-events', //exchange
    'application_queue_name', //queue name - specific to each application!!!
    $bindConfig,
    $logger
);

$subscriber->subscribe(new TestEventSubscriptor());
$subscriber->start();

````

### Domain Topics

Domain Events are named in a namespaced fashion, ex: "user.created.free".

You can subscribe to domain objects using domain topics with the wildcards: # and *

The wildcard # will match any number of namespace sub levels. While the wildcard * will only match one namespace sub level.

So for example, if we have the domain objects:

- "user.created.free"
- "user.created.paid"
- "user.login"

The domain topic: user.# will match the 3 domain objects.

But the domain topic: user.* will only match user.login.

Also you can subscribe to the full domain object name: "user.created.free" will obviously match "user.created.free".