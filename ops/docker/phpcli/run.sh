# Install and/or run composer on the container
COMPOSER_BIN=`which composer`

set -e

if [[ ! -f $COMPOSER_BIN ]]; then

	curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer;

fi

composer install --no-interaction