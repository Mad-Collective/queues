<?php

namespace Domain\Task;

use Domain\Queue\JSONMessageFactory;
use Domain\Task\Exception\InvalidJSONTaskException;

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