<?php

namespace spec\Cmp\DomainEvent\Domain\Publisher;

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
        $this->shouldHaveType('Cmp\DomainEvent\Domain\Publisher\PublisherFactory');
    }

}