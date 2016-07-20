<?php

namespace Cmp\Task\Domain\Task;

use Cmp\Queue\Domain\Message\Message;

class Task implements Message
{

    private $id;
    private $body;

    public function __construct($id, array $body)
    {
        $this->id = $id;
        $this->body = $body;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->id;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'body' => $this->body
        ];
    }
}