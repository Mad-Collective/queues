<?php

namespace Domain\Task;

use Domain\Queue\Message;

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
        $this->name = $name;
        $this->body = $body;
        $this->delay = $delay;
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