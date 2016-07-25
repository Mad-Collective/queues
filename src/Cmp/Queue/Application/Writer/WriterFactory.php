<?php

namespace Cmp\Queue\Application\Writer;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriterFactory;
use Psr\Log\LoggerInterface;

class WriterFactory
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param RabbitMQConfig $config
     *
     * @return \Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQWriter
     */
    public function create(RabbitMQConfig $config)
    {
        $rabbitMQWriterFactory = new RabbitMQWriterFactory($this->logger);
        return $rabbitMQWriterFactory->create($config);
    }

}