<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ route('barbeiro.dashboard') }}" class="nav-link {{ request()->is('barbeiro/dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <li class="nav-item {{ request()->is('barbeiro/agendamentos*') ? 'menu-open' : '' }}">
            <a href="{{ route('barbeiro.agendamentos.index') }}" class="nav-link {{ request()->is('barbeiro/agendamentos*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-calendar-check"></i>
                <p>Meus Agendamentos</p>
            </a>
        </li>
    </ul>
</nav>
