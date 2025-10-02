#!/bin/bash

# Script de démarrage pour le service worker Railway
echo "Starting Laravel Queue Worker..."
echo "Current environment variables:"
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "DB_DATABASE: $DB_DATABASE"
echo "DB_USERNAME: $DB_USERNAME"

# Créer les dossiers nécessaires
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chmod -R 775 storage bootstrap/cache

# Essayer de créer les tables Laravel d'abord
echo "Creating Laravel tables..."
php artisan migrate --force 2>/dev/null || echo "Migration failed, but continuing..."

# Attendre que la base de données soit disponible avec les tables
echo "Checking database connection with tables..."
max_attempts=10
attempt=1

while [ $attempt -le $max_attempts ]; do
  echo "Database connection attempt $attempt/$max_attempts..."

  if php artisan tinker --execute="try { DB::table('users')->limit(1)->get(); echo 'Tables exist!'; } catch(Exception \$e) { echo 'Tables missing: ' . \$e->getMessage(); exit(1); }" 2>/dev/null; then
    echo "Database and tables ready!"
    break
  fi

  if [ $attempt -eq $max_attempts ]; then
    echo "Tables still missing after $max_attempts attempts, starting worker anyway..."
    break
  fi

  echo "Tables not ready, waiting 10 seconds..."
  sleep 10
  attempt=$((attempt + 1))
done

# Démarrer le worker queue
echo "Starting queue worker..."
exec php artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=60 --verbose
