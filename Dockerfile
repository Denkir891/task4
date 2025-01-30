# Usa una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instala extensiones necesarias para Symfony y MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilita mod_rewrite de Apache (necesario para Symfony)
RUN a2enmod rewrite

# Copia los archivos del proyecto al contenedor
COPY . /var/www/html/

# Configura permisos y directorio de trabajo
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala las dependencias de Symfony
RUN composer install --no-dev --optimize-autoloader

# Expone el puerto 80 para el servidor web
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]
