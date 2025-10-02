FROM php:8.2-fpm

# Arguments définis dans docker-compose.yml
ARG user
ARG uid

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Installer une version récente de Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Vérifier la version de Node.js
RUN node -v

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Obtenir dernière version de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Créer un utilisateur système pour exécuter Composer et Artisan
RUN useradd -G www-data,root -u 1000 -d /home/www-data www-data
RUN mkdir -p /home/www-data/.composer && \
    chown -R www-data:www-data /home/www-data

# Définir le répertoire de travail
WORKDIR /var/www

# Copier les fichiers d'application
COPY . /var/www/

# Changer les permissions
RUN chown -R www-data:www-data /var/www

# Installer les dépendances PHP et JS
USER www-data
RUN composer install --no-interaction --prefer-dist
RUN npm ci && npm run build

# Exposer le port 9000
EXPOSE 9000

# Démarrer le serveur PHP-FPM
CMD ["php-fpm"]