<?php

namespace Cmp\DomainEvent\Domain\Event;

abstract class AbstractEvent
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
}