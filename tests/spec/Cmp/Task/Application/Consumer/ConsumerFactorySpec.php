<?php

namespace spec\Cmp\Task\Application\Consumer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class ConsumerFactorySpec extends ObjectBehavior
{

    public function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Task\Application\Consumer\ConsumerFactory');
    }
}
