FROM node:20-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./

RUN npm run build

FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libonig-dev \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install \
    bcmath \
    intl \
    mbstring \
    pdo_pgsql \
    pgsql \
    zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY storage ./storage
COPY artisan ./
COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY --from=frontend /app/public/build ./public/build
COPY docker/start.sh /usr/local/bin/start-render.sh

RUN chmod +x /usr/local/bin/start-render.sh \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf

EXPOSE 10000

CMD ["start-render.sh"]
