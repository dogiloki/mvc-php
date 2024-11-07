# Usamos una imagen oficial de PHP como base
FROM php:8.1-fpm

# Instalamos las dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

# Instalamos Composer (gestor de dependencias de PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configuración del directorio de trabajo
WORKDIR /var/www

# Copiamos los archivos de la aplicación al contenedor
COPY . .

# Instalamos las dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Aseguramos que el directorio de almacenamiento tenga los permisos adecuados
# RUN chown -R www-data:www-data /var/www/storage

# Exponemos el puerto 9000 para el servidor PHP-FPM
EXPOSE 9000

# Definimos el comando por defecto para ejecutar PHP-FPM
CMD ["php-fpm"]
