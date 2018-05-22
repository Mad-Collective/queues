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
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    protected $isDeprecated = false;

    /**
     * @var string|null
     */
    protected $correlationId;

    /**
     * @param string      $origin
     * @param string      $name
     * @param string      $version
     * @param int         $occurredOn
     * @param array       $body
     * @param string      $id
     * @param bool        $isDeprecated
     * @param string|null $correlationId
     */
    public function __construct(
        $origin,
        $name,
        $version,
        $occurredOn,
        array $body = [],
        $id = null,
        $isDeprecated = false,
        $correlationId = null
    ) {
        $this->setOrigin($origin)
            ->setName($name)
            ->setVersion($version)
            ->setOccurredOn($occurredOn);

        $this->body          = $body;
        $this->id            = $id;
        $this->isDeprecated  = $isDeprecated;
        $this->correlationId = $correlationId;
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
     * @return string
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isDeprecated()
    {
        return $this->isDeprecated;
    }

    /**
     * @return string|null
     */
    public function getCorrelationID()
    {
        return $this->correlationId;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getBodyValue($key, $default = null)
    {
        if (!array_key_exists($key, $this->body)) {
            return $default;
        }

        return $this->body[$key];
    }

    /**
     * @param string $key
     *
     * @return mixed
     *
     * @throws DomainEventException
     */
    public function getBodyValueOrFail($key)
    {
        if (!array_key_exists($key, $this->body)) {
            throw new DomainEventException("No value in the body found for Key: $key");
        }

        return $this->body[$key];
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
            'origin'        => $this->origin,
            'name'          => $this->name,
            'version'       => $this->version,
            'occurredOn'    => $this->occurredOn,
            'body'          => $this->body,
            'id'            => $this->id,
            'isDeprecated'  => $this->isDeprecated,
            'correlationId' => $this->correlationId,
        ];
    }
}