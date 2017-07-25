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
        $this->shouldHaveType('Cmp\Queues\Domain\Queue\JSONMessageFactory');
    }

    function it_should_convert_from_json_to_domain_event()
    {
        $taskPreFactory = new DomainEvent('origin', 'name', '1.0.0', time(), array(1,2,3,4,5));
        $this->create(json_encode($taskPreFactory))->shouldBeLike($taskPreFactory);
    }

    function it_throws_exception_for_invalid_json()
    {
        $invalidJsonString = 'fsadfgkajghksdghdg';
        $this->shouldThrow(InvalidJSONDomainEventException::class)->duringCreate($invalidJsonString);
    }

    function it_throws_exception_when_missing_required_keys()
    {
        $decodedJsonData = [
            'foo' => 'bar'
        ];

        $jsonStr = json_encode($decodedJsonData);
        $this->shouldThrow(InvalidJSONDomainEventException::class)->duringCreate($jsonStr);
    }
}
