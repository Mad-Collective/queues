<?php

namespace Cmp\Queues\Infrastructure\AWS\v20121105\Queue;

use Aws\Sqs\SqsClient;
use Cmp\Queues\Domain\Queue\Exception\GracefulStopException;
use Cmp\Queues\Domain\Queue\Exception\ReaderException;
use Cmp\Queues\Domain\Queue\Exception\TimeoutReaderException;
use Cmp\Queues\Domain\Queue\QueueReader as DomainQueueReader;
use Psr\Log\LoggerInterface;

class QueueReader implements DomainQueueReader
{
    /**
     * @var SqsClient
     */
    protected $sqs;

    /**
     * @var string
     */
    protected $queueUrl;

    /**
     * @var int
     */
    protected $messagesToRead;

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
     * @param string          $queueUrl
     * @param int             $messagesToRead
     * @param MessageHandler  $messageHandler
     * @param LoggerInterface $logger
     */
    public function __construct(
        SqsClient $sqs,
        $queueUrl,
        $messagesToRead,
        MessageHandler $messageHandler,
        LoggerInterface $logger
    ) {
        $this->sqs = $sqs;
        $this->queueUrl = $queueUrl;
        $this->messagesToRead = $messagesToRead;
        $this->logger = $logger;
        $this->messageHandler = $messageHandler;
    }

    /**
     * @param callable $callback
     * @param int      $timeout
     *
     * @throws GracefulStopException
     * @throws TimeoutReaderException
     * @throws ReaderException
     */
    public function read(callable $callback, $timeout=0)
    {
        $this->messageHandler->setCallback($callback);

        try {
            $this->consume($timeout);
        } catch(GracefulStopException $e) {
            $this->logger->info("Gracefully stopping the AWS queue reader", ["exception" => $e]);
            throw $e;
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
        $this->sqs->purgeQueue([
            'QueueUrl' => $this->queueUrl,
        ]);
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
            'MaxNumberOfMessages' => $this->messagesToRead,
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
}
