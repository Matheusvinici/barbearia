<?php

namespace App\Console\Commands;

use App\Models\Agendamento;
use App\Models\PushSubscription;
use Illuminate\Console\Command;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class SendPushReminders extends Command
{
    protected $signature = 'push:reminders';
    protected $description = 'Send push notifications for upcoming appointments';

    public function handle(): void
    {
        $vapidSubject = config('app.url');
        $vapidPublicKey = env('VAPID_PUBLIC_KEY');
        $vapidPrivateKey = env('VAPID_PRIVATE_KEY');

        if (!$vapidPublicKey || !$vapidPrivateKey) {
            $this->warn('VAPID keys not configured. Run "php artisan push:generate-keys"');
            return;
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => $vapidSubject,
                'publicKey' => $vapidPublicKey,
                'privateKey' => $vapidPrivateKey,
            ],
        ]);

        $now = now();
        $thresholds = [
            ['min' => 55, 'max' => 65, 'label' => '1 hora'],
            ['min' => 25, 'max' => 35, 'label' => '30 minutos'],
            ['min' => 10, 'max' => 20, 'label' => '15 minutos'],
            ['min' => 3, 'max' => 7, 'label' => '5 minutos'],
        ];

        $sent = 0;

        foreach ($thresholds as $t) {
            $start = (clone $now)->addMinutes($t['min']);
            $end = (clone $now)->addMinutes($t['max']);

            $agendamentos = Agendamento::whereBetween('data', [$start->toDateString(), $end->toDateString()])
                ->whereBetween('hora_inicio', [$start->toTimeString(), $end->toTimeString()])
                ->whereIn('status', ['pendente', 'confirmado'])
                ->get();

            foreach ($agendamentos as $ag) {
                $cliente = $ag->cliente;
                if (!$cliente) continue;

                $subscriptions = PushSubscription::where('subscribable_id', $cliente->id)
                    ->where('subscribable_type', get_class($cliente))
                    ->get();

                foreach ($subscriptions as $sub) {
                    $payload = json_encode([
                        'title' => 'Lembrete de Agendamento',
                        'body' => "Seu agendamento com {$ag->barbeiro?->nome} é em {$t['label']}!",
                        'tag' => "lembrete-{$ag->id}",
                        'url' => $ag->barbearia?->slug
                            ? route('tenant.site.meus-agendamentos', $ag->barbearia->slug)
                            : route('site.meus-agendamentos'),
                    ]);

                    $webPush->queueNotification(
                        new Subscription(
                            $sub->endpoint,
                            $sub->p256dh_key,
                            $sub->auth_key
                        ),
                        $payload
                    );

                    $sent++;
                }
            }
        }

        foreach ($webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                $this->warn("Push failed: {$report->getReason()}");
            }
        }

        $this->info("Sent {$sent} push notifications");
    }
}
