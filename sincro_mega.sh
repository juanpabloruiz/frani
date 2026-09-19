#!/bin/bash
# Sincronización continua de Frani hacia MEGA (macOS: launchd / Linux: systemd).
# Portable: usa su propio directorio y $HOME.
set -u

DIR="$(cd "$(dirname "$0")" && pwd)"
LOG="$DIR/respaldo_mega.log"
MEGA="$HOME/MEGA/frani"
PRODUCTOS="$DIR/src/img/productos"
SQL="$DIR/respaldo.sql"

FECHA=$(date '+%Y-%m-%d %H:%M:%S')

mkdir -p "$MEGA/productos"

# 1) Imágenes de productos (nuevas/cambiadas/borradas)
salida=$(rsync -a --delete -i "$PRODUCTOS/" "$MEGA/productos/")
if [ -n "$salida" ]; then
    echo "[$FECHA] MEGA: imágenes sincronizadas" >> "$LOG"
fi

# 2) respaldo.sql solo si cambió (más nuevo que la copia en MEGA)
salida=$(rsync -u -i "$SQL" "$MEGA/respaldo.sql")
if [ -n "$salida" ]; then
    echo "[$FECHA] MEGA: respaldo.sql actualizado" >> "$LOG"
fi