FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction
COPY . .
RUN composer dump-autoload --optimize --no-dev

# FrankenPHP: servidor de aplicação PHP moderno (Caddy embutido), um processo só.
FROM dunglas/frankenphp:php8.5-alpine
ENV SERVER_NAME=:80 \
    APP_ENV=production \
    APP_DEBUG=false \
    DB_DATABASE=/app/storage/database.sqlite
COPY --from=vendor /app /app
COPY docker-entrypoint.sh /usr/local/bin/helpdesk-entrypoint
RUN chmod +x /usr/local/bin/helpdesk-entrypoint
VOLUME /app/storage
ENTRYPOINT ["helpdesk-entrypoint"]
CMD ["--config", "/etc/frankenphp/Caddyfile", "--adapter", "caddyfile"]
