#!/usr/bin/env bash
set -euo pipefail

# Ensure runtime dirs
mkdir -p /run/php

# Wait for database if DATABASE_URL provided
if [[ -n "${DATABASE_URL:-}" ]]; then
  echo "DATABASE_URL is set; attempting to parse and wait for DB"
  proto="${DATABASE_URL%%://*}"
  rest="${DATABASE_URL#*://}"
  creds_host_port_db="${rest%%\?*}"
  userpass="${creds_host_port_db%@*}"
  host_port_db="${creds_host_port_db#*@}"
  host_port="${host_port_db%%/*}"
  dbname="${host_port_db#*/}"
  host="${host_port%%:*}"
  port="${host#*:}"
  port="${port:-5432}"
  echo "Waiting for Postgres at $host:$port..."
  for i in {1..60}; do
    if (echo > /dev/tcp/$host/$port) >/dev/null 2>&1; then
      echo "Postgres is up"; break; fi
    echo "...retry $i"; sleep 1
  done
fi

# Run migrations and optimizations (ignore failures during first boot without DB)
php artisan migrate --force || echo "migrate skipped"
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec "$@"
