#!/bin/bash

echo "Starting Laravel Web Application..."

# Définir les variables d'environnement
export DB_CONNECTION=mysql

# Créer les dossiers nécessaires
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chmod -R 775 storage bootstrap/cache

# Nettoyer et optimiser
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Attendre que la base de données soit disponible
echo "Checking database connection..."
until php artisan migrate --dry-run >/dev/null 2>&1; do
  echo "Waiting for database..."
  sleep 3
done

# Exécuter les migrations
echo "Running database migrations..."
php artisan migrate --force

# Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Démarrer le serveur avec gestion du port
PORT_NUMBER=${PORT:-8000}
echo "Starting server on port $PORT_NUMBER..."

exec php artisan serve --host=0.0.0.0 --port="$PORT_NUMBER"
