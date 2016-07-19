<?php

namespace spec\Cmp\Task\Appliation\Producer;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class ProducerSpec extends ObjectBehavior
{

    public function let(RabbitMQConfig $config, LoggerInterface $logger)
    {
        $this->beConstructedWith($config, $logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Task\Application\Producer\Producer');
    }
}
