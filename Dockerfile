FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html
WORKDIR /var/www/html

RUN chmod -R 775 storage bootstrap/cache

# 依存関係インストール
RUN composer install --no-dev --optimize-autoloader

# DocumentRootをpublicに変更
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

EXPOSE 8080

# CMDで起動。key:generate は不要
CMD ["bash", "-c", "php artisan config:clear && php artisan migrate --force && apache2-foreground"]

