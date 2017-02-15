<?php
namespace Domain\Event;

use Domain\Queue\Message;

class DomainEvent implements Message
{
    /**
     * @var string
     */
    protected $origin;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $occurredOn;

    /**
     * @var array
     */
    protected $body = array();

    /**
     * DomainEvent constructor.
     * @param $origin
     * @param $name
     * @param $occurredOn
     * @param array $body
     */
    public function __construct($origin, $name, $occurredOn, array $body = [])
    {
        $this->origin = $origin;
        $this->name = $name;
        $this->occurredOn = $occurredOn;
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Timestamp
     *
     * @return int
     */
    public function getOccurredOn()
    {
        return $this->occurredOn;
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
        return 0;
    }

    /**
     * @param $origin
     * @return $this
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Timestamp
     *
     * @param $occurredOn
     * @return $this
     */
    public function setOccurredOn($occurredOn)
    {
        $this->occurredOn = $occurredOn;
        return $this;
    }

    /**
     * @param array $body
     * @return $this
     */
    public function setBody(array $body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'origin' => $this->origin,
            'name' => $this->name,
            'occurredOn' => $this->occurredOn,
            'body' => $this->body
        ];
    }
}