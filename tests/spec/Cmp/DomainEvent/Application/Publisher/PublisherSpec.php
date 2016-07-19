<?php

namespace spec\Cmp\DomainEvent\Application\Publisher;

use Cmp\Queue\Infrastructure\RabbitMQ\RabbitMQConfig;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;

class PublisherSpec extends ObjectBehavior
{

    public function let(RabbitMQConfig $config, LoggerInterface $logger)
    {
        $this->beConstructedWith($config, $logger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Application\Publisher\Publisher');
    }

}