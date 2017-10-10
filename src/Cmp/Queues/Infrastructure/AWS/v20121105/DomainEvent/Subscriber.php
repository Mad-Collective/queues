<?php

namespace Cmp\Queues\Infrastructure\AWS\v20121105\DomainEvent;

use Aws\Sns\SnsClient;
use Aws\Sqs\SqsClient;
use Cmp\Queues\Domain\Event\JSONDomainEventFactory;
use Cmp\Queues\Domain\Event\Subscriber as DomainSubscriber;
use Cmp\Queues\Infrastructure\AWS\v20121105\Queue\MessageHandler;
use Cmp\Queues\Infrastructure\AWS\v20121105\Queue\QueueReader;
use Psr\Log\LoggerInterface;

class Subscriber extends DomainSubscriber
{
    /**
     * @param string                 $region
     * @param string                 $queueName
     * @param string                 $topicName
     * @param LoggerInterface        $logger
     * @param JSONDomainEventFactory $factory
     */
    public function __construct($region, $queueName, $topicName, LoggerInterface $logger, JSONDomainEventFactory $factory = null)
    {
        $queueReader = new QueueReader(
            SqsClient::factory([
                'region'  => $region,
                'version' => '2012-11-05',
            ]),
            SnsClient::factory([
                'region'  => $region,
                'version' => '2010-03-31',
            ]),
            $queueName,
            $topicName,
            new MessageHandler($factory ?: new JSONDomainEventFactory()),
            $logger
        );
        parent::__construct($queueReader, $logger);
    }
}
