#!/bin/bash
cd /home/u597256450/domains/barbercontrolpnz.shop/public_html || exit 1
PHP=/opt/alt/php83/usr/bin/php
git pull origin main
$PHP /usr/local/bin/composer install --no-dev --optimize-autoloader
$PHP artisan migrate --force
$PHP artisan storage:link
$PHP artisan optimize:clear
$PHP artisan config:cache
