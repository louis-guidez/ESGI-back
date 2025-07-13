# Base PHP-FPM
FROM php:8.2-fpm-alpine AS base

RUN apk add --no-cache \
    git unzip zip curl libzip-dev icu-dev oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Build Symfony
FROM base AS build
COPY . /var/www/html
RUN composer install --no-dev --optimize-autoloader \
 && php bin/console cache:clear --env=prod \
 && php bin/console cache:warmup --env=prod

# Final image
FROM base AS prod
COPY --from=build /var/www/html /var/www/html
WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
