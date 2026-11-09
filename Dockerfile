# Digest del índice multiarquitectura: Docker elige amd64/arm64 según el host.
FROM php:8.5.12-fpm-trixie@sha256:baf3ea13a8207fa18e1011bad1016e8b5e59fc974c6966d0efa6f36a16f875f0

# PHP-FPM corre como www-data. Igualamos su uid/gid con el usuario del host
# (1000 por defecto) para que pueda escribir en los volúmenes montados:
# fotos de productos y respaldo.sql. Se puede ajustar al compilar.
ARG PUID=1000
ARG PGID=1000
RUN groupmod -g "${PGID}" www-data \
    && usermod -u "${PUID}" -g "${PGID}" www-data

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
