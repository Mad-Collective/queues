<?php

namespace spec\Domain\Event;

use Domain\Event\DomainEvent;
use Domain\Event\EventSubscriptor;
use Domain\Queue\QueueReader;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class SubscriberSpec extends ObjectBehavior
{
    function let(QueueReader $queueReader, LoggerInterface $logger)
    {
        $this->beConstructedWith($queueReader, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Domain\Event\Subscriber');
    }
    
    function it_subscribes_Subscriptors(
        EventSubscriptor $eventSubscriptor1,
        EventSubscriptor $eventSubscriptor2
    )
    {
        $this->subscribe($eventSubscriptor1)
             ->subscribe($eventSubscriptor2)
        ;
        $this->getSubscriptors()->shouldBe(array($eventSubscriptor1, $eventSubscriptor2));
    }

    function it_notifies_Subscriptor(
        EventSubscriptor $eventSubscriptor,
        DomainEvent $domainEvent
    )
    {
        $eventSubscriptor->isSubscribed($domainEvent)->willReturn(true);
        $eventSubscriptor->isSubscribed($domainEvent)->shouldBeCalled();
        $eventSubscriptor->notify($domainEvent)->shouldBeCalled();

        $this->subscribe($eventSubscriptor);
        $this->notify($domainEvent);
    }

    function it_reads_from_queue(
        QueueReader $queueReader,
        EventSubscriptor $eventSubscriptor
    )   
    {
        $queueReader->read(Argument::cetera())->shouldBeCalled();
        $this->subscribe($eventSubscriptor);
        $this->processOne(function(){}, 1);
    }
    
    function it_should_not_read_from_queue_if_no_EventSubscriptor_added()
    {
        $this->shouldThrow('Domain\Event\Exception\DomainEventException')->duringProcessOne(function(){});
    }
}
