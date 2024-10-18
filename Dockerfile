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
# Копируем настройки Apache
COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf


# Устанавливаем права и устанавливаем зависимости Laravel
WORKDIR /var/www/html

# Проверяем, установлен ли composer.json
RUN if [ -f composer.json ]; then composer install; fi

RUN a2enmod rewrite


# Убедимся, что .env.example существует перед копированием
#RUN cp .env.example .env && php artisan key:generate

# Указываем порт, на котором будет работать приложение
EXPOSE 80
