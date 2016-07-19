<?php

namespace spec\Cmp\DomainEvent\Domain\Event;

use Cmp\DomainEvent\Domain\Event\DomainEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JSONDomainEventFactorySpec extends ObjectBehavior
{

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Domain\Event\JSONDomainEventFactory');
    }

    public function it_should_return_a_valid_domainevent()
    {
        $domainEvent = new DomainEvent('a origin', 'a name', 'a ocurred on', ['extraData1' => 'extraValue1', 'extraData2' => 'extraValue2']);
        $json = '{"origin":"a origin","name":"a name","ocurredOn":"a ocurred on","extra":{"extraData1":"extraValue1","extraData2":"extraValue2"}}';
        $this->create($json)->shouldBeLike($domainEvent);
    }

    public function it_should_throw_exception_if_invalid_domainevent()
    {
        $json = '{"unrelated":"json"}';
        $this->shouldThrow('Cmp\DomainEvent\Domain\Event\Exception\InvalidJSONDomainEventException')->during('create', array($json));
    }

}