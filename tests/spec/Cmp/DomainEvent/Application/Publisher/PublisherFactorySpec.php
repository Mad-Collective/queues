<?php

namespace spec\Cmp\DomainEvent\Application\Publisher;

use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;

class PublisherFactorySpec extends ObjectBehavior
{

    public function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Application\Publisher\PublisherFactory');
    }

}