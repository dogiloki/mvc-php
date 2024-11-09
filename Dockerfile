# Dockerfile para deploy en railway, usando apache

# Usar una imagen base de PHP con Apache
FROM php:8.1-apache

# Instalar dependencias necesarias para Composer y PHP
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

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

# Copiar todos los archivos del proyecto al directorio raíz de Apache
COPY . /var/www/html/

# Copy the custom Nginx config file into de continer
COPY docker/apache/default.conf /etc/apache2/sites-available/000-default.conf

# Set the global ServerName to suppress the warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Ejecutar Composer install para instalar las dependencias con más detalles
WORKDIR /var/www/html
RUN composer install --no-interaction --optimize-autoloader -vvv

# Habilitar mod_rewrite (común en aplicaciones PHP como Laravel)
RUN a2enmod rewrite

# Exponer el puerto 80
EXPOSE 80