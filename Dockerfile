# ベースイメージ
FROM php:8.2-apache

# ===============================
# 1️⃣ 必要なパッケージをインストール
# ===============================
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Apacheのmod_rewriteを有効化
RUN a2enmod rewrite

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Laravelアプリをコピー
COPY . /var/www/html

# 権限設定
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ===============================
# 2️⃣ Composer install（依存関係）
# ===============================
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

# ===============================
# 3️⃣ DocumentRootをpublicに変更
# ===============================
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# ===============================
# 4️⃣ 起動設定
# ===============================
EXPOSE 8080

# ※ key:generate や migrate はビルド時ではなく実行時（Render起動後）に行う
CMD ["bash", "-c", "php artisan config:clear && php artisan key:generate --force && php artisan migrate --force && apache2-foreground"]

