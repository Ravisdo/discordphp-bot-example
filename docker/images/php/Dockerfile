FROM php:7.4-fpm-alpine

LABEL maintainer="Patrick Werner <patrick@werner.domains>"

ADD docker/etc/php/fpm/php-fpm.conf /usr/local/etc/php-fpm.conf
ADD docker/etc/php/php.ini /usr/local/etc/php/php.ini
ADD --chown=82:82 ./ /var/www

RUN apk add --update --no-cache --virtual .build-deps \
        mysql-client \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
    && rm -fr /var/cache/apk/*

WORKDIR /var/www
