<?php

namespace spec\Cmp\DomainEvent\Application\Subscriber;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;

class SubscriberSpec extends ObjectBehavior
{

    public function let(RabbitMQConfig $config, LoggerInterface $logger)
    {
        $this->beConstructedWith($config, ['user.#'],$logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Application\Subscriber\Subscriber');
    }

}