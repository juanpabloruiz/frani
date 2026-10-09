# Frani

Aplicación PHP con MySQLi OO para gestión de productos, dockerizada con:

- `php:8.4-fpm`
- `nginx`
- `mariadb`
- `phpmyadmin`

## Estructura

- `src/`: aplicación PHP
- `init.sql`: esquema inicial de la base de datos
- `compose.yaml`: orquestación de servicios
- `Dockerfile`: imagen PHP-FPM con extensión `mysqli`
- `nginx.conf`: virtual host de Nginx

## Uso

1. Crear la configuración local a partir del ejemplo y ajustar las contraseñas si hace falta:

```bash
cp .env.example .env
```

2. Levantar el stack:

```bash
docker compose up --build
```

3. Abrir:

- App: `http://localhost:${NGINX_PORT}`
- phpMyAdmin: `http://localhost:${PHPMYADMIN_PORT}`

## Actualización de descuentos

Antes de actualizar una instalación existente, agregar el campo de descuento en pesos:

```bash
docker compose exec -T db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' < migrations/20261006_descuento_importe.sql
```

La migración se puede repetir. Conserva los descuentos anteriores en porcentaje; al editar esas ventas se muestra su importe equivalente. Las ventas nuevas guardan el descuento en pesos, con centavos.

## Páginas de productos y URLs amigables

Cada card de la portada y las categorías enlaza a una ficha servida por `src/producto.php`, por ejemplo `/juguetes/masa-magica-por-12`. La ficha muestra foto, descripción, categoría, precio y disponibilidad, con título, descripción y URL canónica para buscadores. Debajo aparecen hasta tres productos relacionados con miniaturas y enlaces: primero los de la misma categoría y luego otros del catálogo si hace falta completar tres sugerencias. Nunca se incluye el producto actual.

Antes de usar los nuevos archivos PHP en una instalación existente, ejecutar la migración:

```bash
docker compose exec -T web php /var/www/backup/migrations/20261009_productos_uri.php
docker compose exec -T web php /var/www/backup/migrations/20261009_categorias_uri.php
docker compose exec -T web nginx -t
docker compose exec -T web nginx -s reload
```

Las migraciones agregan `productos.uri` y `categorias.uri` con índices únicos y completan los registros existentes. Son repetibles y conservan las fechas de modificación. `generar_uri()` quita acentos, convierte ñ a n, usa minúsculas y reemplaza separadores por guiones. Por ejemplo, `Librería` se convierte en `libreria` y `Sublimación` en `sublimacion`. Las URI admiten hasta 200 caracteres y los nombres repetidos al normalizar reciben sufijos `-2`, `-3`, etc. Se reservan nombres de categorías que coinciden con rutas del sistema, como `panel`, `productos`, `css` e `img`; reciben un sufijo para evitar conflictos.

Las altas de productos y categorías generan su URI automáticamente dentro de la misma transacción. Al editar sus nombres se conservan las URI para mantener los enlaces publicados. Los campos permiten `NULL` durante el alta; los registros guardados desde el panel terminan con su URI asignada. Las importaciones SQL externas deben ejecutar nuevamente las migraciones para completar las URI faltantes. `APP_DOMAIN` configura el dominio de las URLs canónicas HTTPS.

Los listados de categorías usan `/juguetes`, `/libreria`, etc. Los enlaces anteriores `/productos/{uri}`, `producto.php?uri=...` y `categoria.php?id=...` redirigen permanentemente a la URL canónica. Si un producto cambia de categoría, su ruta anterior también redirige a la categoría actual.

En instalaciones sin el montaje de `/var/www/backup`, ejecutar ambas migraciones con `php migrations/{archivo}.php` desde el checkout, con PHP y acceso a la base de datos; desplegar también las reglas de `nginx.conf`.

Pruebas de normalización, colisiones, estabilidad y migración (usan una tabla temporal):

```bash
docker compose exec -T web php /var/www/backup/tests/productos_uri.php
docker compose exec -T web php /var/www/backup/tests/categorias_uri.php
```
