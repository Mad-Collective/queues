<?php

namespace Domain\Task;

use Domain\Queue\Message;
use Domain\Task\Exception\TaskException;

class Task implements Message
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $body;

    /**
     * @var int
     */
    private $delay;

    /**
     * Task constructor.
     * @param string $name
     * @param array $body
     * @param int $delay
     */
    public function __construct($name, array $body, $delay=0)
    {
        $this->setName($name)
             ->setDelay($delay)
        ;
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param $name
     * @return $this
     * @throws TaskException
     */
    protected function setName($name)
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
    protected function setDelay($delay)
    {
        if(!is_null($delay) && !preg_match('/^\d+$/', $delay)) {
            throw new TaskException("Task delay $delay is not a valid delay.");
        }
        $this->delay = $delay;
        return $this;
    }


    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'body' => $this->body,
            'delay' => $this->delay
        ];
    }
}