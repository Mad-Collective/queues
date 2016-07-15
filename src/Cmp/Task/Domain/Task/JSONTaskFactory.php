<?php

namespace Cmp\Task\Domain\Task;

class JSONTaskFactory
{

    public function create($json)
    {
        try {
            $taskArray = json_decode($json, true);
            return new Task($taskArray['id'], $taskArray['request']);
        } catch (\Exception $e) {
            throw new InvalidJSONTaskException();
        }
    }

}