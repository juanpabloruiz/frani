FROM php:8.4-fpm

# Instalar dependencias necesarias para compilar/extensiones MySQL
RUN apt-get update && apt-get install -y --no-install-recommends \
    default-libmysqlclient-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    make \
    autoconf \
    gcc \
    g++ \
 && docker-php-ext-install pdo pdo_mysql mysqli \
 && apt-get remove -y gcc g++ make autoconf \
 && apt-get autoremove -y \
 && rm -rf /var/lib/apt/lists/*

 WORKDIR /var/www/html