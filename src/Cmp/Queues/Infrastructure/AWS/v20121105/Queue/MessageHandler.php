<?php

namespace Cmp\Queues\Infrastructure\AWS\v20121105\Queue;

use Cmp\Queues\Domain\Event\Exception\InvalidJSONDomainEventException;
use Cmp\Queues\Domain\Queue\Exception\InvalidJSONMessageException;
use Cmp\Queues\Domain\Queue\Exception\ReaderException;
use Cmp\Queues\Domain\Queue\JSONMessageFactory;
use Cmp\Queues\Domain\Task\Exception\ParseMessageException;
use Exception;

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
     * @return mixed
     * @throws ParseMessageException
     * @throws ReaderException
     */
    public function handleMessage(array $message)
    {
        if (!isset($this->callback)) {
            throw new ReaderException("Handling a message with no callback set");
        }

        try{

            if (!isset($message['Body'])) {
                throw new InvalidJSONMessageException('Undefined index key Body: ' . print_r($message, true));
            }

            $body = json_decode($message['Body'], true);

            if (1 === \count($body) && true === \array_key_exists(0, $body)) {
                $body = $body[0];
            }

            $messagePayload = $body;

            if (false === \array_key_exists('Message', $body)) {
                if (false === \array_key_exists('payload', $body)) {
                    throw new InvalidJSONMessageException('Undefined index key Message: ' . print_r($body, true));
                }
                $messagePayload = $body['payload'];
            }

            if (!isset($messagePayload['Message'])) {
                throw new InvalidJSONMessageException('Undefined index key Message: ' . print_r($messagePayload, true));
            }

            return call_user_func($this->callback, $this->jsonMessageFactory->create($messagePayload['Message']));

        } catch(InvalidJSONMessageException $e) {
            throw new ParseMessageException(json_encode($message),0, $e);
        }
    }

    /**
     * @param callable $callback
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }
}
