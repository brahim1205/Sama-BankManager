#!/bin/sh
set -e

echo "🚀 Démarrage de l'application Laravel..."

# Nettoyer tous les caches
echo "🧹 Nettoyage des caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Attendre que la base de données soit prête avec une méthode compatible
echo "⏳ Vérification de la connexion à la base de données..."
MAX_TRIES=30
COUNT=0
until php artisan db:show > /dev/null 2>&1 || [ $COUNT -eq $MAX_TRIES ]; do
    echo "Base de données non disponible - tentative $COUNT/$MAX_TRIES"
    COUNT=$((COUNT + 1))
    sleep 2
done

if [ $COUNT -eq $MAX_TRIES ]; then
    echo "❌ Impossible de se connecter à la base de données après $MAX_TRIES tentatives"
    echo "Vérifiez vos variables d'environnement DB_*"
    # Continue quand même pour voir les vraies erreurs dans les logs
fi

# Générer la clé si elle n'existe pas
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Génération de la clé d'application..."
    php artisan key:generate --force
fi

# Exécuter les migrations
echo "📊 Exécution des migrations..."
php artisan migrate --force || echo "⚠️  Erreur lors des migrations"

# Installer Passport (clés de cryptage)
echo "🔐 Installation de Passport..."
if [ ! -f "storage/oauth-private.key" ] || [ ! -f "storage/oauth-public.key" ]; then
    echo "Génération des clés Passport..."
    php artisan passport:keys --force
else
    echo "Clés Passport déjà existantes"
fi

# Créer les clients Passport si nécessaire
echo "👥 Configuration des clients Passport..."
php artisan passport:client --personal --no-interaction --name="Personal Access Client" || echo "Client personnel déjà existant"
php artisan passport:client --password --no-interaction --name="Password Grant Client" || echo "Client password déjà existant"

# Régénérer les caches en production
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimisation pour la production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Afficher les informations de démarrage
echo "✅ Application prête!"
echo "📍 URL: $APP_URL"
echo "🌍 Environnement: $APP_ENV"
echo "🔌 Port: ${PORT:-8000}"

# Démarrer le serveur
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}