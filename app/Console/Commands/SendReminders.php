<?php

namespace App\Console\Commands;

use App\Models\Agendamento;
use App\Notifications\LembreteAgendamento;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';

    protected $description = 'Send email reminders to clients 1h, 30min and 15min before appointment';

    public function handle()
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');

        $appointments = Agendamento::whereDate('data', $today)
            ->whereIn('status', ['pendente', 'confirmado'])
            ->with('cliente', 'barbeiro', 'servicos')
            ->get();

        $sent = 0;

        foreach ($appointments as $ag) {
            $horaInicio = Carbon::parse($today . ' ' . $ag->hora_inicio);
            $diff = $now->diffInMinutes($horaInicio, false);

            if ($diff < 0) continue;

            $tipo = null;

            if ($diff <= 65 && $diff >= 55 && !$ag->lembrete_1h_at) {
                $tipo = '1h';
            } elseif ($diff <= 35 && $diff >= 25 && !$ag->lembrete_30min_at) {
                $tipo = '30min';
            } elseif ($diff <= 20 && $diff >= 10 && !$ag->lembrete_15min_at) {
                $tipo = '15min';
            }

            if ($tipo) {
                try {
                    $ag->cliente->notify(new LembreteAgendamento($ag, $tipo));

                    $column = "lembrete_{$tipo}_at";
                    $ag->update([$column => $now]);

                    $sent++;
                    $this->info("Reminder {$tipo} sent to {$ag->cliente->nome} ({$ag->cliente->email})");
                } catch (\Exception $e) {
                    $this->error("Failed to send {$tipo} reminder to {$ag->cliente->nome}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Sent {$sent} reminders");
    }
}
