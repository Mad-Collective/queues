<?php

namespace Cmp\Queue\Infrastructure\RabbitMQ;

use Cmp\Queue\Domain\ConnectionException;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use Psr\Log\LoggerInterface;

class RabbitMQWriterInitializer
{
    const DELAY_QUEUE_PREFIX = 'Delay';

    /**
     * @var AMQPLazyConnection
     */
    private $connection;

    /**
     * @var string
     */
    private $exchangeName;

    /**
     * @var string
     */
    private $exchangeType;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RabbitMQWriterInitializer constructor.
     * @param AMQPLazyConnection $connection
     * @param string             $exchangeName
     * @param string             $exchangeType
     * @param LoggerInterface    $logger
     */
    public function __construct(AMQPLazyConnection $connection, $exchangeName, $exchangeType, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->exchangeName = $exchangeName;
        $this->exchangeType = $exchangeType;
        $this->logger = $logger;
    }

    /**
     * @return \PhpAmqpLib\Channel\AMQPChannel
     * @throws ConnectionException
     */
    public function initialize()
    {
        $this->logger->info('Connecting to RabbitMQ');

        try {
            $channel = $this->connection->channel(); // this is the one starting the connection
            $channel->exchange_declare($this->exchangeName, $this->exchangeType, false, true, false);

            return $channel;
        } catch (\ErrorException $e) {
            $this->logger->error('Error trying to connect to rabbitMQ:' . $e->getMessage());
            throw new ConnectionException('Error trying to connect to the queue backend');
        }
    }

    /**
     * @param integer $delay
     * @return string
     */
    public function initializeDelayQueue($delay)
    {
        $channel = $this->connection->channel();

        $exchangeDelayed = self::DELAY_QUEUE_PREFIX.$delay.$this->exchangeName;
        $queueDelayed = self::DELAY_QUEUE_PREFIX.$delay.'Queue';

        // Delay Queue
        $channel->exchange_declare($exchangeDelayed, 'fanout', false, true, true);
        $channel->queue_declare(
            $queueDelayed,
            false,
            true,
            false,
            true,
            false,
            [
                'x-expires' => ['I', $delay*1000 + 5000],
                'x-message-ttl' => array('I', $delay*1000),
                'x-dead-letter-exchange' => array('S', $this->exchangeName)
            ]
        );
        $channel->queue_bind($queueDelayed, $exchangeDelayed);

        return $exchangeDelayed;
    }
}