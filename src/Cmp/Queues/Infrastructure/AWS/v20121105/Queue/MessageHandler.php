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
     * @throws ParseMessageException
     * @throws ReaderException
     */
    public function handleMessage(array $message)
    {
        if (!isset($this->callback)) {
            throw new ReaderException("Handling a message with no callback set");
        }

        if (!isset($message['Body'])) {
            throw new ParseMessageException(json_encode($message),0, 'Undefined index key Body: ' . print_r($message, true));
        }

        try{

            $body = json_decode($message['Body'], true);

            if (!isset($body['Message'])) {
                throw new InvalidJSONDomainEventException('Undefined index key Message: ' . print_r($body, true));
            }

            call_user_func($this->callback, $this->jsonMessageFactory->create($body['Message']));

        } catch(InvalidJSONMessageException $e) {
            throw new ParseMessageException(json_encode($message),0, $e);
        } catch(Exception $e) {
            throw $e;
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