<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 16:12
 */

namespace Cmp\Queues\Infrastructure\AmqpLib\v26\RabbitMQ\Queue\Config;


class QueueConfig
{
    protected $name;
    protected $passive;
    protected $durable;
    protected $exclusive;
    protected $autoDelete;
    protected $noWait;
    protected $arguments;

    public function __construct($name, $passive, $durable, $exclusive, $autoDelete, $noWait = false, $arguments = array())
    {
        $this->name = $name;
        $this->passive = $passive;
        $this->durable = $durable;
        $this->exclusive = $exclusive;
        $this->autoDelete = $autoDelete;
        $this->noWait = $noWait;
        $this->arguments = $arguments;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPassive()
    {
        return $this->passive;
    }

    public function getDurable()
    {
        return $this->durable;
    }

    public function getExclusive()
    {
        return $this->exclusive;
    }

    public function getAutoDelete()
    {
        return $this->autoDelete;
    }

    public function getNoWait()
    {
        return $this->noWait;
    }

    public function getArguments()
    {
        return $this->arguments;
    }
}