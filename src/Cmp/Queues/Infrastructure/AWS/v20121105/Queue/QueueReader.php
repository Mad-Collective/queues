<?php

namespace Cmp\Queues\Infrastructure\AWS\v20121105\Queue;

use Aws\Sns\SnsClient;
use Aws\Sqs\SqsClient;
use Cmp\Queues\Domain\Queue\Exception\ReaderException;
use Cmp\Queues\Domain\Queue\Exception\TimeoutReaderException;
use Cmp\Queues\Domain\Queue\QueueReader as DomainQueueReader;
use Exception;
use Psr\Log\LoggerInterface;

class QueueReader implements DomainQueueReader
{
    /**
     * @var SqsClient
     */
    protected $sqs;

    /**
     * @var SnsClient
     */
    protected $sns;

    /**
     * @var string
     */
    protected $queueUrl;

    /**
     * @var MessageHandler
     */
    protected $messageHandler;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param SqsClient       $sqs
     * @param SnsClient       $sns
     * @param string          $queueName
     * @param string          $topicName
     * @param MessageHandler  $messageHandler
     * @param LoggerInterface $logger
     */
    public function __construct(
        SqsClient $sqs,
        SnsClient $sns,
        $queueName,
        $topicName,
        MessageHandler $messageHandler,
        LoggerInterface $logger
    ) {
        $this->sqs = $sqs;
        $this->sns = $sns;
        $this->logger = $logger;
        $this->messageHandler = $messageHandler;

        $this->initialize($queueName, $topicName);
    }

    /**
     * @param callable $callback
     * @param int      $timeout
     *
     * @throws TimeoutReaderException
     * @throws ReaderException
     */
    public function read(callable $callback, $timeout=0)
    {
        $this->messageHandler->setCallback($callback);

        try {
            $this->consume($timeout);
        } catch(TimeoutReaderException $e) {
            throw $e;
        } catch(\Exception $e) {
            throw new ReaderException("Error occurred while reading", 0, $e);
        }
    }

    /**
     * Deletes all messages from the queue
     */
    public function purge()
    {
        $this->sqs->purgeQueue($this->queueUrl);
    }

    /**
     * @param int $timeout
     *
     * @throws TimeoutReaderException
     */
    protected function consume($timeout)
    {
        $result = $this->sqs->receiveMessage([
            'QueueUrl' => $this->queueUrl,
            'MessageAttributeNames' => ['All'],
            'MaxNumberOfMessages' => 10,
            'WaitTimeSeconds' => $timeout,
        ]);

        if ($timeout != 0 && !isset($result['Messages'])) {
            throw new TimeoutReaderException();
        }

        $messages = isset($result['Messages']) ? $result['Messages'] : [];
        foreach ($messages as $message) {
            $this->messageHandler->handleMessage($message);
            $this->sqs->deleteMessage(['QueueUrl' => $this->queueUrl, 'ReceiptHandle' => $message['ReceiptHandle']]);
        }
    }

    /**
     * @param string $queueName
     * @param string $topicName
     *
     * @throws ReaderException
     */
    protected function initialize($queueName, $topicName)
    {
        try {
            $this->createQueue($queueName);
            $this->bindToSNS($topicName);
        } catch (Exception $e) {
            $this->logger->error('Error trying to create queue', ['exception' => $e]);
            throw new ReaderException('Error initializing queue reader', 0, $e);
        }
    }

    /**
     * Creates the queue in SQS, nothing will happen if the queue already exists
     *
     * @param string $queueName
     */
    protected function createQueue($queueName)
    {
        $result = $this->sqs->createQueue(array('QueueName' => $queueName));
        $this->queueUrl = $result['QueueUrl'];
    }

    /**
     * @param string $topicName
     */
    protected function bindToSNS($topicName)
    {
        $result = $this->sns->createTopic(['Name' => $topicName]);
        $topicArn = $result->get('TopicArn');

        $queueArn = $this->sqs->getQueueArn($this->queueUrl);
        $this->sns->subscribe([
            'TopicArn' => $topicArn,
            'Protocol' => 'sqs',
            'Endpoint' => $queueArn,
        ]);

        $this->sqs->setQueueAttributes([
            'QueueUrl' => $this->queueUrl,
            'Attributes' => [
                'Policy' => [
                    'Version' => '2012-10-17',
                    'Statement'  => [
                        'Effect' => 'Allow',
                        'Principal' => '*',
                        'Action' => 'sqs:SendMessage',
                        'Resource' => $queueArn,
                        'Condition' => [
                                'ArnEquals' => [
                                    'aws:SourceArn' => $topicArn,
                           ],
                        ],
                    ],
                ],
            ]
        ]);
    }
}
