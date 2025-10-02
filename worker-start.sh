#!/bin/bash

# Script de démarrage pour le service worker Railway
echo "Starting Laravel Queue Worker..."

# Attendre que la base de données soit disponible
echo "Waiting for database connection..."
until php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; do
  echo "Database not ready, waiting..."
  sleep 3
done

echo "Database connected successfully!"

# Démarrer le worker queue
echo "Starting queue worker..."
exec php artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=60 --verbose
