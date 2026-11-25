#!/bin/bash
set -e

cd /var/www/html

# Garante permissões (volume persistente do Coolify pode vir como root)
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Link do storage (idempotente)
php artisan storage:link 2>/dev/null || true

# Aguarda banco subir (Coolify sobe DB junto; tenta por ~60s)
echo ">> Aguardando banco de dados..."
for i in $(seq 1 30); do
  if php artisan migrate:status >/dev/null 2>&1; then
    echo ">> Banco acessível."
    break
  fi
  echo "   tentativa $i/30..."
  sleep 2
done

# Migrations SEMPRE no boot (seguro p/ deploy contínuo)
echo ">> Rodando migrations..."
php artisan migrate --force || echo "!! migrate falhou, verifique DB_HOST/DB_PASSWORD"

# Cache de performance (só em produção)
if [ "$APP_ENV" = "production" ]; then
  echo ">> Otimizando caches..."
  php artisan optimize:clear
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
fi

echo ">> Iniciando supervisord (apache + queue + scheduler)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
