# ベースイメージ
FROM php:8.2-apache

# ===============================
# 1.必要なパッケージをインストール
# ===============================
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql bcmath

# Apache設定
RUN a2enmod rewrite

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# アプリをコピー
COPY . /var/www/html
WORKDIR /var/www/html

# ===============================
# 2.依存関係をインストール
# ===============================
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# ===============================
# 3.権限設定
# ===============================
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ===============================
# 4.Apache設定
# ===============================
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

EXPOSE 8080

# ===============================
# 5.起動時コマンド
# ===============================
CMD ["bash", "-c", "php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear && php artisan migrate --force && apache2-foreground"]
