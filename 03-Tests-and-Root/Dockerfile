FROM php:8.2-cli AS vendor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN apt-get update && apt-get install -y --no-install-recommends git unzip libonig-dev && docker-php-ext-install mbstring && rm -rf /var/lib/apt/lists/*
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction --optimize-autoloader
FROM php:8.2-apache
RUN apt-get update && apt-get install -y --no-install-recommends libsqlite3-dev libonig-dev && docker-php-ext-install pdo_mysql pdo_sqlite mbstring && a2enmod rewrite && rm -rf /var/lib/apt/lists/*
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
ENV APP_ENV=production APP_DEBUG=false LOG_CHANNEL=stderr SESSION_DRIVER=file CACHE_STORE=file QUEUE_CONNECTION=sync
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf && chown -R www-data:www-data storage bootstrap/cache
EXPOSE 80

