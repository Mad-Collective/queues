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
        $domainEvent = new DomainEvent('a origin', 'a name', 'a ocurred on', ['bodyData1' => 'bodyValue1', 'bodyData2' => 'bodyValue2']);
        $json = '{"origin":"a origin","name":"a name","ocurredOn":"a ocurred on","body":{"bodyData1":"bodyValue1","bodyData2":"bodyValue2"}}';
        $this->create($json)->shouldBeLike($domainEvent);
    }

    public function it_should_throw_exception_if_invalid_domainevent()
    {
        $json = '{"unrelated":"json"}';
        $this->shouldThrow('Cmp\DomainEvent\Domain\Event\Exception\InvalidJSONDomainEventException')->during('create', array($json));
    }

}