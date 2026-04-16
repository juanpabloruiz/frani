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

1. Revisar o ajustar `.env`
2. Levantar el stack:

```bash
docker compose up --build
```

3. Abrir:

- App: `http://localhost:${NGINX_PORT}`
- phpMyAdmin: `http://localhost:${PHPMYADMIN_PORT}`
