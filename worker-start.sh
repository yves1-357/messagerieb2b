#!/bin/bash

# Script de démarrage pour le service worker Railway
echo "Starting Laravel Queue Worker..."
echo "Current environment variables:"
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "DB_DATABASE: $DB_DATABASE"
echo "DB_USERNAME: $DB_USERNAME"

# Attendre que la base de données soit disponible avec un timeout
echo "Checking database connection..."
max_attempts=30
attempt=1

while [ $attempt -le $max_attempts ]; do
  echo "Database connection attempt $attempt/$max_attempts..."

  if php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Connected!'; } catch(Exception \$e) { echo 'Failed: ' . \$e->getMessage(); exit(1); }" 2>/dev/null; then
    echo "Database connected successfully!"
    break
  fi

  if [ $attempt -eq $max_attempts ]; then
    echo "Failed to connect to database after $max_attempts attempts"
    echo "Checking if MySQL service is available..."
    php artisan tinker --execute="echo 'DB Config: '; print_r(config('database.connections.mysql'));"
    exit 1
  fi

  echo "Database not ready, waiting 10 seconds..."
  sleep 10
  attempt=$((attempt + 1))
done

# Démarrer le worker queue
echo "Starting queue worker..."
exec php artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=60 --verbose
