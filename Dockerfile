# Usa la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instala extensiones necesarias para Symfony y MySQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql

# Habilita mod_rewrite de Apache
RUN a2enmod rewrite

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Crea un usuario "www-data" y ejecuta todo como ese usuario
RUN useradd -m symfony
WORKDIR /var/www/html
COPY . /var/www/html
RUN chown -R symfony:symfony /var/www/html
USER symfony

# Ejecuta Composer como el usuario "symfony"
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Vuelve a usar root solo para ejecutar Apache
USER root
EXPOSE 80
CMD ["apache2-foreground"]
