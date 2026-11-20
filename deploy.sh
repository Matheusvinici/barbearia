#!/bin/bash
cd /home/u597256450/domains/barbercontrolpnz.shop/public_html || exit 1
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
