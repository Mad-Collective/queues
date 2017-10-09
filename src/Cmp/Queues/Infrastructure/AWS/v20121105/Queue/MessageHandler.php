<?php

namespace Cmp\Queues\Infrastructure\AWS\v20121105\Queue;

use Cmp\Queues\Domain\Queue\Exception\ReaderException;
use Cmp\Queues\Domain\Queue\JSONMessageFactory;

class MessageHandler
{
    /**
     * @var JSONMessageFactory
     */
    private $jsonMessageFactory;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @param JSONMessageFactory $jsonMessageFactory
     */
    public function __construct(JSONMessageFactory $jsonMessageFactory)
    {
        $this->jsonMessageFactory = $jsonMessageFactory;
    }

    /**
     * @param array $message
     *
     * @throws ReaderException
     */
    public function handleMessage(array $message)
    {
        if (!isset($this->callback)) {
            throw new ReaderException("Handling a message with no callback set");
        }

        $body = json_decode($message['Body'], true);
        $task = $this->jsonMessageFactory->create($body['Message']);
        call_user_func($this->callback, $task);
    }

    /**
     * @param callable $callback
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }
}