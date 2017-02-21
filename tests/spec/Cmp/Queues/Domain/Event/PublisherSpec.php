<?php

namespace spec\Cmp\Queues\Domain\Event;

use Cmp\Queues\Domain\Event\DomainEvent;
use Cmp\Queues\Domain\Queue\QueueWriter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PublisherSpec extends ObjectBehavior
{
    function let(QueueWriter $queueWriter)
    {
        $this->beConstructedWith($queueWriter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Queues\Domain\Event\Publisher');
    }

    function it_should_add_DomainEvents(
        DomainEvent $domainEvent1,
        DomainEvent $domainEvent2
    )
    {
        $this->add($domainEvent1)
             ->add($domainEvent2)
        ;
        $this->getEvents()->shouldBe(array($domainEvent1, $domainEvent2));
    }

    function it_should_write_DomainEvents_to_queue(
        QueueWriter $queueWriter,
        DomainEvent $domainEvent
    )
    {
        $this->add($domainEvent);
        $queueWriter->write(array($domainEvent))->shouldBeCalled();
        $this->publish();
    }

    function it_should_not_write_to_queue_if_no_DomainEvent_added()
    {
        $this->shouldThrow('Cmp\Queues\Domain\Event\Exception\DomainEventException')->duringPublish();
    }

}
