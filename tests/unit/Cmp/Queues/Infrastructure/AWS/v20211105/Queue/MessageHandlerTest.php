<?php

namespace unit\Cmp\Queues\Infrastructure\AWS\v20211105\Queue;

use Cmp\Queues\Domain\Queue\JSONMessageFactory;
use Cmp\Queues\Infrastructure\AWS\v20121105\Queue\MessageHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MessageHandlerTest extends TestCase
{
    private MessageHandler $service;
    private MockObject $jsonMessageFactory;
    private bool $callbackCalled;

    protected function setUp(): void
    {
        $this->jsonMessageFactory = $this->createMock(JSONMessageFactory::class);

        $this->service = new MessageHandler(
            $this->jsonMessageFactory,
        );

        $this->callbackCalled = false;

        $callback = function () {
            $this->callbackCalled = true;
        };
        $this->service->setCallback($callback);
    }

    public function testGivenSQSMessageWhenIsPublishedToSQSDirectlyThenCallbackIsCalled(): void
    {
        $message = [
            'Body' => '[{"delay":0,"payload":{"Message":{"name":"messageName","body":{"user_id":1,"tenant_id":4},"delay":0}},"user":"jucy-site-client","destination":{"kind":"sqs","params":{"queue_url":"example"}}}]',
        ];

        $this->service->handleMessage($message);

        self::assertTrue($this->callbackCalled);
    }

    public function testGivenSQSMessageWhenIsPublishedToRemind2DoThenCallbackIsCalled()
    {
        $message = [
            'Body' => '{"Message":{"body":{"tenant_id":4,"user_id":1},"delay":0,"name":"message name"}}'
        ];

        $this->service->handleMessage($message);

        self::assertTrue($this->callbackCalled);
    }
}

//"Body" => "{"Message":{"body":{"tenant_id":4,"user_id":1},"delay":0,"name":"App\\User\\Application\\Service\\GenerateBackgroundInteractionsService"}}"

//^ array:4 [
//    "MessageId" => "cd12ff0b-e742-4d04-b3a9-7c624f49eec5"
//  "ReceiptHandle" => "AQEB9ZSvvKLr9ucCYDpler/sDTbyZ3m5bAmPkNsI5FR45zMLP7bKNhe62Pl37jAVCYp9IMfGY4u5Cr7pNCukya73cVuYlVct2tqXuckYgHwSjbJb83mLTdS5sMtqbGBoJtTriPkxN+TYwuOiAkzxfs1gy7RY+hFIyK0lCxhJ9bFp48BzriYa2X1ATiNUFwEInElMGgF7iBblsjJ7DMRAiN23Q/b0l8ogahm2b1MDF2ioHQRg6gwTsM7CUdd04SuCnP9tpBWRb4q/MuLpDP/uMqZItr4s2c1Km7M4EC+YfL+nnWtQopxmWgwqUaXR6Hhb4qyjga2BcalgFmre3aoX4j6jFLrz8t+49fAvNlC3suh6jG/JOLaNPSgLVr77MoEDx+4m6z2TAbDMERROmIkWIbkO9Ih5BmHMU40qseT1340UOIc="
//  "MD5OfBody" => "6ce4eb86e55a77f98c5cae9214990977"
//  "Body" => "[{"delay":0,"payload":{"Message":{"name":"App\\User\\Application\\Service\\GenerateBackgroundInteractionsService","body":{"user_id":1,"tenant_id":4},"delay":0}},"user":"jucy-site-client","destination":{"kind":"sqs","params":{"queue_url":"https://sqs.us-east-1.amazonaws.com/966746064837/staging-jucy-site-remind2do2-tasks"}}}]"
//]

