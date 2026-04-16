FROM php:8.4-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        default-mysql-client \
    && docker-php-ext-install mysqli \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/conf.d/app.ini /usr/local/etc/php/conf.d/app.ini
