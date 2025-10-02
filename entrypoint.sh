#!/bin/bash

# Assurer que les variables d'environnement MySQL sont définies
export DB_CONNECTION=mysql

# Créer les dossiers nécessaires et définir les permissions
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chmod -R 775 storage bootstrap/cache

# Attendre que la base de données soit disponible
echo "Waiting for database to be ready..."
until php artisan migrate --dry-run 2>/dev/null; do
  echo "Database not ready yet, waiting..."
  sleep 2
done

# Exécuter les migrations
echo "Running migrations..."
php artisan migrate --force

echo "Starting application..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
