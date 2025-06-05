# Usa la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instala extensiones necesarias (puedes agregar más según tus necesidades)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia el código fuente a la carpeta del servidor web
COPY . /var/www/html/

# Da permisos correctos (opcional)
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto 80
EXPOSE 80
