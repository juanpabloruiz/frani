#!/bin/bash
# Instala el disparador de sincro_mega.sh según el SO anfitrión.
# Uso: ./instalar.sh   (con sudo si hace falta, para Linux)
# Es idempotente: se puede volver a ejecutar sin romper nada.
set -eu

DIR="$(cd "$(dirname "$0")" && pwd)"
USER_REAL="$(id -un)"

case "$(uname -s)" in
    Darwin)
        DEST="$HOME/Library/LaunchAgents/com.frani.megasync.plist"
        sed "s|@@DIR@@|$DIR|g" "$DIR/com.frani.megasync.plist" > "$DEST"
        launchctl bootout gui/"$(id -u)"/com.frani.megasync 2>/dev/null || true
        launchctl bootstrap gui/"$(id -u)" "$DEST"
        echo "LaunchAgent instalado y activo (com.frani.megasync)"
        ;;
    Linux)
        sudo sed -e "s|@@DIR@@|$DIR|g" \
            -e "s|@@USER@@|$USER_REAL|g" \
            "$DIR/frani-megasync.service" > /etc/systemd/system/frani-megasync.service
        sudo sed "s|@@DIR@@|$DIR|g" \
            "$DIR/frani-megasync.timer" > /etc/systemd/system/frani-megasync.timer
        sudo systemctl daemon-reload
        sudo systemctl enable --now frani-megasync.timer
        echo "Timer de systemd instalado y activo (frani-megasync.timer)"
        ;;
    *)
        echo "Sistema no soportado: $(uname -s)" >&2
        exit 1
        ;;
esac