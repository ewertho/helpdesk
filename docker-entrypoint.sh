#!/bin/sh
set -e
cd /app

# Sem APP_KEY no ambiente, gera uma e guarda no volume: reiniciar não derruba as sessões.
if [ -z "$APP_KEY" ]; then
    [ -f storage/app.key ] || php -r 'echo "base64:".base64_encode(random_bytes(32));' > storage/app.key
    export APP_KEY="$(cat storage/app.key)"
fi

mkdir -p storage/app/private storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
[ -f "$DB_DATABASE" ] || touch "$DB_DATABASE"

php artisan migrate --force

# Banco novo e modo demonstração: carrega as contas e chamados de exemplo uma única vez.
if [ "$APP_DEMO" = "true" ] && [ "$(php artisan tinker --execute='echo App\Models\User::count();')" = "0" ]; then
    php artisan db:seed --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec docker-php-entrypoint "$@"
