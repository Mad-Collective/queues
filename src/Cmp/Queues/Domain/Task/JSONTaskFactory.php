<?php

namespace Cmp\Queues\Domain\Task;

use Cmp\Queues\Domain\Queue\JSONMessageFactory;
use Cmp\Queues\Domain\Task\Exception\InvalidJSONTaskException;

class JSONTaskFactory implements JSONMessageFactory
{

    public function create($json)
    {
        try {
            $taskArray = json_decode($json, true);
            return new Task($taskArray['name'], $taskArray['body'], $taskArray['delay']);
        } catch (\Exception $e) {
            throw new InvalidJSONTaskException();
        }
    }

}