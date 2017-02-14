<?php
namespace Infrastructure\AmqpLib\v26\Queue;

use Domain\Queue\QueueWriter as DomainQueueWriter;
use Domain\Queue\WriterException;
use Infrastructure\AmqpLib\v26\Queue\Config\ConnectionConfig;
use Infrastructure\AmqpLib\v26\Queue\Config\ExchangeConfig;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class QueueWriter implements DomainQueueWriter
{
    /**
     * @var AMQPLazyConnection
     */
    protected $connection;

    /**
     * @var ConnectionConfig
     */
    protected $connectionConfig;

    /**
     * @var ExchangeConfig
     */
    protected $exchangeConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * QueueWriter constructor.
     * @param ConnectionConfig $connectionConfig
     * @param ExchangeConfig $exchangeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConnectionConfig $connectionConfig,
        ExchangeConfig $exchangeConfig,
        LoggerInterface $logger
    )
    {
        $this->connection = new AMQPLazyConnection(
            $connectionConfig->getHost(),
            $connectionConfig->getPort(),
            $connectionConfig->getUser(),
            $connectionConfig->getPassword(),
            $connectionConfig->getVHost()
        );
        $this->connectionConfig = $connectionConfig;
        $this->exchangeConfig = $exchangeConfig;
    }

    public function write(array $messages)
    {
        $this->logger->info('Connecting to RabbitMQ');
        try {
            $channel = $this->connection->channel();
            $channel->exchange_declare(
                $this->exchangeConfig->getName(),
                $this->exchangeConfig->getType(),
                $this->exchangeConfig->getPassive(),
                $this->exchangeConfig->getDurable(),
                $this->exchangeConfig->getAutoDelete()
            );
        } catch (\ErrorException $exception) {
            $this->logger->error('Error trying to connect to rabbitMQ:' . $exception->getMessage());
            throw new WriterException($exception->getMessage(), $exception->getCode());
        }

        foreach($messages as $message) {
            $encodedMessage = json_encode($message);
            $this->logger->debug('Writing:' . $encodedMessage);
            $msg = new AMQPMessage($encodedMessage, array('delivery_mode' => 2));
            $channel->batch_basic_publish($msg, $this->exchangeConfig->getName(), $message->getName());
        }

        $channel->publish_batch();
    }
}