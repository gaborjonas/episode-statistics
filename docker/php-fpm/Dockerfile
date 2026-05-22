FROM php:8.5-fpm AS php_upstream

FROM php_upstream AS base
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    uuid \
    intl \
    ctype \
    iconv \
    mbstring \
    pdo_pgsql \
    amqp

EXPOSE 9000

FROM base AS dev

ENV COMPOSER_ALLOW_SUPERUSER=1
ARG USER_ID=1000
ARG GROUP_ID=1000

RUN groupadd -g ${GROUP_ID} devuser && \
    useradd -l -u ${USER_ID} -g devuser -m devuser

RUN install-php-extensions xdebug

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY docker/php-fpm/xdebug.ini "$PHP_INI_DIR/conf.d/xdebug.ini"
COPY --chmod=0755 /docker/php-fpm/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chown devuser:devuser /var/www/html
USER devuser

CMD ["php-fpm"]

FROM base AS builder
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.json

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader

FROM base AS prod

COPY . .
COPY --from=builder /var/www/html/vendor ./vendor
COPY ./docker/php-fpm/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"
COPY --chmod=0755 ./docker/php-fpm/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN rm /usr/local/bin/install-php-extensions /var/www/html/composer.json

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
