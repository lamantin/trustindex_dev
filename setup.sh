#!/bin/bash

echo "🚀 Trustindex Mini Projekt Inicializálása..."

# 1. Függőségek telepítése
echo "📦 Composer függőségek telepítése..."
composer install

# 2. Adatbázis mappa és fájl előkészítése
echo "📂 Adatbázis környezet előkészítése..."
mkdir -p var
touch var/data.db

# 3. Adatbázis sémák felépítése a meglévő migrációkból
echo "🗄️ Migrációk futtatása..."
php bin/console doctrine:migrations:migrate --no-interaction

# 4. Tesztek ellenőrzése
echo "🧪 Tesztcsomag futtatása ellenőrzésképp..."
php bin/phpunit

echo "✅ Minden kész! Indíthatod a szervert a következő paranccsal:"
echo "👉 php -S 127.0.0.1:8000 -t public"