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

# Installer les assets avec symlinks en premier, puis copie en fallback
echo "📦 Installing assets..."
if php bin/console assets:install public --symlink --relative --no-interaction --env=prod 2>/dev/null; then
    echo "✅ Assets installed with symlinks"
else
    echo "⚠️  Symlink failed, trying copy..."
    php bin/console assets:install public --no-interaction --env=prod || echo "⚠️  Assets install failed"
fi

# Compiler les assets avec AssetMapper
echo "🔨 Compiling assets with AssetMapper..."
if php bin/console asset-map:compile --env=prod 2>&1; then
    echo "✅ AssetMapper compilation successful"
else
    echo "⚠️  AssetMapper compilation failed (not critical for API Platform)"
fi

# Installer importmap
echo "📥 Installing importmap..."
if php bin/console importmap:install --env=prod 2>&1; then
    echo "✅ Importmap installed"
else
    echo "⚠️  Importmap install failed (not critical)"
fi

# Vérifier que les assets sont bien là
if [ -d "public/bundles/apiplatform" ]; then
    echo "✅ API Platform assets found in public/bundles/"
fi

if [ -d "public/assets" ]; then
    echo "✅ Compiled assets found in public/assets/"
    ls -la public/assets/ | head -5
else
    echo "⚠️  No compiled assets in public/assets/ (may be normal)"
fi

# Nettoyer et générer le cache avec les bonnes permissions
echo "🔥 Warming up cache..."
php bin/console cache:clear --env=prod --no-debug || echo "⚠️  Cache clear failed"
php bin/console cache:warmup --env=prod --no-debug || echo "⚠️  Cache warmup failed"

# S'assurer que les permissions sont correctes
echo "🔒 Setting permissions..."
chown -R www-data:www-data var/ public/uploads public/bundles public/assets 2>/dev/null || true
chmod -R 755 public/bundles public/assets 2>/dev/null || true
chmod -R 777 var/cache var/log var/sessions 2>/dev/null || true

echo "✅ Application ready!"

# Démarrer Apache
exec apache2-foreground
