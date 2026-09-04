FROM php:8.4-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        nginx \
        default-mysql-client \
        libpng-dev \
        libjpeg62-turbo-dev \
        libwebp-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install mysqli gd \
    && rm -rf /var/lib/apt/lists/*

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
