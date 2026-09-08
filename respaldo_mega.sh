#!/bin/bash
# Respaldos diarios de Frani hacia MEGA (versionado por MEGA)
# BD -> respaldo.sql , imágenes -> productos/
set -u

LOG="/Users/pabloruiz/docker/frani/respaldo_mega.log"
MEGA="/Users/pabloruiz/MEGA/frani"
PRODUCTOS="/Users/pabloruiz/docker/frani/src/img/productos"
ENV="/Users/pabloruiz/docker/frani/.env"

FECHA=$(date '+%Y-%m-%d %H:%M:%S')
echo "[$FECHA] Inicio del respaldo" >> "$LOG"

# Cargar credenciales de BD desde .env
set -a
. "$ENV"
set +a

mkdir -p "$MEGA/productos"

# 1) Respaldo de la base de datos -> respaldo.sql (MEGA versiona al sobrescribir)
if ! docker exec frani-db sh -c \
    "mariadb-dump --single-transaction --routines -uroot -p\"\$MARIADB_ROOT_PASSWORD\" \"$DB_NAME\"" \
    > "$MEGA/respaldo.sql" 2>> "$LOG"; then
    echo "[$FECHA] ERROR en mariadb-dump" >> "$LOG"
    exit 1
fi
echo "[$FECHA] respaldo.sql -> $MEGA/respaldo.sql ($(wc -c < "$MEGA/respaldo.sql") bytes)" >> "$LOG"

# 2) Sincronizar imágenes de productos -> productos/
if ! rsync -a "$PRODUCTOS/" "$MEGA/productos/" >> "$LOG" 2>&1; then
    echo "[$FECHA] ERROR en rsync de productos" >> "$LOG"
    exit 1
fi
echo "[$FECHA] Imágenes sincronizadas en $MEGA/productos/" >> "$LOG"

echo "[$FECHA] Respaldo completado." >> "$LOG"