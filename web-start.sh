#!/bin/bash

echo "Starting Laravel Web Application..."

# Définir les variables d'environnement
export DB_CONNECTION=mysql

# Créer les dossiers nécessaires
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
chmod -R 775 storage bootstrap/cache

# Builder les assets frontend
echo "=== DEBUGGING ASSETS BUILD ==="
echo "Node version: $(node --version)"
echo "NPM version: $(npm --version)"
echo "Current directory: $(pwd)"
echo "Checking package.json..."
if [ -f "package.json" ]; then
    echo "✅ package.json exists"
    cat package.json | grep -A 5 -B 5 '"scripts"'
else
    echo "❌ package.json missing!"
fi

echo "Checking node_modules..."
if [ -d "node_modules" ]; then
    echo "✅ node_modules exists"
else
    echo "❌ node_modules missing! Installing..."
    npm install
fi

# Nettoyer avant de builder
echo "Cleaning previous builds..."
rm -rf public/build
rm -rf node_modules/.vite

# Build avec verbose
echo "Running Vite build..."
npm run build 2>&1 | tee build.log

echo "=== BUILD COMPLETED ==="

# Vérifier que les assets ont été générés
if [ -d "public/build" ]; then
    echo "✅ Assets directory exists"
    echo "Contents of public/build:"
    ls -la public/build/
    echo "=== Manifest content ==="
    if [ -f "public/build/manifest.json" ]; then
        cat public/build/manifest.json
    else
        echo "❌ manifest.json missing!"
    fi
else
    echo "❌ Assets directory missing!"
    echo "Build log contents:"
    cat build.log || echo "No build log found"
fi

echo "=== END ASSETS DEBUG ==="

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
