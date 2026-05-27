# AGENTS.md

## Project
Laravel 11 barbershop management system (`barbearia` DB) with WhatsApp bot integration. Full financial control.

## Auth
- **guard `web`**: Admin users (full access) — `App\Models\User`
- **guard `barbeiro`**: Barbers (limited to own appointments) — `App\Models\Barbeiro`

Barbers log in at `/barbeiro/login`. Admin at `/login`.

## Commands

| Command | Purpose |
|---------|---------|
| `composer dev` | Runs server + queue + logs + Vite concurrently |
| `php artisan migrate` | Run migrations |
| `php artisan db:seed --class=DatabaseSeeder` | Seed admin user + settings |
| `npm run build` / `npm run dev` | Vite asset build |
| `php artisan notifications:table` | Create notifications migration |
| `php artisan test` / `vendor/bin/phpunit` | Run tests |

## Architecture

- **Routes**: `routes/web.php` (admin + barber) and `routes/api.php` (WhatsApp bot API)
- **Admin namespace**: `App\Http\Controllers\Admin\`
- **Barber namespace**: `App\Http\Controllers\Barbeiro\`
- **Bot namespace**: `App\Http\Controllers\Bot\`
- **Layout**: AdminLTE 4 / Bootstrap 5
- **Queue**: `database` driver (jobs table)
- **Notifications**: Laravel Database Notifications (bell icon in AdminLTE)

## WhatsApp Bot

- **Project**: `whatsapp-bot/` (Node.js + whatsapp-web.js + Express)
- **Flow**: Barber → Day → Time → Service → Confirm
- **Reminder**: Bot sends reminder 1h before appointment
- **Webhook**: Bot calls GET/POST on `https://yourdomain.com/api/bot/...`
- **Run**: `npm start` in `whatsapp-bot/` directory

## Financial Module

- **Despesas**: CRUD with categories, payment methods, paid/pending
- **Caixa**: Daily opening/closing with cash flow tracking
- **Relatorios**: Reports by period, by barber, by service

## Setup

1. Create MySQL DB `barbearia` and update `.env`
2. `php artisan migrate`
3. `php artisan db:seed`
4. `composer dev`
5. `cd whatsapp-bot && npm start` (scan QR code)

## Gotchas

- Bot requires `whatsapp-bot/.env` with `APP_URL` pointing to the Laravel server
- For local bot testing, use a tunnel like `ngrok` or serve over LAN
- Barbers login with email + password (different from admin users)
- After seeding, admin credentials: admin@admin.com / 123456
- DB name is `barbearia` (not `idiomas2026`)
