# Usamos una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalamos las dependencias del sistema y la extensión de PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Habilitamos el mod_rewrite de Apache (necesario para las rutas de Laravel)
RUN a2enmod rewrite

# Instalamos Composer (el equivalente a Maven en PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Nos movemos a la carpeta de trabajo
WORKDIR /var/www/html

# Copiamos todos los archivos de tu proyecto al contenedor
COPY . .

# Instalamos las dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Damos permisos a las carpetas de almacenamiento (Laravel lo necesita para funcionar)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Cambiamos la carpeta raíz de Apache a la carpeta "public" de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Exponemos el puerto 80 (Render lo detecta automáticamente)
EXPOSE 80

# Comando para ejecutar las migraciones y arrancar Apache
CMD php artisan storage:link && php artisan migrate --force && apache2-foreground