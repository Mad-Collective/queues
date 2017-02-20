<?php

namespace spec\Domain\Event;

use Domain\Event\DomainEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JSONDomainEventFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Domain\Event\JSONDomainEventFactory');
        $this->shouldHaveType('Domain\Event\JSONDomainEventFactory');
    }
    
    function it_should_convert_from_json_to_DomainEvent()
    {
        $domainEventPreFactory = new DomainEvent('origin', 'name', time(), array(1,2,3,4,5));
        $domainEventPostFactory = $this->create(json_encode($domainEventPreFactory));
        $domainEventPostFactory->getName()->shouldBe($domainEventPreFactory->getName());
        $domainEventPostFactory->getOrigin()->shouldBe($domainEventPreFactory->getOrigin());
        $domainEventPostFactory->getOccurredOn()->shouldBe($domainEventPreFactory->getOccurredOn());
        $domainEventPostFactory->getBody()->shouldBe($domainEventPreFactory->getBody());
    }
}
