
FROM php:8.2-apache


RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql


RUN a2enmod rewrite


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


COPY . /var/www/html/


WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html


RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress


EXPOSE 80


CMD ["apache2-foreground"]
