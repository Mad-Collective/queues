<?php

namespace spec\Cmp\DomainEvent\Application\Subscriber;

use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;

class SubscriberFactorySpec extends ObjectBehavior
{

    public function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Application\Subscriber\SubscriberFactory');
    }

}