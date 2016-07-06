<?php

namespace spec\Cmp\DomainEvent\Domain\Publisher;

use PhpSpec\ObjectBehavior;

class PublisherFactorySpec extends ObjectBehavior
{

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Domain\Publisher\PublisherFactory');
    }

}