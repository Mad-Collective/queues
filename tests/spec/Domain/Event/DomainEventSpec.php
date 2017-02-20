<?php

namespace spec\Domain\Event;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DomainEventSpec extends ObjectBehavior
{
    protected $time;

    function let()
    {
        $this->time = microtime(true);
        $this->beConstructedWith('origin', 'name', $this->time, array(1,2,3));
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

    function it_should_throw_exception_if_empty_origin()
    {
        $this->shouldThrow(
            'Domain\Event\Exception\DomainEventException'
        )->during('__construct', array('', 'name', microtime(true)));
    }

    function it_should_throw_exception_if_empty_name()
    {
        $this->shouldThrow(
            'Domain\Event\Exception\DomainEventException'
        )->during('__construct', array('origin', '', microtime(true)));
    }

    function it_should_throw_exception_if_invalid_occurredOn()
    {
        $this->shouldThrow(
            'Domain\Event\Exception\DomainEventException'
        )->during('__construct', array('origin', '', 'occurredOn'));
    }

    function it_should_get_origin()
    {
        $this->getOrigin()->shouldBe('origin');
    }

    function it_should_get_name()
    {
        $this->getName()->shouldBe('name');
    }

    function it_should_get_occurredOn()
    {
        $this->getOccurredOn()->shouldBe($this->time);
    }

    function it_should_get_body()
    {
        $this->getBody()->shouldBe(array(1,2,3));
    }
}
