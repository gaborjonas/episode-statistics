#!/bin/sh
set -e

mkdir -p var
if [ "$(id -u)" = "0" ]; then
    chown www-data:www-data var
fi
php bin/console cache:warmup
php bin/console doctrine:migrations:migrate --no-interaction

exec "$@"
