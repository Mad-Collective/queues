<?php

namespace Cmp\Queues\Infrastructure\AWS\v20121105\Queue;

use Aws\Sns\SnsClient;
use Cmp\Queues\Domain\Queue\Exception\WriterException;
use Cmp\Queues\Domain\Queue\Message;
use Cmp\Queues\Domain\Queue\QueueWriter as DomainQueueWriter;
use Psr\Log\LoggerInterface;

class QueueWriter implements DomainQueueWriter
{
    /**
     * @var SnsClient
     */
    protected $client;

    /**
     * @var string
     */
    private $topicArn;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param SnsClient       $client
     * @param string          $topicName
     * @param LoggerInterface $logger
     */
    public function __construct(SnsClient $client, $topicName, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;

        $this->initialize($topicName);
    }

    /**
     * @param Message[] $messages
     *
     * @throws WriterException
     * @return null
     */
    public function write(array $messages)
    {
        foreach ($messages as $message) {
            $this->send($message);
        }
    }

    /**
     * @param Message $message
     *
     * @throws WriterException
     */
    protected function send(Message $message)
    {
        try {
            $this->client->publish([
                'TopicArn' => $this->topicArn,
                'Message' => json_encode($message),
            ]);
        } catch(\Exception $e) {
            $this->logger->error('Error writing messages', ['exception' => $e]);
            throw new WriterException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $name
     *
     * @throws WriterException
     */
    protected function initialize($name)
    {
        try {
            $result = $this->client->createTopic(['Name' => $name]);
            $this->topicArn = $result->get('TopicArn');
        } catch (\Exception $e) {
            $this->logger->error('Error trying to create an SNS topic', ['exception' => $e]);
            throw new WriterException($e->getMessage(), $e->getCode());
        }
    }
}