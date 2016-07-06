<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Publisher;

use PhpSpec\ObjectBehavior;

class PublisherFactorySpec extends ObjectBehavior
{

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Publisher\PublisherFactory');
    }




}