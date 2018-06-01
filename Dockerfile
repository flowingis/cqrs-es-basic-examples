FROM php:7.2-fpm-alpine

RUN apk update && \
    apk add zlib-dev mysql-client

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

WORKDIR /var/www
