<?php

namespace Cmp\Task\Domain\Task;

// @TODO: Rethink this object
class Task implements \JsonSerializable
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