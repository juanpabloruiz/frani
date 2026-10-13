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

Al seleccionar una foto en el panel de productos se abre un editor de recorte con proporción libre, cuadrada, horizontal o vertical, zoom y giro. “Recortar foto” también permite editar la foto ya guardada. “Aplicar recorte” actualiza la vista previa; la imagen se guarda al guardar o actualizar el producto. Cancelar conserva la selección anterior y “Deshacer cambios de foto” recupera la foto guardada. Se puede usar la imagen completa. El editor usa Cropper.js 1.6.2 y FontAwesome locales, se adapta al tema del sistema y admite mouse, teclado y gestos táctiles. Los recortes se envían por el mismo campo `foto` como JPEG con fondo blanco y hasta 800 × 800 px, conservando la proporción; el servidor genera JPG y WebP como antes. No requiere migraciones.

Prueba de guardado de recortes y validación de archivos, con imágenes y directorios temporales:

```bash
docker compose exec -T web php /var/www/backup/tests/foto_producto.php
```

La portada, cada categoría y las fichas de producto tienen un buscador al comienzo del contenido. Filtra al escribir por palabras del nombre, la descripción o la categoría, sin distinguir mayúsculas ni acentos. La portada busca en todos los productos, incluso los que no aparecen entre las últimas 25 novedades; cada categoría busca solo en sus propios productos. En las fichas busca dentro de la categoría del producto y muestra las mismas tarjetas del catálogo; al limpiar el campo se recupera la ficha original. En los listados, limpiar restaura el listado inicial. No requiere migraciones.

La portada y las categorías cargan los productos en tandas de 25 al acercarse al final del listado. La portada recorre todo el catálogo por fecha y las categorías mantienen el orden por precio. Las búsquedas también cargan sus resultados progresivamente; cambiar o limpiar el término cancela la carga anterior. El botón “Ver más productos” permite cargar una tanda manualmente y reintentar si falla la conexión. Sin JavaScript, el mismo enlace abre la siguiente página (`?pagina=2`), con su propia URL canónica. El buscador de las fichas conserva su comportamiento original.

Los títulos H1 de portada y categorías usan `visually-hidden` de Bootstrap: forman parte del HTML y son accesibles para lectores de pantalla, sin ocupar espacio en la interfaz. El menú indica la sección activa. Las fichas conservan visible el nombre del producto. Las rutas de navegación se publican en los datos estructurados `BreadcrumbList`, sin mostrar breadcrumbs adicionales en la página.

El catálogo público usa el mismo `js/tema.js` que el panel: sigue la preferencia clara u oscura del sistema y cambia automáticamente si esa preferencia se modifica con la página abierta. Se carga al comienzo del `head` para aplicar el tema antes de pintar la interfaz. Los fondos, las tarjetas, el buscador y los textos se adaptan; las fotos mantienen sus colores originales.

Pruebas de alcance, palabras, acentos y respuestas del buscador:

```bash
docker compose exec -T web php /var/www/backup/tests/catalogo_busqueda.php
```

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

Los controles para crear, compartir y descargar la plantilla vertical sólo se muestran al iniciar sesión con `pabloruiz1980@gmail.com`. El correo se comprueba en el usuario que la sesión identifica en la base. Para visitantes y otras cuentas, esos controles y la vista previa se omiten del HTML. Los botones para compartir el enlace permanecen públicos; el endpoint de la imagen vertical conserva su acceso público.

Las fichas incluyen botones con los iconos de FontAwesome locales para compartir en Facebook, X, WhatsApp y Telegram. Comparten la URL canónica pública del producto, sin parámetros de seguimiento. “Copiar enlace” permite pegarlo en Instagram u otras aplicaciones; si el navegador no permite usar el portapapeles, muestra el enlace seleccionado para copiarlo manualmente. “Más opciones para compartir” abre el menú del dispositivo cuando el navegador admite Web Share (los destinos dependen de las aplicaciones instaladas). Los enlaces a redes sociales funcionan sin JavaScript y solo se abren cuando el usuario los pulsa.

