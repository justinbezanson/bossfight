FROM composer:latest AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --no-dev \
    --no-scripts \
    --optimize-autoloader


FROM composer:latest AS wayfinder

WORKDIR /app

COPY --from=vendor /app/vendor ./vendor
COPY . .

RUN cp .env.example .env && \
    php artisan key:generate --force && \
    touch database/database.sqlite && \
    php artisan wayfinder:generate --with-form && \
    rm -f .env database/database.sqlite


FROM node:22-bookworm-slim AS frontend

RUN apt-get update -qq && \
    apt-get install -y -qq \
        ca-certificates \
        curl \
        gnupg \
    && curl -sSL https://packages.sury.org/php/apt.gpg | gpg --dearmor -o /usr/share/keyrings/sury.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/sury.gpg] https://packages.sury.org/php/ bookworm main" > /etc/apt/sources.list.d/sury.list \
    && apt-get update -qq \
    && apt-get install -y -qq \
        php8.3-cli \
        php8.3-common \
        php8.3-curl \
        php8.3-mbstring \
        php8.3-sqlite3 \
        php8.3-xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

COPY --from=wayfinder /app/resources/js/actions /app/resources/js/actions
COPY --from=wayfinder /app/resources/js/routes /app/resources/js/routes
COPY --from=wayfinder /app/resources/js/wayfinder /app/resources/js/wayfinder
COPY --from=vendor /app/vendor ./vendor

RUN cp .env.example .env && \
    php artisan key:generate --force && \
    touch database/database.sqlite

RUN npm run build && \
    rm -f .env database/database.sqlite


FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx supervisor sqlite-dev && \
    docker-php-ext-install pdo_sqlite && \
    apk del sqlite-dev

COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN chown -R www-data:www-data storage bootstrap/cache database && \
    chmod +x /usr/local/bin/docker-entrypoint

EXPOSE 80

ENTRYPOINT ["docker-entrypoint"]
CMD ["supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
