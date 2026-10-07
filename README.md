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
