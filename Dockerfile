FROM php:8.2-apache

# 安裝系統依賴
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip curl git \
    && docker-php-ext-install pdo_mysql zip

# 啟用 Apache rewrite 模組
RUN a2enmod rewrite

# 設定 Apache 的 Document Root 為 Laravel public 目錄
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 複製 Laravel 專案
COPY . /var/www/html

# 設定工作目錄
WORKDIR /var/www/html

# 安裝 Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# 安裝 Laravel 相依
RUN composer install --no-interaction --prefer-dist --optimize-autoloader || true

# 設定 Laravel 所需目錄的權限
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
