<?php

namespace Cmp\Task\Domain\Task;


use Cmp\Queue\Domain\Message;

// @TODO: Rethink this object
class Task implements Message
{

    private $id;
    private $request;

    public function __construct($id, $request)
    {
        $this->id = $id;
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'request' => $this->request
        ];
    }
}