<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <form method="GET" class="d-flex gap-2 align-items-center flex-wrap" wire:submit.prevent>
                <label class="mb-0">Data:</label>
                <input type="date" name="data" class="form-control form-control-sm" style="width:auto"
                       value="{{ $data }}" wire:change="$set('data', $event.target.value)">
                <label class="mb-0">Barbearia:</label>
                <select name="barbearia_id" class="form-control form-control-sm" style="width:auto"
                        wire:change="$set('barbeariaId', $event.target.value)">
                    <option value="">Todas</option>
                    @foreach($barbearias as $b)
                    <option value="{{ $b->id }}" {{ $barbeariaId == $b->id ? 'selected' : '' }}>{{ $b->nome }}</option>
                    @endforeach
                </select>
                <label class="mb-0">Barbeiro:</label>
                <select name="barbeiro_id" class="form-control form-control-sm" style="width:auto"
                        wire:change="$set('barbeiroId', $event.target.value)">
                    <option value="">Todos</option>
                    @foreach($barbeiros as $b)
                    <option value="{{ $b->id }}" {{ $barbeiroId == $b->id ? 'selected' : '' }}>{{ $b->nome }}</option>
                    @endforeach
                </select>
                <a href="{{ route('admin.agendamentos.index', ['data' => $data, 'barbeiro_id' => $barbeiroId, 'barbearia_id' => $barbeariaId]) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-search"></i>
                </a>
            </form>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovoAgendamento">
                <i class="fas fa-plus"></i> Novo Agendamento
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Cliente</th>
                        <th>Barbearia</th>
                        <th>Barbeiro</th>
                        <th>Serviços</th>
                        <th>Status</th>
                        <th>Valor</th>
                        <th>Pagamento</th>
                        <th>Plano</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendamentos as $ag)
                    <tr wire:key="ag-{{ $ag->id }}">
                        <td>{{ $ag->hora_inicio->format('H:i') }}</td>
                        <td>{{ $ag->cliente->nome }}<br><small class="text-muted">{{ $ag->cliente->telefone }}</small></td>
                        <td>{{ $ag->barbearia?->nome ?? '-' }}</td>
                        <td>{{ $ag->barbeiro->nome }}</td>
                        <td>{{ $ag->servicos->pluck('nome')->implode(', ') }}</td>
                        <td>
                            <span class="badge-status status-{{ $ag->status }}" id="status-badge-{{ $ag->id }}">{{ ucfirst($ag->status) }}</span>
                            @if(in_array($ag->status, ['pendente', 'confirmado']))
                            <div class="d-flex gap-1 mt-1">
                                <button class="btn btn-sm btn-outline-success" wire:click="atualizarStatus({{ $ag->id }}, 'realizado')" title="Confirmar presença">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" wire:click="atualizarStatus({{ $ag->id }}, 'ausente')" title="Faltou">
                                    <i class="fas fa-times"></i>
                                </button>

                            </div>
                            @endif
                        </td>
                        <td>R$ {{ number_format($ag->total ?? 0, 2, ',', '.') }}</td>
                        <td>
                            <select class="form-select form-select-sm" style="width:auto;min-width:110px"
                                wire:change="atualizarPagamento({{ $ag->id }}, $event.target.value)">
                                <option value="">--</option>
                                @foreach(App\Models\Agendamento::FORMAS_PAGAMENTO as $fp)
                                <option value="{{ $fp }}" {{ $ag->forma_pagamento === $fp ? 'selected' : '' }}>{{ $fp }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            @php
                                $cp = $ag->cliente?->planos?->first();
                            @endphp
                            @if($cp && $ag->usar_plano)
                                <small class="d-block">{{ $cp->plano->nome }}</small>
                                @if($ag->dentro_da_cota)
                                    <span class="badge bg-success" style="font-size:10px">Dentro da cota</span>
                                @else
                                    <span class="badge bg-danger" style="font-size:10px">Cota excedida</span>
                                @endif
                            @elseif($cp)
                                <small class="d-block">{{ $cp->plano->nome }}</small>
                                <span class="badge bg-warning" style="font-size:10px">Sem uso do plano</span>
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.agendamentos.show', $ag) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.agendamentos.edit', $ag) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <button onclick="confirmarExclusao('{{ route('admin.agendamentos.destroy', $ag) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">Nenhum agendamento para esta data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
    document.addEventListener('livewire:init', function () {
        Livewire.on('status-updated', function ({ id, status }) {
            const badge = document.getElementById('status-badge-' + id);
            if (badge) {
                badge.className = `badge-status status-${status}`;
                badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
            }
            if (status === 'realizado') {
                Swal.fire('Realizado!', 'Valor registrado no caixa.', 'success');
            }
        });
        Livewire.on('pagamento-updated', function () {
            Swal.fire('Pagamento atualizado!', '', 'success');
        });
    });
    </script>
</div>
