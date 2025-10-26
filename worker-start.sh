#!/bin/bash

echo "🚀 Starting Laravel Queue Worker..."

# Créer les dossiers nécessaires
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chmod -R 775 storage bootstrap/cache

# Attendre la base de données
echo "⏳ Waiting for database..."
until php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; do
  echo "Database not ready, retrying in 5s..."
  sleep 5
done

echo "✅ Database connected!"

# Vider les caches
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

echo "🔄 Starting queue worker..."

# Démarrer le worker avec des logs verbeux
exec php artisan queue:work database --verbose --tries=3 --timeout=90 --sleep=3
