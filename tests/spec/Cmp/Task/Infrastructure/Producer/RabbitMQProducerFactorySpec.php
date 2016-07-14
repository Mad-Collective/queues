<?php

namespace spec\Cmp\Task\Infrastructure\Producer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class RabbitMQProducerFactorySpec extends ObjectBehavior
{
    public function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Task\Infrastructure\Producer\RabbitMQProducerFactory');
    }
}
