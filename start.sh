#!/bin/bash

# Exécuter les migrations
php artisan migrate --force

# Cacher les configurations pour de meilleures performances
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer le lien symbolique pour le stockage
php artisan storage:link || true

# Définir les permissions des dossiers
chmod -R 775 storage bootstrap/cache

echo "Application Laravel prête à démarrer!"
