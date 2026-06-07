FROM php:8.3-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev

# extensions PHP essentielles
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

CMD ["sleep", "infinity"]