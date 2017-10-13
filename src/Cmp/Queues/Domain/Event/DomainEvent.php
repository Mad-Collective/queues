<?php
namespace Cmp\Queues\Domain\Event;

use Cmp\Queues\Domain\Event\Exception\DomainEventException;
use Cmp\Queues\Domain\Queue\Message;

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
     * @var string
     */
    protected $version;

    /**
     * @var int
     */
    protected $occurredOn;

    /**
     * @var array
     */
    protected $body = array();

    /**
     * @var bool
     */
    protected $isDeprecated = false;

    /**
     * @param string $origin
     * @param string $name
     * @param string $version
     * @param int    $occurredOn
     * @param array  $body
     * @param bool   $isDeprecated
     */
    public function __construct($origin, $name, $version, $occurredOn, array $body = [], $isDeprecated = false)
    {
        $this->setOrigin($origin)
             ->setName($name)
             ->setVersion($version)
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
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
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
     * @return bool
     */
    public function isDeprecated()
    {
        return $this->isDeprecated;
    }

    /**
     * @param string $origin
     * @return DomainEvent $this
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
     * @param string $name
     * @return DomainEvent $this
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
     * @param string $version
     * @return DomainEvent $this
     * @throws DomainEventException
     */
    protected function setVersion($version)
    {
        if(empty($version)) {
            throw new DomainEventException('DomainEvent version cannot be empty');
        }
        $this->version = $version;
        return $this;
    }

    /**
     * @param int $occurredOn
     * @return DomainEvent $this
     * @throws DomainEventException
     */
    protected function setOccurredOn($occurredOn)
    {
        if(!is_null($occurredOn) && !preg_match('/^\d+(\.\d{1,4})?$/', $occurredOn)) { // accepts also microseconds
            throw new DomainEventException("$occurredOn is not a valid unix timestamp.");
        }

        if ($occurredOn > time()) {
            throw new DomainEventException('OccuredOn cannot be located in the future');
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
            'origin'       => $this->origin,
            'name'         => $this->name,
            'version'      => $this->version,
            'occurredOn'   => $this->occurredOn,
            'body'         => $this->body,
            'isDeprecated' => $this->isDeprecated,
        ];
    }
}