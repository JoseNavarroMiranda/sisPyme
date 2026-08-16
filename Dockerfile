FROM php:8.3-cli-alpine

RUN apk add --no-cache libpq-dev \
    && docker-php-ext-install pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN composer dump-autoload --optimize

EXPOSE 8000

CMD ["sh", "-c", "php artisan storage:link || true && php artisan serve --host=0.0.0.0 --port=8000"]