# Pluggit - Queues
[![Build Status](https://travis-ci.org/CMProductions/queues.svg?branch=master)](https://travis-ci.org/CMProductions/queues)
[![Build Status](https://scrutinizer-ci.com/g/CMProductions/queues/badges/build.png?b=master)](https://scrutinizer-ci.com/g/CMProductions/queues/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/CMProductions/queues/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/CMProductions/queues/?branch=master)


This is the Queues Abstraction Library. It will provide you with two main abstractions:

+ [DomainEvent][1]: Publish and subscribe to Domain Events.
+ [Tasks][2]: Produce and Consume Tasks.

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


## Test Helpers

This library is exposed in CLI for testing purposes:

[Test Helpers][3]

[3]: tests/helpers/README.md

## Environment

Start environment
```bash
make dev
```

Enter environment
```bash
make enter
```

## Logs
PhpSpec:
```bash
bin/phpspec run
```

Behat:
```bash
bin/behat
```