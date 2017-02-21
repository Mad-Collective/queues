<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 16:12
 */

namespace Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config;


class ExchangeConfig
{
    protected $name;
    protected $type;
    protected $passive;
    protected $durable;
    protected $autoDelete;

    public function __construct($name, $type, $passive, $durable, $autoDelete)
    {
        $this->name = $name;
        $this->type = $type;
        $this->passive = $passive;
        $this->durable = $durable;
        $this->autoDelete = $autoDelete;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getPassive()
    {
        return $this->passive;
    }

    public function getDurable()
    {
        return $this->durable;
    }

    public function getAutoDelete()
    {
        return $this->autoDelete;
    }

}