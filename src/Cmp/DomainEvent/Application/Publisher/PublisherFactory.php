<?php

namespace Cmp\DomainEvent\Application\Publisher;

use Cmp\DomainEvent\Domain\Publisher\Publisher as DomainPublisher;
use Cmp\Queue\Application\Writer\WriterFactory;
use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use Psr\Log\LoggerInterface;

class PublisherFactory
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function create(RabbitMQConfig $config)
    {
        $queueWriterFactory = new WriterFactory($this->logger);
        $queueWriter = $queueWriterFactory->create($config);
        return new DomainPublisher($queueWriter, $this->logger);
    }

}