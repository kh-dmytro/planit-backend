# Используем официальный образ PHP с Apache
FROM php:8.1-apache

# Устанавливаем расширения PHP и необходимые зависимости
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем проект в директорию /var/www/html
COPY . /var/www/html

# Устанавливаем права и запускаем установку зависимостей
WORKDIR /var/www/html
RUN composer install

# Копируем файл настроек и генерируем ключ приложения Laravel
RUN cp .env.example .env
RUN php artisan key:generate

# Указываем порт, на котором будет работать приложение
EXPOSE 80
