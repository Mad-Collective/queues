<?php

namespace Cmp\DomainEvent\Domain\Publisher;

use Cmp\Queue\Domain\Message\Message;
use Cmp\Queue\Domain\Writer\AbstractWriter;
use Psr\Log\LoggerInterface;

class Publisher
{

    private $writer;

    private $logger;

    /**
     * Publisher constructor.
     *
     * @param AbstractWriter  $writer
     * @param LoggerInterface $logger
     */
    public function __construct(AbstractWriter $writer, LoggerInterface $logger)
    {
        $this->writer = $writer;
        $this->logger = $logger;
    }

    public function add(Message $message)
    {
        $this->writer->add($message);
    }

    public function publish()
    {
        $this->writer->write();
    }
}