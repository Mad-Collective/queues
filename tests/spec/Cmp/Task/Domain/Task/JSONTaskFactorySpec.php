<?php

namespace spec\Cmp\Task\Domain\Task;

use Cmp\Task\Domain\Task\Task;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JSONTaskFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Task\Domain\Task\JSONTaskFactory');
    }

    public function it_should_return_a_valid_task()
    {
        $task = new Task('an id', array('body key' => 'a body'));
        $json = '{"id":"an id","body":{"body key":"a body"}}';
        $this->create($json)->shouldBeLike($task);
    }

    public function it_should_throw_exception_if_invalid_task()
    {
        $json = '{"unrelated":"json"}';
        $this->shouldThrow('Cmp\Task\Domain\Task\Exception\InvalidJSONTaskException')->during('create', array($json));
    }
}
