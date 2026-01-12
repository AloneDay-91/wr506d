#!/bin/bash
set -e

echo "🚀 Starting application setup..."

# Attendre que la base de données soit prête (optionnel mais recommandé)
echo "⏳ Waiting for database..."
timeout=60
while ! php bin/console dbal:run-sql "SELECT 1" > /dev/null 2>&1; do
    timeout=$((timeout - 1))
    if [ $timeout -le 0 ]; then
        echo "⚠️  Database not ready, continuing anyway..."
        break
    fi
    sleep 1
done

# Installer les assets
echo "📦 Installing assets..."
php bin/console assets:install public --no-interaction --env=prod || echo "⚠️  Assets install failed, continuing..."

# Nettoyer et générer le cache avec les bonnes permissions
echo "🔥 Warming up cache..."
php bin/console cache:clear --env=prod --no-debug || echo "⚠️  Cache clear failed"
php bin/console cache:warmup --env=prod --no-debug || echo "⚠️  Cache warmup failed"

# S'assurer que les permissions sont correctes
echo "🔒 Setting permissions..."
chown -R www-data:www-data var/ public/uploads public/bundles 2>/dev/null || true
chmod -R 777 var/cache var/log var/sessions 2>/dev/null || true

echo "✅ Application ready!"

# Démarrer Apache
exec apache2-foreground