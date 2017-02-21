<?php

namespace spec\Cmp\Queues\Domain\Task;

use Cmp\Queues\Domain\Queue\QueueWriter;
use Cmp\Queues\Domain\Task\Task;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProducerSpec extends ObjectBehavior
{
    function let(
        QueueWriter $queueWriter
    )
    {
        $this->beConstructedWith($queueWriter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Queues\Domain\Task\Producer');
    }

    function it_adds_tasks(
        Task $task1,
        Task $task2
    )
    {
        $this->add($task1)
             ->add($task2)
        ;
        $this->getTasks()->shouldBe(array($task1, $task2));
    }

    function it_writes_to_queue(
        QueueWriter $queueWriter,
        Task $task
    )
    {
        $this->add($task);
        $queueWriter->write(array($task))->shouldBeCalled();
        $this->produce();
    }

    function it_should_not_write_to_queue_if_no_Task_added()
    {
        $this->shouldThrow('Cmp\Queues\Domain\Task\Exception\TaskException')->duringProduce();
    }
}
