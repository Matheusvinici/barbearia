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
            'message' => "{$this->agendamento->cliente->nome} agendou com {$this->agendamento->barbeiro->nome} para {$this->agendamento->data->format('d/m/Y')} às {$this->agendamento->hora_inicio->format('H:i')}",
            'url' => $this->agendamento->barbearia_id
                ? route('tenant.admin.agendamentos.show', [$this->agendamento->barbearia->slug, $this->agendamento->id])
                : route('admin.agendamentos.show', $this->agendamento->id),
            'icon' => 'fab fa-whatsapp',
            'color' => '#25D366',
            'cliente_nome' => $this->agendamento->cliente->nome,
            'cliente_telefone' => $this->agendamento->cliente->telefone,
            'barbeiro_nome' => $this->agendamento->barbeiro->nome,
            'data' => $this->agendamento->data->format('d/m/Y'),
            'hora_inicio' => $this->agendamento->hora_inicio->format('H:i'),
            'hora_fim' => $this->agendamento->hora_fim->format('H:i'),
            'servicos' => $this->agendamento->servicos->pluck('nome')->toArray(),
            'total' => (float) $this->agendamento->total,
            'status' => $this->agendamento->status,
        ];
    }
}
