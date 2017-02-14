<?php
/**
 * Created by PhpStorm.
 * User: quimmanrique
 * Date: 13/02/17
 * Time: 16:23
 */

namespace Infrastructure\AmqpLib\v26;

class ConnectionConfig
{
    protected $host;
    protected $port;
    protected $user;
    protected $password;
    protected $vHost;

    public function __construct($host, $port, $user, $password, $vHost='/')
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->vHost = $vHost;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getVHost()
    {
        return $this->vHost;
    }
}