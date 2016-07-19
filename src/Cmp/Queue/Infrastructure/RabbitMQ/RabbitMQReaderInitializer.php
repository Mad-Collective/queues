<?php

namespace Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;

interface RabbitMQReaderInitializer
{

    /**
     * @throws ConnectionException
     */
    public function initialize(callable $callback);

}