FROM php:8.4-fpm
# Install system packages

RUN apt-get update && apt-get install -y \
    curl \
    ca-certificates

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

RUN node -v
RUN npm -v


RUN apt-get update && apt-get install -y nginx
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        intl \
        bcmath \
        exif \
        gd \
        zip
# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./


COPY . .

WORKDIR /var/www/html

COPY . .

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader 

RUN npm install
RUN npm run build

RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

COPY docker/nginx/default.conf /etc/nginx/sites-available/default


COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 10000

CMD ["/start.sh"]