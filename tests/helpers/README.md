# Test Helpers

The test helpers are little apps exposing the queues library via CLI and HTTP.

None of these applications are production ready and are made just for testing the library (i.e. Load Testing).

You have available two types of apps:

 - Command Line Apps
 - HTTP App

## Command Line Apps

The CLI Apps will allow you to Produce and Consume [Tasks][1] and to Publish and Subscribe to [DomainEvents][2].

All of them require the Queue Backend (RabbitMQ) configuration available in environment variables:

 - QUEUES_RABBITMQ_HOST
 - QUEUES_RABBITMQ_PORT
 - QUEUES_RABBITMQ_USER
 - QUEUES_RABBITMQ_PASS
 - QUEUES_RABBITMQ_EXCHANGE
 - QUEUES_RABBITMQ_QUEUE
 
A way to set them is to declare right before launching the app:
 
````
QUEUES_RABBITMQ_HOST="a__host" QUEUES_RABBITMQ_PORT=5672 QUEUES_RABBITMQ_USER="a_user_" QUEUES_RABBITMQ_PASS="a_pass_" QUEUES_RABBITMQ_EXCHANGE="a_exchange_name" QUEUES_RABBITMQ_QUEUE="a_queue_name" ./subscribe user.#
````

REMEMBER: Tasks and Domain Events uses diferent rabbitMQ internal routing mecanisms, so you cannot use the same exchange or queue name for the two.

### Produce

Produce will allow you to produce Tasks:

````
./produce <id>
````

In example:

````
./produce 34
````

**DISCLAIMER**: This will set a hardcoded body in the task providing the task body is not suported by this tool.

### Consume

Consume will allow you to Consume Tasks:

````
./consume
````

### Publish

Publish will allow you to publish Domain Events:

````
./publish <origin> <name>
````

In example:

````
./publish wellhello user.created.free
````

### Subscribe

Subscribe will allow you to subscribe to Domain Events using Domain Topics:

````
./subscribe <domainTopic1> <domainTopic2> ...
````

````
./subscribe user.# admin.content.#
````

## HTTP App

The HTTP App will allow you to Produce [Tasks][1] and Publish [DomainEvents][2] with a HTTP request.

First you will need to set the directory "tests/helpers/http" to be served by a server with PHPFPM.

You will also need the Queue Backend (RabbitMQ) configuration available in environment variables to this PHPFPM:

 - QUEUES_RABBITMQ_HOST
 - QUEUES_RABBITMQ_PORT
 - QUEUES_RABBITMQ_USER
 - QUEUES_RABBITMQ_PASS
 - QUEUES_RABBITMQ_EXCHANGE
 - QUEUES_RABBITMQ_QUEUE
 
Once all is set up the HTTP app will expose two endpoints:
 
### POST /task
 
This endpoint will allow you to produce Tasks.

Expected body:
 
````json
{
    "id": "23",
    "body": {"message":"ole ole!!!"}
}
````

### POST /domainevent
 
This endpoint will allow you to publish Domain Events.

Expected body:
 
````json
{
    "origin": "wellhello",
    "name": "user.created.free",
    "body": {"message":"ole ole!!!"}
}
````



[1]: doc/Tasks.md
[2]: doc/DomainEvents.md