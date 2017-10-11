<?php

namespace Cmp\Queues\Infrastructure\AWS\v20121105\Queue;

use Aws\Sns\SnsClient;
use Aws\Sqs\SqsClient;

class Queue
{
    /**
     * @var SqsClient
     */
    private $sqs;

    /**
     * @var SnsClient
     */
    private $sns;

    /**
     * @param SqsClient $sqs
     * @param SnsClient $sns
     */
    public function __construct(SqsClient $sqs, SnsClient $sns)
    {
        $this->sqs = $sqs;
        $this->sns = $sns;
    }

    /**
     * @param string $region
     *
     * @return self
     */
    public static function create($region)
    {
        $sqs = SqsClient::factory(['region' => $region, 'version' => '2012-11-05',]);
        $sns = SnsClient::factory(['region' => $region, 'version' => '2010-03-31']);

        return new self($sqs, $sns);
    }

    /**
     * Creates the queue, the topic and the binding between the queue and the topic-
     *
     * @param string $queueName
     * @param string $topicName
     *
     * @return array with two values: queueUrl and topicArn
     */
    public function createQueueAndTopic($queueName, $topicName)
    {
        $queueUrl = $this->createQueue($queueName);
        $topicArn = $this->createTopic($topicName);
        $this->bindQueueToTopic($queueUrl, $topicArn);

        return [
            'queueUrl' => $queueUrl,
            'topicArn' => $topicArn,
        ];
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function createQueue($name)
    {
        return $this->sqs->createQueue(['QueueName' => $name])->get('QueueUrl');
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function createTopic($name)
    {
        return $this->sns->createTopic(['Name' => $name])->get('TopicArn');
    }

    /**
     * @param string $queueUrl
     * @param string $topicArn
     */
    public function bindQueueToTopic($queueUrl, $topicArn)
    {
        $queueArn = $this->sqs->getQueueArn($queueUrl);
        $this->sns->subscribe([
            'TopicArn' => $topicArn,
            'Protocol' => 'sqs',
            'Endpoint' => $queueArn,
        ]);

        $this->sqs->setQueueAttributes([
            'QueueUrl' => $queueUrl,
            'Attributes' => [
                'Policy' => json_encode([
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
                ]),
            ]
        ]);
    }
}
