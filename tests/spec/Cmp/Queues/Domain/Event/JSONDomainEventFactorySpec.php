<?php

namespace spec\Cmp\Queues\Domain\Event;

use Cmp\Queues\Domain\Event\DomainEvent;
use Cmp\Queues\Domain\Event\Exception\InvalidJSONDomainEventException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JSONDomainEventFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Queues\Domain\Event\JSONDomainEventFactory');
        $this->shouldHaveType('Cmp\Queues\Domain\Event\JSONDomainEventFactory');
    }

    function it_should_convert_from_json_to_DomainEvent()
    {
        $domainEventPreFactory = new DomainEvent('origin', 'name', time(), array(1,2,3,4,5));
        $this->create(json_encode($domainEventPreFactory))->shouldBeLike($domainEventPreFactory);
    }

    function it_throws_exception_for_invalid_json()
    {
        $invalidJsonString = 'foo';
        $this->shouldThrow(InvalidJSONDomainEventException::class)->duringCreate($invalidJsonString);
    }

    function it_throws_exception_when_missing_required_keys()
    {
        $data = [
            'foo' => 'bar'
        ];

        $validJsonData = json_encode($data);

        $this->shouldThrow(InvalidJSONDomainEventException::class)->duringCreate($validJsonData);
    }
}
