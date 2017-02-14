<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 16:14
 */

namespace Infrastructure\AmqpLib\v26;

class ConsumeConfig
{
    protected $noLocal;
    protected $noAck;
    protected $exclusive;
    protected $noWait;

    public function __construct($noLocal, $noAck, $exclusive, $noWait)
    {
        $this->noLocal = $noLocal;
        $this->noAck = $noAck;
        $this->exclusive = $exclusive;
        $this->noWait = $noWait;
    }

    public function getNoLocal()
    {
        return $this->noLocal;
    }

    public function getNoAck()
    {
        return $this->noAck;
    }

    public function getExclusive()
    {
        return $this->exclusive;
    }

    public function getNoWait()
    {
        return $this->noWait;
    }
}