<?php

namespace Cmp\Task\Domain\Task;

use Cmp\Queue\Domain\JSONMessageFactory;
use Cmp\Task\Domain\Task\Exception\InvalidJSONTaskException;

class JSONTaskFactory implements JSONMessageFactory
{

    public function create($json)
    {
        try {
            $taskArray = json_decode($json, true);
            return new Task($taskArray['id'], $taskArray['body']);
        } catch (\Exception $e) {
            throw new InvalidJSONTaskException();
        }
    }

}