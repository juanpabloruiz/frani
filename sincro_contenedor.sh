#!/bin/bash
# Sincronización continua dentro del contenedor (servicio "sincro").
# Copia respaldo.sql + productos/ a la MEGA del host (montada en /mega)
# y hace un dump completo de la BD cada día a las 07:00.
set -u

SQL="/app/proyecto/respaldo.sql"
SRC="/app/proyecto/src/img/productos"
MEGA="/mega"

mkdir -p "$MEGA/productos"

dump() {
    local temporal="$MEGA/respaldo.sql.tmp"
    if MYSQL_PWD="$DB_PASSWORD" mysqldump --single-transaction --routines --events \
        --no-tablespaces --set-gtid-purged=OFF --column-statistics=0 --skip-masking-policies \
        -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" "$DB_NAME" > "$temporal"; then
        mv "$temporal" "$MEGA/respaldo.sql"
    else
        rm -f "$temporal"
        echo "Error al generar respaldo MySQL" >&2
        return 1
    fi
}

echo "[$(date '+%Y-%m-%d %H:%M:%S')] sincro: dump inicial"
dump

while true; do
    if [ -e "$SQL" ]; then
        cp -u "$SQL" "$MEGA/respaldo.sql"
    fi

    for f in "$SRC"/*; do
        [ -e "$f" ] || continue
        bn=$(basename "$f")
        if [ -e "$MEGA/productos/$bn" ] && cmp -s "$f" "$MEGA/productos/$bn"; then
            continue
        fi
        cp "$f" "$MEGA/productos/"
    done

    for f in "$MEGA/productos"/*; do
        [ -e "$f" ] || continue
        bn=$(basename "$f")
        [ -e "$SRC/$bn" ] || rm -f "$f"
    done

    if [ "$(date +%H:%M)" = "07:00" ]; then
        echo "[$(date '+%Y-%m-%d %H:%M:%S')] sincro: dump diario"
        dump
    fi

    sleep 15
done
