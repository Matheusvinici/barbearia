@php
    $isWeb = Auth::guard('web')->check();
    $user = Auth::user();
    $__tenant = request()->route('barbearia');
    $__tenantSlug = $__tenant ? $__tenant->slug : null;
    $__route = $__tenantSlug ? 'tenant.admin.' : 'admin.';
    $__params = $__tenantSlug ? [$__tenantSlug] : [];
    function navRoute($name, $params = []) {
        $__tenantSlug = request()->route('barbearia')?->slug;
        return $__tenantSlug
            ? route('tenant.admin.' . $name, array_merge([$__tenantSlug], $params))
            : route('admin.' . $name, $params);
    }
    function barbeiroNavRoute($name, $params = []) {
        $__tenantSlug = request()->route('barbearia')?->slug;
        return $__tenantSlug
            ? route('tenant.barbeiro.' . $name, array_merge([$__tenantSlug], $params))
            : route('barbeiro.' . $name, $params);
    }
    function isActive($patterns) {
        $path = request()->path();
        foreach ((array)$patterns as $p) {
            if (str_starts_with($path, $p)) return true;
        }
        return false;
    }
@endphp

<div class="nav-section">
    <div class="nav-label">Geral</div>

    <a href="{{ navRoute('dashboard') }}" class="nav-item {{ isActive(['admin/dashboard', 'admin/home', 'barbeiro/dashboard', 'dashboard', 'home']) || request()->is('*/dashboard') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        <span>Painel Inicial</span>
    </a>

    @if($user->can('agendamento.view'))
    <a href="{{ navRoute('agendamentos.index') }}" class="nav-item {{ isActive(['admin/agendamentos', 'barbeiro/agendamentos']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span>{{ $isWeb ? 'Agendamentos' : 'Meus Agendamentos' }}</span>
    </a>
    @endif

    @if($user->can('cliente.view'))
    <a href="{{ navRoute('clientes.index') }}" class="nav-item {{ isActive(['admin/clientes']) && !isActive(['admin/clientes-planos']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>Clientes</span>
    </a>
    @endif

    @if($user->can('barbeiro.view'))
    <a href="{{ navRoute('barbeiros.index') }}" class="nav-item {{ isActive(['admin/barbeiros']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>Profissionais</span>
    </a>
    @endif

    @if($user->can('servico.view'))
    <a href="{{ navRoute('servicos.index') }}" class="nav-item {{ isActive(['admin/servicos']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        <span>Serviços</span>
    </a>
    @endif

    @if($user->can('bloqueio.view'))
    <a href="{{ $isWeb ? navRoute('bloqueios.index') : barbeiroNavRoute('bloqueios.index') }}" class="nav-item {{ isActive(['admin/bloqueios', 'barbeiro/bloqueios']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <span>Bloqueios</span>
    </a>
    @endif
</div>

<div class="nav-section">
    <div class="nav-label">Financeiro</div>

    @if($user->can('despesa.view'))
    <a href="{{ navRoute('despesas.index') }}" class="nav-item {{ isActive(['admin/despesas']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <span>Despesas</span>
    </a>
    @endif

    @if($user->can('despesa.view') || $user->can('caixa.view'))
    <a href="{{ navRoute('caixa.index') }}" class="nav-item {{ isActive(['admin/caixa']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><path d="M8 14h.01"/><path d="M16 14h.01"/></svg>
        <span>Caixa</span>
    </a>
    @endif

    @if($user->can('relatorio.view'))
    <a href="{{ navRoute('relatorios.index') }}" class="nav-item {{ isActive(['admin/relatorios']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        <span>Relatórios</span>
    </a>
    @endif
</div>

<div class="nav-section">
    <div class="nav-label">Configurações</div>

    @if($user->can('configuracao.edit'))
    <a href="{{ navRoute('configuracoes.index') }}" class="nav-item {{ isActive(['admin/configuracoes']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span>Configurações</span>
    </a>
    @endif

    @if($user->can('plano.view'))
    <a href="{{ navRoute('planos.index') }}" class="nav-item {{ isActive(['admin/planos']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
        <span>Planos</span>
    </a>
    @endif

    @if($user->can('role.view') && !$__tenantSlug)
    <a href="{{ route('admin.users.index') }}" class="nav-item {{ isActive(['admin/users']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>Usuários</span>
    </a>
    <a href="{{ route('admin.roles.index') }}" class="nav-item {{ isActive(['admin/roles']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <span>Papéis</span>
    </a>
    @endif

    @if($user->can('barbearia.view'))
    <a href="{{ route('admin.barbearias.index') }}" class="nav-item {{ isActive(['admin/barbearias']) ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span>Barbearias</span>
    </a>
    @endif
</div>
