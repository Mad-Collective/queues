<?php

namespace Cmp\DomainEvent\Domain\Event;

use Cmp\Queue\Domain\Message;

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

    protected $extra = array();

    public function __construct($origin, $name, $ocurredOn, $extra = [])
    {
        $this->origin = $origin;
        $this->name = $name;
        $this->ocurredOn = $ocurredOn;
        $this->extra = $extra;
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
    public function getExtra()
    {
        return $this->extra;
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
     * @param array $extra
     * @return $this
     */
    public function setExtra(array $extra)
    {
        $this->extra = $extra;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'origin' => $this->origin,
            'name' => $this->name,
            'ocurredOn' => $this->ocurredOn,
            'extra' => $this->extra
        ];
    }

}