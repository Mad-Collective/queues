#!/bin/bash

# Install and/or run composer on the container
COMPOSER_BIN=`which composer`

set -e

if [[ ! -f $COMPOSER_BIN ]]; then

	curl -sS http://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer;

fi

composer install --no-interaction
sleep 99999999
