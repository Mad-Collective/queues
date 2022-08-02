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

ADD https://letsencrypt.org/certs/isrgrootx1.pem.txt /usr/local/share/ca-certificates/isrgrootx1.pem
RUN sed '/DST_Root_CA_X3.crt/d' /etc/ca-certificates.conf > /tmp/cacerts.conf && mv /tmp/cacerts.conf /etc/ca-certificates.conf
RUN cd /usr/local/share/ca-certificates && openssl x509 -in isrgrootx1.pem -inform PEM -out isrgrootx1.crt
RUN update-ca-certificates

VOLUME ["$FOLDER"]

ENTRYPOINT exec /run.sh