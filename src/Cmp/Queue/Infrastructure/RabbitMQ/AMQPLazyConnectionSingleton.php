<?php

namespace Cmp\Queue\Infrastructure\RabbitMQ;

use PhpAmqpLib\Connection\AMQPLazyConnection;

class AMQPLazyConnectionSingleton
{

    private static $amqpLazyConnection;

    public static function getInstance($host, $port, $user, $password)
    {
        if(null === static::$amqpLazyConnection) {
            static::$amqpLazyConnection = new AMQPLazyConnection($host, $port, $user, $password);
        }

        return static::$amqpLazyConnection;
    }

    protected function _construct()
    {
    }
}