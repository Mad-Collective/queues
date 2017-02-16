<?php

namespace spec\Domain\Task;

use Domain\Task\Task;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JSONTaskFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Domain\Task\JSONTaskFactory');
    }

    function it_should_convert_from_json_to_Task()
    {
        $taskPreFactory = new Task('name', array(1,2,3), 10);
        $taskPostFactory = $this->create(json_encode($taskPreFactory));
        $taskPostFactory->getName()->shouldBe($taskPreFactory->getName());
        $taskPostFactory->getBody()->shouldBe($taskPreFactory->getBody());
        $taskPostFactory->getDelay()->shouldBe($taskPreFactory->getDelay());
    }
}
