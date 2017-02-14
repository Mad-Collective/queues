<?php
namespace Domain\Event;

use Cmp\Queue\Domain\Message\Message;

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
    protected $ocurredOn;

    /**
     * @var array
     */

    protected $body = array();

    public function __construct($origin, $name, $ocurredOn, array $body = [])
    {
        $this->origin = $origin;
        $this->name = $name;
        $this->ocurredOn = $ocurredOn;
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
    public function getOcurredOn()
    {
        return $this->ocurredOn;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
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
     * @param $ocurredOn
     * @return $this
     */
    public function setOcurredOn($ocurredOn)
    {
        $this->ocurredOn = $ocurredOn;
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
            'ocurredOn' => $this->ocurredOn,
            'body' => $this->body
        ];
    }
}