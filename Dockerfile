# Stage 1 — Composer build
FROM composer:2 AS build
WORKDIR /app
COPY . .
RUN composer install --no-dev --prefer-dist --no-scripts
RUN composer run-script post-autoload-dump

# Stage 2 — PHP-FPM
FROM php:8.2-fpm-alpine
RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev zip unzip bash git
RUN docker-php-ext-install pdo pdo_mysql intl zip mbstring bcmath pcntl

WORKDIR /var/www/html
COPY --from=build /app /var/www/html
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
