<?php

namespace Cmp\Queues\Domain\Task;

use Cmp\Queues\Domain\Task\Exception\TaskException;

class Task implements TaskInterface
{
    private string $name;
    private array $body;
    private int $delay;

    /**
     * Task constructor.
     * @param string $name
     * @param array $body
     * @param int $delay
     * @throws TaskException
     */
    public function __construct(string $name, array $body, int $delay = 0)
    {
        $this->setName($name)
             ->setDelay($delay)
        ;
        $this->body = $body;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }

    /**
     * @param $name
     * @return $this
     * @throws TaskException
     */
    protected function setName($name): Task
    {
        if(empty($name)) {
            throw new TaskException('Task name cannot be empty');
        }
        $this->name = $name;
        return $this;
    }

    /**
     * @param $delay
     * @return $this
     * @throws TaskException
     */
    protected function setDelay($delay): Task
    {
        if(!is_null($delay) && !preg_match('/^\d+$/', $delay)) {
            throw new TaskException("Task delay $delay is not a valid delay.");
        }
        $this->delay = $delay;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'body' => $this->body,
            'delay' => $this->delay
        ];
    }

    public function getAttributes(): array
    {
        return [];
    }
}
