<?php

namespace App\Notifications;

use App\Models\Agendamento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class LembreteAgendamento extends Notification
{
    use Queueable;

    public Agendamento $agendamento;
    public string $tipo;

    public function __construct(Agendamento $agendamento, string $tipo = '1h')
    {
        $this->agendamento = $agendamento;
        $this->tipo = $tipo;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable instanceof \App\Models\Cliente && $notifiable->email) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        $labels = ['1h' => '1 hora', '30min' => '30 minutos', '15min' => '15 minutos'];

        return [
            'agendamento_id' => $this->agendamento->id,
            'title' => 'Lembrete de agendamento',
            'message' => "{$this->agendamento->cliente->nome} tem agendamento em {$labels[$this->tipo]} ({$this->agendamento->hora_inicio}) com {$this->agendamento->barbeiro->nome}",
            'url' => route('admin.agendamentos.show', $this->agendamento->id),
            'icon' => 'fas fa-bell',
            'color' => '#ffc107',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $labels = ['1h' => '1 hora', '30min' => '30 minutos', '15min' => '15 minutos'];
        $ag = $this->agendamento;

        return (new MailMessage)
            ->subject("Lembrete: seu agendamento é em {$labels[$this->tipo]}")
            ->greeting("Olá, {$ag->cliente->nome}!")
            ->line("Seu agendamento é em aproximadamente {$labels[$this->tipo]}.")
            ->line("**Barbeiro:** {$ag->barbeiro->nome}")
            ->line("**Data:** {$ag->data->format('d/m/Y')}")
            ->line("**Horário:** {$ag->hora_inicio}")
            ->line("**Serviço(s):** {$ag->servicos->pluck('nome')->implode(', ')}")
            ->line("**Valor:** R$ " . number_format($ag->total, 2, ',', '.'))
            ->action('Ver Agendamento', route('admin.agendamentos.show', $ag->id))
            ->salutation('Equipe ' . config('app.name'));
    }
}
