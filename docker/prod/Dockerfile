# ─────────────── STAGE 1 : base ───────────────
FROM php:8.2-fpm-alpine AS base

# Install PHP extensions needed for Symfony
RUN apk add --no-cache \
    git unzip zip curl icu-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Global env for all stages
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=prod \
    SYMFONY_SKIP_SCRIPT=1

# ─────────────── STAGE 2 : build ───────────────
FROM base AS build

# Copy the project
COPY . /var/www/html

# Prevent git permission errors during composer install
RUN git config --global --add safe.directory /var/www/html

# Install PHP dependencies without dev + no auto-scripts
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Installer mc (client MinIO)
RUN curl -O https://dl.min.io/client/mc/release/linux-amd64/mc \
    && chmod +x mc \
    && mv mc /usr/local/bin/mc

# ─────────────── STAGE 3 : final image ───────────────
FROM base AS prod

COPY --from=build /var/www/html /var/www/html

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]