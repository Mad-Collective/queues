<?php

namespace spec\Cmp\DomainEvent\Infrastructure\Publisher;

use Cmp\DomainEvent\Infrastructure\Exception\BackendNotImplementedException;
use PhpSpec\ObjectBehavior;

class PublisherFactorySpec extends ObjectBehavior
{

    public function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\DomainEvent\Infrastructure\Publisher\PublisherFactory');
    }

    public function it_should_throw_exception_if_not_implemented_backend_is_provided()
    {
        $this->shouldThrow(new BackendNotImplementedException())->during('create', [['backend' => 'backendThatIsNotImplementedForSure']]);
    }

    public function it_should_return_rabbitmq_publisher_if_backend_rabbitmq_is_provided()
    {
        $this->create(['backend' => 'rabbitmq'])->shouldReturnAnInstanceOf('Cmp\DomainEvent\Infrastructure\Publisher\RabbitMQ\Publisher');
    }

}