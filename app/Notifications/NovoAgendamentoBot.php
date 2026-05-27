<?php

namespace App\Notifications;

use App\Models\Agendamento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NovoAgendamentoBot extends Notification
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
            'title' => 'Novo agendamento via WhatsApp',
            'message' => "{$this->agendamento->cliente->nome} agendou com {$this->agendamento->barbeiro->nome} para {$this->agendamento->data->format('d/m/Y')} às {$this->agendamento->hora_inicio}",
            'url' => route('admin.agendamentos.show', $this->agendamento->id),
            'icon' => 'fab fa-whatsapp',
            'color' => '#25D366',
        ];
    }
}