“Imagen para estado de WhatsApp” abre la vista previa de una tarjeta JPEG vertical de 1080 × 1920 (9:16), con la foto completa ampliada, un fondo desenfocado y sólo la marca, el nombre y el precio en letras grandes. No incluye descripción, categoría ni dominio pequeño. “Compartir imagen” entrega exclusivamente el archivo al menú del dispositivo cuando el navegador permite compartir archivos; el usuario elige el destino. El archivo se prepara al abrir la vista previa, antes del clic para compartir. La descarga permanece disponible para guardar la imagen en la galería y seleccionarla desde Mi estado de WhatsApp. “Compartir enlace” identifica por separado los botones que envían la URL y generan la tarjeta horizontal. El endpoint `/imagen-estado.php?producto={id}` consulta los datos en la base, valida las rutas públicas y admite GET/HEAD y caché con ETag. `descargar=1` entrega el archivo como adjunto. Su URL cambia al actualizar la foto, el precio, los textos o la plantilla. Las rutas de vista previa y descarga pertenecen al mismo origen, incluso en `www` o localhost. Se reserva margen superior e inferior para los controles del estado. Las vistas previas Open Graph y X conservan su formato horizontal.

La portada, las categorías y las fichas sirven los metadatos directamente en el HTML, sin depender de JavaScript. `src/seo.php` prepara la información y `src/_seo.php` genera la descripción, la URL canónica, Open Graph para Facebook y las tarjetas de X.

Cada producto publica su título, descripción y foto. La descripción social comienza con el precio vigente en pesos argentinos (`ARS`) y también se incluyen `product:price:amount` y `product:price:currency`. Facebook y X deciden qué campos muestran en sus vistas previas; el precio y la descripción pueden no aparecer en todos los formatos. Los productos sin descripción usan un texto de consulta y los productos sin foto válida usan la misma ilustración de compra de la portada (`img/Ejemplo..jpg`) al compartir; la versión de la URL incluye ese archivo para renovar la caché cuando cambie.

`/imagen-social.php?producto={id}` genera un JPEG de 1200 × 630 con la foto completa y un bloque legible de título, descripción breve y precio actual en ARS. El texto forma parte de la imagen para seguir visible cuando una red omita la descripción de su tarjeta. Los datos se consultan en la base; el visitante no puede reemplazar el precio desde la URL. Las URLs incluyen una versión para renovar la caché cuando cambian la foto, el título, la descripción, la categoría, el precio o el generador. Las tarjetas de X usan `summary_large_image`. La tipografía Lato se distribuye con su licencia OFL en `src/fonts/`.

El modo `/imagen-social.php?foto={nombre}` conserva la vista de foto sola. El endpoint es público, no requiere iniciar sesión, admite GET/HEAD y valida la ruta del archivo. Un ID de producto inválido o inexistente responde 404 y los errores de consulta responden 503 sin caché.

La portada y las categorías tienen una tarjeta de marca independiente en `/imagen-catalogo.php`, de 1200 × 630 píxeles, con fondo blanco, el logo negro ampliado y centrado, y la descripción oscura a 24 puntos debajo. No incluye el título «Catálogo de productos» ni un dominio al pie. El logo ocupa hasta 900 píxeles de ancho para conservarlo completo y dejar espacio legible a dos líneas de descripción en el formato horizontal. El título y la descripción también se envían como metadatos Open Graph y X para el área de texto nativa de la aplicación receptora. La portada usa el título «Frani»; las categorías conservan su nombre. Estas usan `?categoria={id}` y se validan contra la base. `seo_catalogo_contenido()` prepara los textos y `seo_imagen_catalogo()` renueva la versión cuando cambian el logo, los metadatos o el generador. La canónica de la paginación se conserva.

