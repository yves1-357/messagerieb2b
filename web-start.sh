#!/bin/bash

echo "Starting Laravel Web Application..."

# Définir les variables d'environnement
export DB_CONNECTION=mysql

# Créer les dossiers nécessaires
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chmod -R 775 storage bootstrap/cache

# Attendre que la base de données soit disponible (connexion seulement)
echo "Checking database connection..."
until php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connected';" >/dev/null 2>&1; do
  echo "Waiting for database..."
  sleep 3
done

echo "Database connected! Running migrations..."

# Exécuter les migrations d'abord
echo "Running database migrations..."
php artisan migrate --force

# Maintenant nettoyer et optimiser (après que les tables existent)
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Optimiser pour la production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer le lien symbolique pour le stockage
php artisan storage:link || true

# Démarrer le serveur avec gestion du port
PORT_NUMBER=${PORT:-8000}
echo "Starting server on port $PORT_NUMBER..."

exec php artisan serve --host=0.0.0.0 --port="$PORT_NUMBER"
