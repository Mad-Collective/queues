<?php

namespace spec\Domain\Event;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DomainEventSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('origin', 'name', microtime(true));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Domain\Event\DomainEvent');
        $this->shouldHaveType('Domain\Queue\Message');
    }

    function it_should_return_delay_0()
    {
        $this->getDelay()->shouldBe(0);
    }
}
