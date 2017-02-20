<?php
namespace Domain\Event;

use Domain\Event\Exception\DomainEventException;
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
        $this->setOrigin($origin)
             ->setName($name)
             ->setOccurredOn($occurredOn)
        ;
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
     * @throws DomainEventException
     */
    protected function setOrigin($origin)
    {
        if(empty($origin)) {
            throw new DomainEventException('DomainEvent origin cannot be empty');
        }
        $this->origin = $origin;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     * @throws DomainEventException
     */
    protected function setName($name)
    {
        if(empty($name)) {
            throw new DomainEventException('DomainEvent name cannot be empty');
        }
        $this->name = $name;
        return $this;
    }

    /**
     * @param $occurredOn
     * @return $this
     * @throws DomainEventException
     */
    protected function setOccurredOn($occurredOn)
    {
        if(!is_null($occurredOn) && !preg_match('/^\d+(\.\d{1,4})?$/', $occurredOn)) { // accepts also microseconds
            throw new DomainEventException("$occurredOn is not a valid unix timestamp.");
        }
        $this->occurredOn = $occurredOn;
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