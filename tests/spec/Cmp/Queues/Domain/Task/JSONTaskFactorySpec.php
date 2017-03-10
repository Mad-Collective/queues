<?php

namespace spec\Cmp\Queues\Domain\Task;

use Cmp\Queues\Domain\Task\Exception\InvalidJSONTaskException;
use Cmp\Queues\Domain\Task\Task;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JSONTaskFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Queues\Domain\Task\JSONTaskFactory');
        $this->shouldHaveType('Cmp\Queues\Domain\Queue\JSONMessageFactory');
    }

    function it_should_convert_from_json_to_Task()
    {
        $taskPreFactory = new Task('name', array(1,2,3), 10);
        $this->create(json_encode($taskPreFactory))->shouldBeLike($taskPreFactory);
    }

    function it_throws_exception_for_invalid_json()
    {
        $invalidJsonString = 'fsadfgkajghksdghdg';
        $this->shouldThrow(InvalidJSONTaskException::class)->duringCreate($invalidJsonString);
    }

    function it_throws_exception_when_missing_required_keys()
    {
        $decodedJsonData = [
            'foo' => 'bar'
        ];

        $jsonStr = json_encode($decodedJsonData);
        $this->shouldThrow(InvalidJSONTaskException::class)->duringCreate($jsonStr);
    }
}
