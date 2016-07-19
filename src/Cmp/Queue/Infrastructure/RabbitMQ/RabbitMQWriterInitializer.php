<?php

namespace Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;

interface RabbitMQWriterInitializer
{
    /**
     * @throws ConnectionException
     */
    public function initialize();


}