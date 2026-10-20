
FROM php:8.2-apache

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo_mysql

# Habilitar mod_rewrite para las rutas de la API
RUN a2enmod rewrite

# Permitir reglas .htaccess
RUN printf '<Directory /var/www/html>\nAllowOverride All\nRequire all granted\n</Directory>\n' \
    > /etc/apache2/conf-available/aulaviva.conf \
    && a2enconf aulaviva

# Copiar la aplicación
COPY . /var/www/html/

WORKDIR /var/www/html

EXPOSE 80
