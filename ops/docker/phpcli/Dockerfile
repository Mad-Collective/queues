FROM marclop/php-fpm-build:5.6-cli

MAINTAINER Marc Lopez <marc5.12@outlook.com>

ENV USER queues
ENV FOLDER /app/queues
ENV RABBITMQ_HOST rabbitmq
ENV RABBITMQ_PORT 5672
ENV RABBITMQ_USER guest
ENV RABBITMQ_PASSWORD guest
ENV RABBITMQ_VHOST /

### Application specific stuff ###

RUN useradd -u 1000 $USER

# Create app and ssh folder
RUN mkdir -p $FOLDER

# Set the workdir to the base app
WORKDIR $FOLDER

# COPY php config
COPY confs/php.ini /usr/local/etc/php/php.ini

# COPY RUN SCRIPT
COPY run.sh /run.sh
RUN chmod 755 /run.sh

VOLUME ["$FOLDER"]

ENTRYPOINT exec /run.sh