<?php

namespace App\Notifications;

use App\Models\Agendamento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LembreteAgendamento extends Notification
{
    use Queueable;

    public Agendamento $agendamento;

    public function __construct(Agendamento $agendamento)
    {
        $this->agendamento = $agendamento;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'agendamento_id' => $this->agendamento->id,
            'title' => 'Lembrete de agendamento',
            'message' => "{$this->agendamento->cliente->nome} tem agendamento com {$this->agendamento->barbeiro->nome} em 1 hora ({$this->agendamento->hora_inicio})",
            'url' => route('admin.agendamentos.show', $this->agendamento->id),
            'icon' => 'fas fa-bell',
            'color' => '#ffc107',
        ];
    }
}
