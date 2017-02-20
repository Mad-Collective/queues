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

``` bash
composer require "pluggit/queues"
```

## Environment

Start environment
```bash
make dev
```

Enter environment
```bash
make enter
```

## Tests
PhpSpec:
```bash
bin/phpspec run
```

Behat:
```bash
bin/behat
```

Helpers:
Small cli tool for manual execution