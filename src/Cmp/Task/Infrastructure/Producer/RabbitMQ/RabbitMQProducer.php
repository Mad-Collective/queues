<?php

namespace Cmp\Task\Infrastructure\Producer\RabbitMQ;

use Cmp\Task\Domain\Task\Task;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class RabbitMQProducer
{

    /**
     * @var array
     */
    private $config;

    /**
     * @var RabbitMQProducerInitializer
     */
    private $rabbitMQProducerInitializer;

    /**
     * @var AMQPChannel
     */
    private $channel = null;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RabbitMQProducer constructor.
     *
     * @param RabbitMQProducerInitializer $rabbitMQProducerInitializer
     * @param array                       $config
     * @param LoggerInterface             $logger
     */
    public function __construct(RabbitMQProducerInitializer $rabbitMQProducerInitializer, array $config, LoggerInterface $logger)
    {

        $this->rabbitMQProducerInitializer = $rabbitMQProducerInitializer;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function produce(Task $task)
    {
        if (!$this->channel) {
            $this->channel = $this->rabbitMQProducerInitializer->initialize();
        }

        $this->logger->debug('Producing Task:' . json_encode($task));
        $msg = new AMQPMessage(json_encode($task), array('delivery_mode' => 2));
        $this->channel->basic_publish($msg, '', $this->config['queue']);
    }


}