@php
    $telefone = old('telefone', session('telefone', ''));
    $novo = old('novo', session('novo', false));
@endphp

<x-guest-layout>
@section('title', 'Agendar')
@section('subtitle', 'Agende seu horário')

@section('footer-links')
    <a href="{{ route('login') }}" class="text-muted small me-3"><i class="fas fa-user-shield"></i> Administração</a>
@stop

<form method="POST" action="/entrar">
    @csrf

    <div class="text-center mb-4">
        <h5 class="fw-bold">Olá, cliente!</h5>
        <p class="text-muted small">Digite seu telefone para agendar</p>
    </div>

    <div class="mb-3">
        <label class="form-label">Telefone</label>
        <input type="tel" name="telefone" class="form-control form-control-lg text-center"
               value="{{ $telefone }}"
               placeholder="(88) 99999-9999" maxlength="15" required autofocus
               oninput="this.value=this.value.replace(/\D/g,'').replace(/^(\d{2})(\d{5})(\d{4}).*/,'($1) $2-$3')">
    </div>

    <div class="mb-3" id="nome-field" style="{{ $novo ? '' : 'display:none' }}">
        <label class="form-label">Nome completo</label>
        <input type="text" name="nome" class="form-control form-control-lg"
               value="{{ old('nome') }}" placeholder="Seu nome" {{ $novo ? 'required' : '' }}>
    </div>

    @if(session('error')) <div class="alert alert-danger py-2 small">{{ session('error') }}</div> @endif

    <button type="submit" class="btn btn-login btn-block text-white w-100">
        {{ $novo ? 'Cadastrar e Agendar' : 'Entrar' }}
    </button>
</form>

@if(!$novo)
<div class="text-center mt-3">
    <span class="text-muted small">Primeira vez?</span>
    <a href="#" class="small" onclick="event.preventDefault();document.getElementById('nome-field').style.display='';document.querySelector('[name=nome]').required=true;this.closest('div').remove()">Criar cadastro</a>
</div>
@endif

</x-guest-layout>
