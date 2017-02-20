<?php

namespace spec\Domain\Task;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TaskSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('name', array(1,2,3), 10);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Domain\Task\Task');
    }

    function it_should_throw_exception_if_empty_name()
    {
        $this->shouldThrow(
            'Domain\Task\Exception\TaskException'
        )->during('__construct', array('', array(1,2,3)));
    }

    function it_should_get_name()
    {
        $this->getName()->shouldBe('name');
    }

    function it_should_get_body()
    {
        $this->getBody()->shouldBe(array(1,2,3));
    }

    function it_should_get_delay()
    {
        $this->getDelay()->shouldBe(10);
    }
}
