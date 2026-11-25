# ---------- Stage 1: frontend (Vite) ----------
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json vite.config.js tailwind.config.js postcss.config.cjs ./
RUN npm ci

COPY resources ./resources
COPY public ./public
RUN npm run build && rm -f public/hot


# ---------- Stage 2: PHP + Apache ----------
FROM php:8.3-apache

# Dependências do sistema + extensões PHP exigidas pelo Laravel 11
RUN apt-get update && apt-get install -y \
    git curl zip unzip supervisor sqlite3 \
    libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache: DocumentRoot -> public/ + AllowOverride
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && printf '<Directory /var/www/html/public>\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

WORKDIR /var/www/html

# Copia código (o .dockerignore evita vendor, node_modules, .env, .git)
COPY . .
COPY --from=frontend /app/public/build ./public/build

# Frontend build pode não existir se o Vite falhar — não quebra o build
# Instala dependências PHP sem rodar scripts artisan no build (ENV ainda não existe no Coolify)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts \
    && mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache

# Supervisor: Apache + queue + scheduler no mesmo container (ideal p/ Coolify)
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

ENTRYPOINT ["entrypoint.sh"]
