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
RUN composer install --no-dev --optimize-autoloader

# APP_KEYの自動生成とキャッシュクリア
RUN php artisan key:generate --force
RUN php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

RUN php artisan migrate --force

# Laravelキャッシュを生成
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# ===============================
# 3️⃣ DocumentRootをpublicに変更
# ===============================
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# ポート公開
EXPOSE 8080

# ===============================
# 4️⃣ Apache起動
# ===============================
CMD ["apache2-foreground"]

