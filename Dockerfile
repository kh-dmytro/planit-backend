# Используем официальный образ PHP с FPM
FROM php:8.1-fpm

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Устанавливаем рабочую директорию
WORKDIR /var/www

# Копируем файлы проекта
COPY . /var/www

# Увеличиваем таймаут для Composer, если необходимо
RUN composer config --global process-timeout 600

# Устанавливаем зависимости Laravel
RUN composer install --prefer-dist --no-scripts --no-progress

# Генерируем ключ приложения
RUN php artisan key:generate

# Настраиваем доступ к директории storage и bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Команда для запуска Laravel и выполнения миграций
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT}
