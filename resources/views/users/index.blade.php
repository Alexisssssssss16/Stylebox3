@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')

@push('styles')
    <style>
        .role-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .role-desc-tooltip {
            font-size: 0.72rem;
            color: #6c757d;
            display: block;
            margin-top: 2px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: #1a1a1a;
            color: #d4a017;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')
    <div class="card card-custom">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-user-cog me-2 text-warning"></i>Usuarios del Sistema</h5>
            @can('usuarios.manage')
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary-custom btn-sm">
                    <i class="fas fa-plus me-1"></i> Nuevo Usuario
                </a>
            @endcan
        </div>
        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Search --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
                <div class="input-group" style="max-width: 400px;">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Buscar por nombre o email..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary btn-sm" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Usuario</th>
                            <th>Email</th>
                            <th>Rol actual</th>
                            <th>Acceso</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            @php
                                $userRole = $user->roles->first();
                                $roleName = $userRole ? $userRole->name : null;
                                $cfg = $roleName ? ($roleConfig[$roleName] ?? null) : null;
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <small class="text-muted">(Tú)</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted small">{{ $user->email }}</td>

                                {{-- Rol actual con ícono y label --}}
                                <td>
                                    @if($cfg)
                                        <span class="role-pill {{ $cfg['badge_class'] }}">
                                            <i class="{{ $cfg['icon'] }}"></i>
                                            {{ $cfg['label'] }}
                                        </span>
                                    @elseif($roleName)
                                        <span class="role-pill bg-secondary text-white">
                                            <i class="fas fa-user-tag"></i>
                                            {{ ucfirst($roleName) }}
                                        </span>
                                    @else
                                        <span class="text-muted small fst-italic">Sin rol</span>
                                    @endif
                                </td>

                                {{-- Descripción breve del acceso --}}
                                <td style="max-width: 240px;">
                                    @if($cfg)
                                        <small class="text-muted">{{ $cfg['description'] }}</small>
                                    @else
                                        <small class="text-muted fst-italic">—</small>
                                    @endif
                                </td>

                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-circle me-1" style="font-size:8px;"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning">
                                            <i class="fas fa-circle me-1" style="font-size:8px;"></i>Pendiente
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end pe-4">
                                    @can('usuarios.manage')
                                        {{-- Assign Role --}}
                                        <a href="{{ route('admin.users.roles', $user) }}" class="btn btn-warning btn-sm me-1"
                                            title="Cambiar Rol" style="background:#d4a017; border-color:#d4a017; color:#fff;">
                                            <i class="fas fa-user-tag"></i>
                                        </a>
                                        {{-- Edit Profile --}}
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="btn btn-light btn-sm text-primary me-1" title="Editar perfil">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('usuarios.manage')
                                        @if(!$user->hasRole('admin') || \App\Models\User::role('admin')->count() > 1)
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('¿Eliminar a {{ addslashes($user->name) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-light btn-sm text-danger" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-users fa-2x mb-2 d-block opacity-25"></i>
                                    No se encontraron usuarios.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Legend --}}
            <div class="d-flex align-items-center gap-4 mt-1 mb-3 small text-muted flex-wrap">
                @foreach($roleConfig as $rName => $rCfg)
                    <span>
                        <span class="role-pill {{ $rCfg['badge_class'] }}" style="font-size:0.7rem;">
                            <i class="{{ $rCfg['icon'] }}"></i>{{ $rCfg['label'] }}
                        </span>
                        — {{ $rCfg['description'] }}
                    </span>
                @endforeach
            </div>

            <div class="mt-2">
                {{ $users->appends(request()->query())->links() }}
            </div>

        </div>
    </div>
@endsection