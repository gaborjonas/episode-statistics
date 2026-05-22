#!/bin/sh
set -e

mkdir -p var
chown www-data:www-data var
php bin/console cache:warmup
php bin/console doctrine:migrations:migrate --no-interaction

exec "$@"
