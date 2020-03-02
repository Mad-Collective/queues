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
    protected $sns;

    /**
     * @var string
     */
    private $topicArn;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param SnsClient       $sns
     * @param string          $topicArn
     * @param LoggerInterface $logger
     */
    public function __construct(SnsClient $sns, $topicArn, LoggerInterface $logger)
    {
        $this->sns = $sns;
        $this->topicArn = $topicArn;
        $this->logger = $logger;
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
            $this->sns->publish([
                'TopicArn'          => $this->topicArn,
                'Message'           => json_encode($message),
                'MessageAttributes' => $message->getAttributes()
            ]);
        } catch(\Exception $e) {
            $this->logger->error('Error writing messages', ['exception' => $e]);
            throw new WriterException($e->getMessage(), $e->getCode());
        }
    }
}