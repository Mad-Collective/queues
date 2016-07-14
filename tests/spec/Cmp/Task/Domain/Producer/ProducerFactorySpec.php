<?php

namespace spec\Cmp\Task\Domain\Producer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class ProducerFactorySpec extends ObjectBehavior
{

    public function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Task\Domain\Producer\ProducerFactory');
    }
}
