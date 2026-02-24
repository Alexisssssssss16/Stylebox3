<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="sidebar-brand">
            <i class="fas fa-layer-group text-warning me-2"></i>
            Style<span>Box</span>
        </a>
    </div>

    <ul class="nav-links list-unstyled mb-0">

        {{-- PRINCIPAL --}}
        <li>
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- OPERACIONES --}}
        <li class="nav-item-header">Operaciones</li>

        <li>
            <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <i class="fas fa-cash-register text-success"></i>
                <span>POS / Vender</span>
            </a>
        </li>
        <li>
            <a href="{{ route('clients.index') }}"
                class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Clientes</span>
            </a>
        </li>

        @can('pedidos.virtuales.manage')
            <li>
                <a href="{{ route('admin.pedidos.index') }}"
                    class="nav-link {{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-basket text-primary"></i>
                    <span>Pedidos Virtuales</span>
                </a>
            </li>
        @endcan

        {{-- CATÁLOGO --}}
        <li class="nav-item-header">Catálogo</li>

        <li>
            <a href="{{ route('products.index') }}"
                class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="fas fa-tshirt"></i>
                <span>Productos</span>
            </a>
        </li>
        <li>
            <a href="{{ route('measurement_units.index') }}"
                class="nav-link {{ request()->routeIs('measurement_units.*') ? 'active' : '' }}">
                <i class="fas fa-ruler-combined"></i>
                <span>Unidades de Medida</span>
            </a>
        </li>
        <li>
            <a href="{{ route('shop.index') }}" class="nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}">
                <i class="fas fa-store"></i>
                <span>Vista del Catálogo</span>
            </a>
        </li>

        {{-- REPORTES --}}
        <li class="nav-item-header">Reportes</li>

        @can('reportes_view')
            <li>
                <a href="{{ route('reports.index') }}"
                    class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reportes de Ventas</span>
                </a>
            </li>
        @endcan

        {{-- SISTEMA (solo admin) --}}
        @canany(['users_list', 'roles_list'])
            <li class="nav-item-header">Sistema</li>
        @endcanany

        @can('users_list')
            <li>
                <a href="{{ route('admin.users.index') }}"
                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>
                    <span>Usuarios</span>
                </a>
            </li>
        @endcan

        @can('roles_list')
            <li>
                <a href="{{ route('admin.roles.index') }}"
                    class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i>
                    <span>Roles</span>
                </a>
            </li>
        @endcan

        @role('admin')
        <li>
            <a href="{{ route('admin.permissions.index') }}"
                class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                <i class="fas fa-key"></i>
                <span>Permisos</span>
            </a>
        </li>
        @endrole

    </ul>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div class="user-info">
                <div class="fw-bold text-truncate small">{{ auth()->user()->name }}</div>
                <small class="text-muted d-block text-truncate">
                    @foreach(auth()->user()->roles as $r)
                        {{ ucfirst($r->name) }}@if(!$loop->last), @endif
                    @endforeach
                </small>
            </div>
            <a href="#" class="logout-btn" title="Cerrar Sesión"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</nav>