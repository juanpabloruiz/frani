FROM container-registry.oracle.com/mysql/community-server:26.10.0-aarch64@sha256:8f32e012b7ebf9005f1b2fb64a72ce0830b6775d17dd44f2c75c6ed791a0d2ae AS mysql-client

FROM php:8.5.12-fpm-trixie@sha256:1262f88f3d86e04fa74a28cf26c60e2509af2023323c4f5d4cd11e65834bb5a1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        nginx \
        libncurses6 libstdc++6 diffutils \
        libpng-dev \
        libjpeg62-turbo-dev \
        libwebp-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install mysqli gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=mysql-client /usr/bin/mysql /usr/bin/mysqldump /usr/local/bin/

RUN rm -f /etc/nginx/sites-enabled/default

# Límites de subida de imágenes (productos/clientes). El tope real de la app
# (subir_foto) es 5MB; nginx-proxy central permite 32M.
RUN printf 'upload_max_filesize = 8M\npost_max_size = 16M\nmax_file_uploads = 20\n' \
    > /usr/local/etc/php/conf.d/zz-uploads.ini

COPY nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p /var/www/html

COPY src/ /var/www/html/

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
