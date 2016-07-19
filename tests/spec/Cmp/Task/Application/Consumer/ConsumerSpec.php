<?php

namespace spec\Cmp\Task\Application\Consumer;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class ConsumerSpec extends ObjectBehavior
{

    public function let(RabbitMQConfig $config, LoggerInterface $logger)
    {
        $this->beConstructedWith($config, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Task\Application\Consumer\Consumer');
    }
}