El logo enviado en SVG se conserva en `src/img/social/frani-logo.svg`, con conversiones a PNG, JPG y WebP generadas a mayor resolución y sin el margen blanco original. Las vistas previas Open Graph y X apuntan a JPEG público, para compartir en Facebook, X, LinkedIn, WhatsApp, Telegram y otras aplicaciones que leen esos metadatos. La composición mantiene centrado el logo; los formatos que usan miniaturas cuadradas pueden recortar sus extremos. No modifica el logo visible del encabezado. Los textos visibles y la caché de cada tarjeta dependen de la aplicación receptora.

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
docker compose exec -T web php /var/www/backup/tests/imagen_catalogo.php
docker compose exec -T web php /var/www/backup/tests/imagen_estado.php
node tests/compartir_producto.js
docker compose exec -T web php /var/www/backup/tests/compartir_producto.php
# Comprobar también el dominio público, si sirve este mismo catálogo:
docker compose exec -T -e SEO_TEST_BASE_URL=https://frani.ar web php /var/www/backup/tests/seo.php
```

## Publicación automática en la página de Facebook

Las altas y ediciones desde **Productos** pueden crear publicaciones nuevas en una página propia de Facebook, con nombre, precio en ARS, descripción y enlace a la ficha. También se incluyen los cambios manuales de stock. Guardar sin cambios públicos o modificar solamente el costo no genera una publicación. Una edición publica otra novedad; no modifica publicaciones anteriores. Facebook puede conservar en caché la vista previa del enlace, por eso el mensaje incluye el precio guardado explícitamente.

El envío se realiza en un proceso separado: guardar un producto agrega el evento dentro de su misma transacción, sin esperar a Facebook. No se importa el catálogo anterior. Las ventas, los ajustes masivos de porcentajes, el panel de pruebas y los cambios SQL directos no generan eventos.

1. Preparar la tabla en una instalación existente (se puede repetir):

```bash
docker compose exec -T web php /var/www/backup/migrations/20261013_facebook_publicaciones.php
```

2. Completar en `.env` el ID de la **página** y su token de **Página**. `FACEBOOK_APP_ID` sigue siendo el metadato público de la aplicación; no reemplaza estas credenciales. Mantener `FACEBOOK_AUTO_PUBLISH=false` durante la configuración:

```dotenv
FACEBOOK_AUTO_PUBLISH=false
FACEBOOK_PAGE_ID=555387371639380
FACEBOOK_PAGE_ACCESS_TOKEN=REEMPLAZAR_POR_EL_TOKEN_DE_PAGINA
FACEBOOK_GRAPH_VERSION=v26.0
```

El token se conserva únicamente en `.env`, excluido de Git. El token de usuario se amplía a larga duración y se utiliza para obtener el de Página con `/me/accounts`; el de Página resultante puede indicar caducidad «Nunca». El token debe estar autorizado para publicar en esa página con `pages_manage_posts` y `pages_read_engagement`. Guías de Meta: [tokens de larga duración](https://developers.facebook.com/documentation/facebook-login/guides/access-tokens/get-long-lived) y [publicaciones](https://developers.facebook.com/documentation/pages-api/posts).

3. Aplicar el entorno al servicio web y comprobar la conexión. La comprobación solo lee el nombre e ID de la página; el primer guardado real comprueba también el permiso de publicación:

```bash
docker compose up -d --no-deps web
docker compose exec -T web php /var/www/html/facebook-worker.php --validar
```

4. Una vez validado, cambiar `FACEBOOK_AUTO_PUBLISH=true` y levantar web y worker:

```bash
docker compose --profile facebook up -d --no-deps web facebook-worker
```

El worker consulta la cola cada 15 segundos. Para pausar nuevos eventos y envíos, volver a `FACEBOOK_AUTO_PUBLISH=false` y recrear ambos servicios; los pendientes se conservan. El perfil `facebook` permite dejar la integración desactivada en otras instalaciones. Fuera de Docker, usar PHP CLI con el mismo entorno y supervisar `php src/facebook-worker.php --loop`; ejecutar la migración con `php migrations/20261013_facebook_publicaciones.php`.

Consultar estado o registros sin mostrar el token:

```bash
docker compose exec -T web php /var/www/html/facebook-worker.php --estado
docker compose logs --tail=50 facebook-worker
```

Los rechazos transitorios confirmados se reintentan hasta cinco veces con esperas crecientes, conservando el orden de los cambios de un mismo producto. Los rechazos por token/permisos quedan `fallido`; luego de corregir el acceso pueden volver a encolarse con `--reintentar=ID`. Un timeout, una respuesta sin ID o un proceso interrumpido durante el envío quedan `incierto`: deben revisarse en la página antes de cualquier nueva publicación, ya que Meta puede haber recibido el primer envío. Nunca se reenvían automáticamente los eventos inciertos ni los publicados. Los productos eliminados antes del envío se omiten, conservando el registro del evento.

Pruebas con transporte simulado, tablas temporales y sin publicar contenido real:

```bash
docker compose exec -T web php /var/www/backup/tests/facebook_api.php
docker compose exec -T web php /var/www/backup/tests/facebook_publicaciones.php
```
