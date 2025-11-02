# QuickChat - Application de messagerie instantanée

QuickChat est une application web de messagerie instantanée moderne, développée avec Laravel et Vue.js, permettant de communiquer facilement et en toute sécurité avec vos proches, collègues ou groupes de travail.

## Fonctionnalités principales

- **Messages instantanés et sécurisés** : Envoyez et recevez des messages en temps réel grâce à une interface fluide et moderne.
- **Notifications en temps réel** : Recevez des notifications instantanées lors de la réception de nouveaux messages.
- **Groupes et canaux de discussion** : Créez des groupes pour échanger à plusieurs, partagez des fichiers, images et liens.
- **Gestion des contacts** : Recherchez des utilisateurs, démarrez de nouvelles conversations, ajoutez des contacts.
- **Profil utilisateur** : Personnalisez votre profil, modifiez votre nom d'utilisateur, gérez vos paramètres et votre sécurité.
- **Interface responsive** : Profitez d'une expérience optimale sur ordinateur comme sur mobile.
- **Sécurité** : Authentification sécurisée, gestion des sessions, et respect de la vie privée.

## Technologies utilisées

- **Backend** : Laravel (PHP)
- **Frontend** : Vue.js
- **Notifications temps réel** : Pusher
- **Base de données** : MySQL
- **Autres** : Axios, Inertia.js, Tailwind CSS

## Installation

1. Clonez le dépôt :
	git clone https://github.com/yves1-357/messagerieb2b.git
2. Installez les dépendances backend :
	composer install
3. Installez les dépendances frontend :
	npm install
4. Configurez votre fichier `.env` et générez la clé d'application :
	cp .env.example .env
	php artisan key:generate
5. Lancez le serveur de développement :
	php artisan serve
	npm run dev

## Déploiement sur Railway

L'application est déployée automatiquement sur Railway, une plateforme cloud moderne pour héberger des applications web et des bases de données.

- **Accès en ligne** : [https://web-production-f9b8.up.railway.app/]
- Le déploiement utilise les fichiers `railway.json`, `web-start.sh` et `worker-start.sh` pour automatiser la configuration, la build des assets et le lancement des workers Laravel.
- Chaque push sur la branche principale déclenche un déploiement automatique.

Pour déployer sur votre propre Railway :
1. Créez un projet sur [Railway](https://railway.app/).
2. Connectez votre dépôt GitHub.
3. Ajoutez les variables d'environnement nécessaires dans le dashboard Railway.
4. Les scripts de démarrage (`web-start.sh` et `worker-start.sh`) gèrent la préparation de l'environnement, la build, les migrations et le lancement de l'application.

## Auteurs

- yves1-357
- RandyKoke
