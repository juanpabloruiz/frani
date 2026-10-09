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

## SEO y vistas previas al compartir

La portada, las categorías y las fichas sirven los metadatos directamente en el HTML, sin depender de JavaScript. `src/seo.php` prepara la información y `src/_seo.php` genera la descripción, la URL canónica, Open Graph para Facebook y las tarjetas de X.

Cada producto publica su título, descripción y foto. La descripción social comienza con el precio vigente en pesos argentinos (`ARS`) y también se incluyen `product:price:amount` y `product:price:currency`. Facebook y X deciden qué campos muestran en sus vistas previas; el precio y la descripción pueden no aparecer en todos los formatos. Los productos sin descripción usan un texto de consulta y los productos sin foto usan el logo de Frani al compartir.

`/imagen-social.php?producto={id}` genera un JPEG de 1200 × 630 con la foto completa y un bloque legible de título, descripción breve y precio actual en ARS. El texto forma parte de la imagen para seguir visible cuando una red omita la descripción de su tarjeta. Los datos se consultan en la base; el visitante no puede reemplazar el precio desde la URL. Las URLs incluyen una versión para renovar la caché cuando cambian la foto, el título, la descripción, la categoría, el precio o el generador. Las tarjetas de X usan `summary_large_image`. La tipografía Lato se distribuye con su licencia OFL en `src/fonts/`.

El modo `/imagen-social.php?foto={nombre}` conserva la vista de foto sola; la portada y las categorías usan el logo. El endpoint es público, no requiere iniciar sesión, admite GET/HEAD y valida la ruta del archivo. Un ID de producto inválido o inexistente responde 404 y los errores de consulta responden 503 sin caché.

Para Google, cada ficha incluye datos estructurados `Product`, `Offer` y `BreadcrumbList`, con el mismo precio y disponibilidad visibles en la página. Un stock nulo omite la disponibilidad; un stock positivo indica disponible y cero o negativo indica agotado. Solo se declara una imagen de producto si existe una foto real. La portada incluye `WebSite` y `Organization`, y las categorías incluyen `CollectionPage` y su ruta de navegación. Los resultados enriquecidos dependen de Google.

- `/sitemap.xml` lista automáticamente la portada, las categorías y todos los productos con URI válida, aunque no aparezcan entre los 25 productos de la portada. Las fechas de productos usan UTC e incluyen cambios de categoría. No se inventan fechas de modificación de los listados.
- `/robots.txt` permite el catálogo y las fotos, excluye el panel y anuncia el sitemap. El panel y los endpoints de prueba también envían `noindex`.
- Las páginas inexistentes responden 404 y no publican datos estructurados de productos.

Este cambio no requiere migraciones adicionales. Al desplegar `src/` y `nginx.conf`, comprobar y recargar Nginx:

```bash
docker compose exec -T web nginx -t
docker compose exec -T web nginx -s reload
```

Registrar `https://{APP_DOMAIN}/sitemap.xml` en [Google Search Console](https://search.google.com/search-console) y comprobar una ficha con [Rich Results Test](https://search.google.com/test/rich-results). Para un enlace que Facebook ya tenga guardado, usar [Sharing Debugger](https://developers.facebook.com/tools/debug/) y volver a extraer la información. Las modificaciones de precio o descripción se reflejan al siguiente rastreo de cada plataforma; la caché social puede conservar los datos anteriores.

Para publicar `fb:app_id`, configurar `FACEBOOK_APP_ID` en `.env` con el identificador numérico real de una aplicación propia en [Meta for Developers](https://developers.facebook.com/apps/), disponible en Configuración → Básica. Es un identificador público: no es el ID de la página de Facebook ni el App Secret. El metadato se omite si falta o no es numérico; no se utiliza un ID ficticio para ocultar la advertencia del depurador. Después de cambiar `.env`, aplicar las variables al servicio con `docker compose up -d --no-deps web` y volver a extraer el enlace en Sharing Debugger.

El dominio canónico es el configurado por `APP_DOMAIN`, con HTTPS. En el proxy o Cloudflare conviene redirigir el acceso por HTTP y `www` a ese dominio; Nginx interno conserva HTTP para funcionar detrás del proxy y en desarrollo.

Pruebas de metadatos, precio, disponibilidad, escaping, imágenes y acceso de los rastreadores:

```bash
docker compose exec -T web php /var/www/backup/tests/seo.php
docker compose exec -T web php /var/www/backup/tests/imagen_social.php
# Comprobar también el dominio público, si sirve este mismo catálogo:
docker compose exec -T -e SEO_TEST_BASE_URL=https://frani.ar web php /var/www/backup/tests/seo.php
```
