#!/usr/bin/env bash
set -euo pipefail

# Small helper to start the PHP built-in server for the `francoisdcls/` docroot.
# Usage:
#   scripts/start-dev.sh [mysql|sqlite]
# By default the script starts with mysql. You can override MYSQL env vars
# before calling the script to use different credentials.

BASE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DOCROOT="$BASE_DIR/francoisdcls"
LOG="$DOCROOT/var/php_dev_server.log"
PIDFILE="$DOCROOT/var/php_dev_server.pid"

usage() {
    cat <<EOF
Usage: $(basename "$0") [mysql|sqlite]

Starts the PHP dev server for the Francoisdcls site.
By default it uses MySQL. Pass 'sqlite' to use the local SQLite test DB
(the file is ${DOCROOT}/var/test_db.sqlite).
EOF
}

DRIVER="${1:-mysql}"

if [ -f "$PIDFILE" ]; then
    OLD_PID=$(cat "$PIDFILE" 2>/dev/null || true)
    if [ -n "$OLD_PID" ] && kill -0 "$OLD_PID" 2>/dev/null; then
        echo "Stopping existing dev server pid $OLD_PID"
        kill "$OLD_PID" || true
        sleep 0.2
    fi
    rm -f "$PIDFILE"
fi

case "$DRIVER" in
    mysql)
        export FRANCOISDB_DRIVER=mysql
        # sensible defaults — you can override these in your environment
        export FRANCOISDB_HOST="${FRANCOISDB_HOST:-195.15.235.20}"
        export FRANCOISDB_NAME="${FRANCOISDB_NAME:-francois_duclos}"
        export FRANCOISDB_USER="${FRANCOISDB_USER:-root}"
        export FRANCOISDB_PASS="${FRANCOISDB_PASS:-INNnsk40374}"
        ;;
    sqlite)
        export FRANCOISDB_DRIVER=sqlite
        SQLITE_FILE="$DOCROOT/var/test_db.sqlite"
        if [ ! -f "$SQLITE_FILE" ]; then
            echo "Warning: SQLite file not found: $SQLITE_FILE"
            echo "Create or restore it if you need the local test DB." >&2
        fi
        ;;
    -h|--help)
        usage
        exit 0
        ;;
    *)
        echo "Unknown driver: $DRIVER" >&2
        usage
        exit 2
        ;;
esac

echo "Starting PHP dev server (driver=$FRANCOISDB_DRIVER) on 127.0.0.1:8080"
php -S 127.0.0.1:8080 -t "$DOCROOT" > "$LOG" 2>&1 &
echo $! > "$PIDFILE"
echo "Started pid $(cat "$PIDFILE") — log: $LOG"
