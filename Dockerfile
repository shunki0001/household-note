# ベースイメージとしてPHPとApacheを使用
FROM php:8.2-apache

# 作業ディレクトリ
WORKDIR /var/www/html

# システムパッケージインストール
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install pdo_mysql zip mbstring

# Composerインストール
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# プロジェクトファイルをコピー
COPY . .

# Laravelの依存関係をインストール
RUN composer install --no-dev --optimize-autoloader

# パーミッション調整（storageとbootstrap/cache）
RUN chown -R www-data:www-data storage bootstrap/cache

# ApacheのドキュメントルートをLaravelのpublicに設定
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN a2enmod rewrite

