<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ;

use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;

class RabbitMQSubscriberFactorySpec extends ObjectBehavior
{

    public function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Subscriber\RabbitMQ\RabbitMQSubscriberFactory');
    }

}