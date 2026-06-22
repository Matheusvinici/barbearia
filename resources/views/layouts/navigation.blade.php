@php
    $isWeb = Auth::guard('web')->check();
    $user = Auth::user();
@endphp
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ $isWeb ? route('admin.dashboard') : route('barbeiro.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard', 'barbeiro/dashboard', 'home', 'dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        @if($user->can('agendamento.view'))
        <li class="nav-item {{ $isWeb ? (request()->is('admin/agendamentos*') ? 'menu-open' : '') : (request()->is('barbeiro/agendamentos*') ? 'menu-open' : '') }}">
            <a href="{{ $isWeb ? route('admin.agendamentos.index') : route('barbeiro.agendamentos.index') }}" class="nav-link {{ $isWeb ? (request()->is('admin/agendamentos*') ? 'active' : '') : (request()->is('barbeiro/agendamentos*') ? 'active' : '') }}">
                <i class="nav-icon fas fa-calendar-check"></i>
                <p>{{ $isWeb ? 'Agendamentos' : 'Meus Agendamentos' }}</p>
            </a>
        </li>
        @endif

        @if($user->can('barbearia.view'))
        <li class="nav-item {{ request()->is('admin/barbearias*') ? 'menu-open' : '' }}">
            <a href="{{ route('admin.barbearias.index') }}" class="nav-link {{ request()->is('admin/barbearias*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-store"></i>
                <p>Barbearias</p>
            </a>
        </li>
        @endif

        @if($user->can('barbeiro.view'))
        <li class="nav-item {{ request()->is('admin/barbeiros*') ? 'menu-open' : '' }}">
            <a href="{{ route('admin.barbeiros.index') }}" class="nav-link {{ request()->is('admin/barbeiros*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-tie"></i>
                <p>Barbeiros</p>
            </a>
        </li>
        @endif

        @if($user->can('servico.view'))
        <li class="nav-item {{ request()->is('admin/servicos*') ? 'menu-open' : '' }}">
            <a href="{{ route('admin.servicos.index') }}" class="nav-link {{ request()->is('admin/servicos*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-cut"></i>
                <p>Serviços</p>
            </a>
        </li>
        @endif

        @if($user->can('cliente.view'))
        <li class="nav-item {{ request()->is('admin/clientes*') && !request()->is('admin/clientes-planos*') ? 'menu-open' : '' }}">
            <a href="{{ route('admin.clientes.index') }}" class="nav-link {{ request()->is('admin/clientes*') && !request()->is('admin/clientes-planos*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>Clientes</p>
            </a>
        </li>
        @endif

        @if($user->can('plano.view'))
        <li class="nav-item has-treeview {{ request()->is('admin/planos*', 'admin/clientes-planos*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/planos*', 'admin/clientes-planos*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-shopping-basket"></i>
                <p>Planos <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.planos.index') }}" class="nav-link {{ request()->is('admin/planos*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Gerenciar Planos</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.clientes-planos.index') }}" class="nav-link {{ request()->is('admin/clientes-planos*') && !request()->is('admin/clientes-planos/dashboard') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Vincular Clientes</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.clientes-planos.dashboard') }}" class="nav-link {{ request()->is('admin/clientes-planos/dashboard*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Dashboard de Cotas</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if($user->can('bloqueio.view'))
        <li class="nav-item {{ request()->is('admin/bloqueios*') ? 'menu-open' : '' }}">
            <a href="{{ route('admin.bloqueios.index') }}" class="nav-link {{ request()->is('admin/bloqueios*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-ban"></i>
                <p>Bloqueio de Agenda</p>
            </a>
        </li>
        @endif

        @if($user->can('despesa.view'))
        <li class="nav-item has-treeview {{ request()->is('admin/despesas*', 'admin/caixa*', 'admin/relatorios*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->is('admin/despesas*', 'admin/caixa*', 'admin/relatorios*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-dollar-sign"></i>
                <p>Financeiro <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.despesas.index') }}" class="nav-link {{ request()->is('admin/despesas*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Despesas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.caixa.index') }}" class="nav-link {{ request()->is('admin/caixa*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Caixa</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.relatorios.index') }}" class="nav-link {{ request()->is('admin/relatorios*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Relatórios</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if($user->can('role.view') || $user->can('configuracao.edit'))
        <li class="nav-item nav-header">ACESSO</li>

        @if($user->can('role.view'))
        <li class="nav-item {{ request()->is('admin/users*') ? 'menu-open' : '' }}">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-shield"></i>
                <p>Usuários</p>
            </a>
        </li>

        <li class="nav-item {{ request()->is('admin/roles*') ? 'menu-open' : '' }}">
            <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->is('admin/roles*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-shield-alt"></i>
                <p>Papéis e Permissões</p>
            </a>
        </li>
        @endif

        @if($user->can('configuracao.edit'))
        <li class="nav-item {{ request()->is('admin/configuracoes*') ? 'menu-open' : '' }}">
            <a href="{{ route('admin.configuracoes.index') }}" class="nav-link {{ request()->is('admin/configuracoes*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-cog"></i>
                <p>Configurações</p>
            </a>
        </li>
        @endif
        @endif
    </ul>
</nav>
