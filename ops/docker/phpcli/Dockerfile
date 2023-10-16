FROM php:8.0-fpm-alpine

RUN docker-php-ext-install -j$(nproc) \
        bcmath \
        sockets

#ENV USER queues
#ENV FOLDER /app/queues
#ENV RABBITMQ_HOST rabbitmq
#ENV RABBITMQ_PORT 5672
#ENV RABBITMQ_USER guest
#ENV RABBITMQ_PASSWORD guest
#ENV RABBITMQ_VHOST /

# Set the workdir to the base app
WORKDIR /app/queues

# COPY php config
COPY confs/php.ini /usr/local/etc/php/php.ini

RUN apk add --no-cache \
        curl
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
