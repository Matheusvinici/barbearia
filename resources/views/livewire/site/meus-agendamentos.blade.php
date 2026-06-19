<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Meus Agendamentos</h5>
        <a href="{{ route('site.agendar') }}" class="btn btn-primary btn-sm" wire:navigate>
            <i class="bi bi-plus-circle"></i> Novo
        </a>
    </div>

    <div class="d-flex align-items-center gap-2 mb-3 p-3 bg-white rounded-3 shadow-sm">
        <i class="bi bi-person-circle fs-3"></i>
        <div>
            <strong>{{ $cliente->nome }}</strong><br>
            <small class="text-muted">{{ preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $cliente->telefone) }}</small>
        </div>
        <a href="{{ route('site.login') }}" class="ms-auto btn btn-sm btn-outline-secondary" wire:navigate>
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>

    @forelse($agendamentos as $ag)
    <div class="step-card">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <strong>{{ $ag->barbeiro->nome }}</strong>
                <span class="badge-status status-{{ $ag->status }} ms-1">{{ ucfirst($ag->status) }}</span>
                <div class="small text-muted mt-1">
                    <i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($ag->data)->format('d/m/Y') }}
                    <i class="bi bi-clock ms-2"></i> {{ substr($ag->hora_inicio, 0, 5) }}
                </div>
                <div class="small mt-1">
                    @foreach($ag->servicos as $s)
                    <span class="badge bg-light text-dark me-1">{{ $s->nome }}</span>
                    @endforeach
                </div>
            </div>
            <div class="text-end">
                <strong class="text-success">R$ {{ number_format($ag->total, 2, ',', '.') }}</strong>
            </div>
        </div>
    </div>
    @empty
    <div class="step-card text-center text-muted py-4">
        <i class="bi bi-calendar-x fs-1"></i>
        <p class="mt-2">Nenhum agendamento encontrado.</p>
        <a href="{{ route('site.agendar') }}" class="btn btn-primary" wire:navigate>
            <i class="bi bi-plus-circle"></i> Agendar Agora
        </a>
    </div>
    @endforelse
</div>
