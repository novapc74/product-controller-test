FROM php:8.4-alpine

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY docker/embed/php/conf.d $PHP_INI_DIR/conf.d

RUN apk update && apk add --no-cache \
        git \
        g++ \
        bash \
        make \
        wget \
        unzip \
        libtool \
        icu-dev \
        autoconf \
        pkgconfig \
        libpng-dev \
        libzip-dev \
        libwebp-dev \
        freetype-dev \
        linux-headers \
    && docker-php-ext-install pdo pdo_mysql zip intl \
    && rm -rf /var/cache/apk/*

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && rm -rf /tmp/pear

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /project

ARG UID=1000
RUN adduser -D -u $UID -s /bin/bash develop && \
    echo "develop:develop" | chpasswd && \
    chown -R develop:develop /project

USER develop

RUN mkdir -p /home/develop/bin

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/home/develop/bin --filename=composer --quiet
RUN curl -sS https://get.symfony.com/cli/installer | bash

ENV PATH="/home/develop/bin:${PATH}"