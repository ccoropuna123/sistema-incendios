# === ETAPA 1: Compilar estilos con Node ===
FROM node:18-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# === ETAPA 2: Servidor Apache con PHP ===
FROM php:8.2-apache

# Instalar extensiones de PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Habilitar mod_rewrite de Apache para Laravel
RUN a2enmod rewrite

# Cambiar la raíz de Apache a la carpeta /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copiar el proyecto al contenedor
COPY . /var/www/html

# Traer los archivos compilados por Vite desde la Etapa 1
COPY --from=frontend /app/public/build /var/www/html/public/build

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Configurar permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Crear un archivo de SQLite vacío en caso de que no exista
RUN touch /var/www/html/database/database.sqlite \
    && chown -R www-data:www-data /var/www/html/database

EXPOSE 80
