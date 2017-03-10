<?php

namespace Cmp\Queues\Domain\Task;

use Cmp\Queues\Domain\Queue\JSONMessageFactory;
use Cmp\Queues\Domain\Task\Exception\InvalidJSONTaskException;
use Cmp\Queues\Domain\Task\Exception\TaskException;

class JSONTaskFactory implements JSONMessageFactory
{
    /**
     * @param $json
     *
     * @return Task
     * @throws InvalidJSONTaskException
     * @throws TaskException
     */
    public function create($json)
    {
        $taskArray = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJSONTaskException("String is not valid JSON");
        }

        if (!isset($taskArray['name'], $taskArray['body'])) {
            throw new InvalidJSONTaskException("Cannot reconstruct task. Name or body fields are missing");
        }

       return new Task($taskArray['name'], $taskArray['body'], isset($taskArray['delay']) ? $taskArray['delay'] : 0);
    }
}