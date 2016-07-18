<?php

namespace Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;

interface RabbitMQInitializer
{
    /**
     * @throws ConnectionException
     */
    public function initialize();


}