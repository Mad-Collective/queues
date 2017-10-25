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
     * @var bool
     */
    private $raw;

    /**
     * @param JSONMessageFactory $jsonMessageFactory
     * @param bool               $raw
     */
    public function __construct(JSONMessageFactory $jsonMessageFactory, $raw = false)
    {
        $this->jsonMessageFactory = $jsonMessageFactory;
        $this->raw                = $raw;
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

        if ($this->raw) {
            $json = $message['Body'];
        } else {
            $body = json_decode($message['Body'], true);
            $json = $body['Message'];
        }

        $task = $this->jsonMessageFactory->create($json);
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