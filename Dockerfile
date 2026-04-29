FROM php:8.2-fpm

# 1. Установка системных зависимостей + Node.js (для фронтенда)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 2. Очистка кэша apt (уменьшает размер образа)
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Установка PHP расширений (включая xml и dom для парсера ноутбуков)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip xml dom

# 4. Установка Composer (официальный образ)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Установка рабочей директории
WORKDIR /var/www

# 6. Копирование файлов проекта
COPY . .

# 7. Установка зависимостей PHP
RUN composer install --no-interaction --optimize-autoloader

# 8. Установка прав (ВАЖНО для Laravel и Livewire)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 9. Открываем порт для связи с Nginx
EXPOSE 9000

CMD ["php-fpm"]
