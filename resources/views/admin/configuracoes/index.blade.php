@extends('layouts.app')
@section('title', 'Configurações')
@section('breadcrumb', 'Configurações')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Configurações do Sistema</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.configuracoes.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nome da Barbearia</label>
                            <input type="text" name="nome_barbearia" class="form-control" value="{{ $configuracoes['nome_barbearia'] }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Telefone</label>
                            <input type="text" name="telefone" class="form-control" value="{{ $configuracoes['telefone'] }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Endereço</label>
                            <input type="text" name="endereco" class="form-control" value="{{ $configuracoes['endereco'] }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Horário de Abertura</label>
                            <input type="time" name="horario_abertura" class="form-control" value="{{ $configuracoes['horario_abertura'] }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Horário de Fechamento</label>
                            <input type="time" name="horario_fechamento" class="form-control" value="{{ $configuracoes['horario_fechamento'] }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Intervalo entre Agendamentos (min)</label>
                            <input type="number" name="intervalo_minutos" class="form-control" value="{{ $configuracoes['intervalo_minutos'] }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Dias de Funcionamento</label>
                            <div class="row">
                                @php $dias = explode(',', $configuracoes['dias_funcionamento']); @endphp
                                @foreach([0=>'Dom',1=>'Seg',2=>'Ter',3=>'Qua',4=>'Qui',5=>'Sex',6=>'Sáb'] as $key => $label)
                                <div class="col-4 form-check">
                                    <input type="checkbox" name="dias_funcionamento_checkbox[]" class="form-check-input" value="{{ $key }}" id="dia{{ $key }}" {{ in_array((string)$key, $dias) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dia{{ $key }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="dias_funcionamento" id="dias_funcionamento_hidden" value="{{ $configuracoes['dias_funcionamento'] }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Token do Bot WhatsApp (para autenticação)</label>
                            <input type="text" name="whatsapp_bot_token" class="form-control" value="{{ $configuracoes['whatsapp_bot_token'] }}">
                            <small class="text-muted">Use para validar chamadas do bot para a API</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>🤖 Bot WhatsApp</h5></div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <strong>Status:</strong><br>
                    @if($botOnline)
                        <span class="badge bg-success" style="font-size:14px">🟢 Online</span>
                    @else
                        <span class="badge bg-danger" style="font-size:14px">🔴 Offline</span>
                    @endif
                </div>

                @if(!$botOnline)
                    <div class="alert alert-warning py-2">
                        <small>O servidor do bot não está rodando.<br>Execute: <code>cd whatsapp-bot && node index.js</code></small>
                    </div>
                @endif

                @if($qrExiste)
                    <hr>
                    <strong>QR Code para conectar:</strong>
                    <div class="mt-2">
                        <img src="{{ route('admin.configuracoes.qr-code') }}?t={{ time() }}" class="img-fluid" style="max-width:250px" alt="QR Code WhatsApp">
                    </div>
                    <small class="text-muted">Escaneie com o WhatsApp da barbearia</small>
                @elseif($botOnline)
                    <div class="alert alert-success py-2 mt-2 mb-0">
                        <small>✅ Bot conectado! Nenhum QR necessário.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('input[name="dias_funcionamento_checkbox[]"]').change(function() {
    let valores = [];
    $('input[name="dias_funcionamento_checkbox[]"]:checked').each(function() {
        valores.push($(this).val());
    });
    $('#dias_funcionamento_hidden').val(valores.join(','));
});

@if($qrExiste && !$botOnline)
setTimeout(function() { location.reload(); }, 10000);
@endif
</script>
@endpush
@endsection
