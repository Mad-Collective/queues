<?php
namespace Infrastructure\AmqpLib\v26;

use Domain\Queue\QueueWriter as DomainQueueWriter;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Message\AMQPMessage;

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
     * QueueWriter constructor.
     * @param ConnectionConfig $connectionConfig
     * @param ExchangeConfig $exchangeConfig
     */
    public function __construct(
        ConnectionConfig $connectionConfig,
        ExchangeConfig $exchangeConfig
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
        $channel = $this->connection->channel();
        $channel->exchange_declare(
            $this->exchangeConfig->getName(),
            $this->exchangeConfig->getType(),
            $this->exchangeConfig->getPassive(),
            $this->exchangeConfig->getDurable(),
            $this->exchangeConfig->getAutoDelete()
        );
        foreach($messages as $message) {
            $encodedMessage = json_encode($message);
            //$this->logger->debug('Writing:' . $encodedMessage);
            $msg = new AMQPMessage($encodedMessage, array('delivery_mode' => 2));
            $channel->batch_basic_publish($msg, $this->exchangeConfig->getName(), $message->getName());
        }
        $channel->publish_batch();
    }
}