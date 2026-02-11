FROM php:8.3-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    pdo_mysql \
    mysqli \
    zip \
    exif \
    pcntl \
    bcmath \
    gd

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Очистка кэша apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка рабочей директории
WORKDIR /var/www/html

# Копируем существующие файлы проекта (на этапе сборки пусто, но примонтируется позже)
COPY ./backend /var/www/html

# Устанавливаем права
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]