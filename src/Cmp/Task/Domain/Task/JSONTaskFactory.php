<?php

namespace Cmp\Task\Domain\Task;

use Cmp\Queue\Domain\JSONDomainObjectFactory;

class JSONTaskFactory implements JSONDomainObjectFactory
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