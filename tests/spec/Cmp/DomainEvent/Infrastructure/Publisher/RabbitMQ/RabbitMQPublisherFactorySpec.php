<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ;

use PhpSpec\ObjectBehavior;

class RabbitMQPublisherFactorySpec extends ObjectBehavior
{

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\RabbitMQPublisherFactory');
    }

}