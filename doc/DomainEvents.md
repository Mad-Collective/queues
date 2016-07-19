# Domain Events

## Publisher

Example code to publish Domain Events:

````php

$config = [
    'host' => 'rabbit_host',
    'port' => '5672',
    'user' => 'rabbitmq-server',
    'password' => 'teamcmp',
    'exchange' => 'testExchange4',
];

// Dont use this naive logger in production, inject your application logger ;)
$logger = new \Cmp\DomainEvent\Infrastructure\Log\NaiveStdoutLogger();

$config = new Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig($config['host'], $config['port'], $config['user'], $config['password'], $config['exchange']);

$publisher = new \Cmp\DomainEvent\Application\Publisher\Publisher($config, $logger);

$domainEvent1 = new Cmp\DomainEvent\Domain\Event\DomainEvent('a origin', 'user.created.female', '1468936678.651', ['extraData1' => 'extraValue1', 'extraData2' => 'extraValue2']);
$domainEvent2 = new Cmp\DomainEvent\Domain\Event\DomainEvent('a origin', 'user.created.male', '468936678.6515', ['extraData1' => 'extraValue1', 'extraData2' => 'extraValue2']);
$domainEvent3 = new Cmp\DomainEvent\Domain\Event\DomainEvent('a origin', 'mail.sent', '1468936678.6515', ['extraData1' => 'extraValue1', 'extraData2' => 'extraValue2']);

$publisher->add($domainEvent1);
$publisher->add($domainEvent2);
$publisher->add($domainEvent3);

$publisher->publish();

````

## Subscriber

Example code to Subscribe your clases to Domain Events:

````php

// Your classes listening to Domain Events will need to implement EventSubscriptor
class TestEventSubscriptor implements \Cmp\DomainEvent\Domain\Event\EventSubscriptor
{

    public function notify(\Cmp\DomainEvent\Domain\Event\DomainEvent $event)
    {
        var_dump($event);
    }

}

$config = [
    'host' => 'rabbit_host',
    'port' => '5672',
    'user' => 'rabbitmq-server',
    'password' => 'teamcmp',
    'exchange' => 'testExchange4',
    'queue' => 'testqueues'
];

// Dont use this naive logger in production, inject your application logger ;)
$logger = new \Cmp\DomainEvent\Infrastructure\Log\NaiveStdoutLogger();

// Subscribing to every user domain event
$domainTopics = ['user.#'];

$config = new Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig($config['host'], $config['port'], $config['user'], $config['password'], $config['exchange'], $config['queue']);

$subscriber = new \Cmp\DomainEvent\Application\Subscriber\Subscriber($config, $domainTopics, $logger);

$testEventSubscriptor = new TestEventSubscriptor();

$subscriber->subscribe($testEventSubscriptor);

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