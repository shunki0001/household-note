#!/usr/bin/env bash
echo "Running composer"
composer install --no-dev --working-dir=/var/www/html

echo "Caching config..."
./vendor/bin/sail artisan config:cache

echo "Caching routes..."
./vendor/bin/sail artisan route:cache

echo "Running migrations..."
./vendor/bin/sail artisan migrate --force

