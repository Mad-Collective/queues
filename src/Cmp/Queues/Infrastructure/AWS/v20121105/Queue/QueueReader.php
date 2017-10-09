<?php

namespace Cmp\Queues\Infrastructure\AWS\v20121105\Queue;

use Aws\Sqs\SqsClient;
use Cmp\Queues\Domain\Queue\Exception\ReaderException;
use Cmp\Queues\Domain\Queue\QueueReader as DomainQueueReader;
use Exception;
use Psr\Log\LoggerInterface;

class QueueReader implements DomainQueueReader
{
    /**
     * @var SqsClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $queueUrl;

    /**
     * @var string
     */
    protected $queueName;

    /**
     * @var MessageHandler
     */
    protected $messageHandler;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param SqsClient       $client
     * @param string          $queueName
     * @param MessageHandler  $messageHandler
     * @param LoggerInterface $logger
     */
    public function __construct(SqsClient $client, $queueName, MessageHandler $messageHandler, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->queueName = $queueName;
        $this->logger = $logger;
        $this->messageHandler = $messageHandler;
    }

    /**
     * @param callable $callback
     * @param int      $timeout
     *
     * @throws ReaderException
     */
    public function read(callable $callback, $timeout=0)
    {
        $this->initialize();
        $this->messageHandler->setCallback($callback);

        try {
            $this->consume($timeout);
        } catch(\Exception $e) {
            throw new ReaderException("Error occurred while reading", 0, $e);
        }
    }

    /**
     * Deletes all messages from the queue
     */
    public function purge()
    {
        $this->client->purgeQueue($this->queueUrl);
    }

    /**
     * @param int $timeout
     */
    protected function consume($timeout)
    {
        $result = $this->client->receiveMessage([
            'QueueUrl' => $this->queueUrl,
            'MessageAttributeNames' => ['All'],
            'MaxNumberOfMessages' => 10,
            'WaitTimeSeconds' => $timeout,
        ]);

        $messages = isset($result['Messages']) ? $result['Messages'] : [];
        foreach ($messages as $message) {
            $this->messageHandler->handleMessage($message);
            $this->client->deleteMessage(['QueueUrl' => $this->queueUrl, 'ReceiptHandle' => $message['ReceiptHandle']]);
        }
    }

    /**
     * @throws ReaderException
     */
    protected function initialize()
    {
        try {
            $this->createQueue();
        } catch (Exception $e) {
            $this->logger->error('Error trying to create queue', ['exception' => $e]);
            throw new ReaderException('Error initializing queue reader', 0, $e);
        }
    }

    /**
     * Creates the queue in SQS, nothing will happen if the queue already exists
     */
    protected function createQueue()
    {
        if (!isset($this->queueUrl)) {
            $result = $this->client->createQueue(array('QueueName' => $this->queueName));
            $this->queueUrl = $result['QueueUrl'];
        }
    }
}
