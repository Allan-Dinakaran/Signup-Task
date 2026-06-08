FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libssl-dev \
    pkg-config \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install mongodb redis igbinary && \
    docker-php-ext-enable mongodb redis igbinary && \
    docker-php-ext-install mysqli

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/

WORKDIR /var/www/html

RUN composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-reqs

RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["bash", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-80}/g\" /etc/apache2/ports.conf && sed -i \"s/*:80>/*:${PORT:-80}>/g\" /etc/apache2/sites-enabled/000-default.conf && apache2-foreground"]
