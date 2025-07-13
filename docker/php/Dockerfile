FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Commande au démarrage du conteneur
CMD composer install --no-interaction && php bin/console doctrine:migrations:migrate --no-interaction && php -S 0.0.0.0:8000 -t public
