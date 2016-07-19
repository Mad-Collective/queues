# queues

This is the Queues Abstraction Library. It will provide you with two main abstractions:

**[DomainEvent][1]**: Publish and subscribe to Domain Events.
**[Tasks][2]**: Produce and Consume Tasks.

[1]: doc/DomainEvents.md
[2]: doc/Tasks.md

## Installation

Add this repo to your composer.json 

````json
"repositories": {
  "cmp/queues": {
    "type": "vcs",
    "url": "git@github.com:CMProductions/queues.git"
  }
}
````

Then require it as usual:

``` bash
composer require "cmp/queues"
```


