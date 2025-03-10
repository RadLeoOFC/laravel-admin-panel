# Using PHP 8.2 with FPM
FROM php:8.2-fpm

# Installing dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    zip unzip git curl libpng-dev libonig-dev libxml2-dev sqlite3 libsqlite3-dev \
    libzip-dev libicu-dev g++ supervisor \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring gd opcache bcmath zip pcntl intl

# Installing Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Setting the working directory
WORKDIR /var/www/html

# Fixing "dubious ownership" error when working with Git
RUN git config --global --add safe.directory /var/www/html

# Copying Laravel files into the container
COPY . .

# Removing the vendor folder (if any) to avoid conflicts
RUN rm -rf vendor

# Installing Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Copying the environment file and generating the application key
COPY .env.example .env
RUN php artisan key:generate

# Setting permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copying Supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Running Supervisor
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
