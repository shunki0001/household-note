# ベースイメージとしてPHPとApacheを使用# ベースイメージ
FROM php:8.2-apache

# システム依存パッケージ + PostgreSQL拡張をインストール
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Apacheのmod_rewriteを有効化
RUN a2enmod rewrite

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Laravelアプリをコピー
COPY . /var/www/html

# 権限設定
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Composerで依存関係インストール
RUN composer install --no-dev --optimize-autoloader

# Laravelキャッシュを生成
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# DocumentRootをpublicに変更
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# ポート公開
EXPOSE 8080

# サーバー起動
CMD ["apache2-foreground"]

