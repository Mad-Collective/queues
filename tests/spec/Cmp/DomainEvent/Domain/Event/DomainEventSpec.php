<?php

namespace spec\Cmp\DomainEvent\Domain\Event;

use PhpSpec\ObjectBehavior;

class DomainEventSpec extends ObjectBehavior
{

    private $origin = 'a origin';
    private $name = 'a name';
    private $ocurredOn = 'a ocurred on';
    private $extra = ['extraData1' => 'extraValue1', 'extraData2' => 'extraValue2'];

    public function let()
    {
        $this->beConstructedWith($this->origin, $this->name, $this->ocurredOn, $this->extra);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Domain\Event\DomainEvent');
    }

    public function it_should_be_serialized_to_JSON_correctly()
    {
        $this->jsonSerialize()->shouldBe([
            'origin' => $this->origin,
            'name' => $this->name,
            'ocurredOn' => $this->ocurredOn,
            'extra' => $this->extra
        ]);
    }

}