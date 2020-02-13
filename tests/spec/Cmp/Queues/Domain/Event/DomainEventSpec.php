<?php

namespace spec\Cmp\Queues\Domain\Event;

use Cmp\Queues\Domain\Event\Exception\DomainEventException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DomainEventSpec extends ObjectBehavior
{
    protected $time;

    function let()
    {
        $this->time = microtime(true)-10;
        $this->beConstructedWith(
            'origin',
            'name',
            '1.0.0',
            $this->time,
            array("foo" => "bar", "empty" => null),
            'uuid',
            true,
            'correlation'
  //          ['attribute1', 'attribute2']
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Queues\Domain\Event\DomainEvent');
        $this->shouldHaveType('Cmp\Queues\Domain\Queue\Message');
    }

    function it_should_return_delay_0()
    {
        $this->getDelay()->shouldBe(0);
    }

    function it_should_throw_exception_if_empty_origin()
    {
        $this->shouldThrow(
            'Cmp\Queues\Domain\Event\Exception\DomainEventException'
        )->during('__construct', array('', 'name', '1.0.0', microtime(true)));
    }

    function it_should_throw_exception_if_empty_name()
    {
        $this->shouldThrow(
            'Cmp\Queues\Domain\Event\Exception\DomainEventException'
        )->during('__construct', array('origin', '', '1.0.0', microtime(true)));
    }

    function it_should_throw_exception_if_empty_version()
    {
        $this->shouldThrow(
            'Cmp\Queues\Domain\Event\Exception\DomainEventException'
        )->during('__construct', array('origin', 'name', '', microtime(true)));
    }

    function it_should_throw_exception_if_invalid_occurredOn()
    {
        $this->shouldThrow(
            'Cmp\Queues\Domain\Event\Exception\DomainEventException'
        )->during('__construct', array('origin', 'name', '1.0.0', 'occurredOn'));
    }

    function it_should_throw_exception_if_occurredOn_in_future()
    {
        $this->shouldThrow(
            'Cmp\Queues\Domain\Event\Exception\DomainEventException'
        )->during('__construct', array('origin', 'name', '1.0.0', time() + 8 * 24 * 60 * 60));
    }

    function it_should_get_origin()
    {
        $this->getOrigin()->shouldBe('origin');
    }

    function it_should_get_name()
    {
        $this->getName()->shouldBe('name');
    }

    function it_should_get_version()
    {
        $this->getVersion()->shouldBe('1.0.0');
    }

    function it_should_get_occurredOn()
    {
        $this->getOccurredOn()->shouldBe($this->time);
    }

    function it_should_get_body()
    {
        $this->getBody()->shouldBe(array("foo" => "bar", "empty" => null));
    }

    function it_should_get_the_id()
    {
        $this->getID()->shouldBe('uuid');
    }

    function it_should_get_the_deprecated_flag()
    {
        $this->isDeprecated()->shouldBe(true);
    }

    function it_should_have_the_correlation_id()
    {
        $this->getCorrelationId()->shouldBe("correlation");
    }

/*    function it_should_have_the_extra_attributes()
    {
        $this->getExtraAttributes()->shouldBe(['attribute1', 'attribute2']);
    }*/

    function it_can_get_body_values()
    {
        $this->getBodyValue("foo")->shouldBe("bar");
        $this->getBodyValue("nope", "default")->shouldBe("default");
        $this->getBodyValue("empty", "default")->shouldBe(null);
    }

    function it_can_get_body_values_or_fail()
    {
        $this->shouldThrow(DomainEventException::class)->duringGetBodyValueOrFail("nope");
    }
}
