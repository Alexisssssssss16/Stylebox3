@extends('layouts.admin')

@section('title', 'Roles del Sistema')

@push('styles')
    <style>
        .role-card-display {
            border: 1.5px solid #e9ecef;
            border-radius: 16px;
            padding: 1.4rem 1.5rem;
            height: 100%;
            transition: box-shadow 0.2s;
        }

        .role-card-display:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
        }

        .role-icon-lg {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .module-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 50px;
            padding: 3px 10px;
            font-size: 0.73rem;
            color: #495057;
            margin: 2px;
        }

        .system-badge {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 1px 7px;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: .03em;
        }
    </style>
@endpush

@section('content')
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1"><i class="fas fa-shield-alt me-2 text-warning"></i>Roles del Sistema</h5>
            <p class="text-muted small mb-0">Cada rol define el nivel de acceso de un usuario a los módulos de StyleBox</p>
        </div>
        @can('roles.manage')
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary-custom btn-sm">
                <i class="fas fa-plus me-1"></i> Nuevo Rol
            </a>
        @endcan
    </div>

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

    {{-- Role Cards Grid --}}
    <div class="row g-4 mb-4">
        @forelse ($roles as $role)
            @php
                $cfg = $roleConfig[$role->name] ?? [
                    'label' => ucfirst($role->name),
                    'icon' => 'fas fa-user-tag',
                    'color_bg' => '#6c757d',
                    'color_text' => '#fff',
                    'badge_class' => 'bg-secondary text-white',
                    'description' => 'Rol personalizado.',
                    'modules' => [],
                ];
                $isSystem = in_array($role->name, ['admin', 'vendedor', 'comprador']);
            @endphp
            <div class="col-lg-4 col-md-6">
                <div class="role-card-display">
                    {{-- Header --}}
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="role-icon-lg" style="background:{{ $cfg['color_bg'] }}; color:{{ $cfg['color_text'] }};">
                            <i class="{{ $cfg['icon'] }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold">{{ $cfg['label'] }}</span>
                                @if($isSystem)
                                    <span class="system-badge"><i class="fas fa-lock me-1"
                                            style="font-size:.55rem;"></i>Sistema</span>
                                @endif
                            </div>
                            <div class="d-flex gap-2 mt-1">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:0.7rem;">
                                    {{ $role->permissions->count() }} permisos
                                </span>
                                <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:0.7rem;">
                                    {{ $role->users()->count() }} usuarios
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <p class="small text-muted mb-3">{{ $cfg['description'] }}</p>

                    {{-- Modules --}}
                    @if(!empty($cfg['modules']))
                        <div class="mb-3">
                            <div class="text-uppercase text-muted"
                                style="font-size:0.65rem; letter-spacing:.06em; font-weight:600; margin-bottom:6px;">
                                Acceso a
                            </div>
                            <div>
                                @foreach($cfg['modules'] as $mod)
                                    <span class="module-tag">
                                        <i class="fas fa-check-circle" style="color:#198754; font-size:0.6rem;"></i>
                                        {{ $mod }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="d-flex gap-2 pt-2 border-top mt-2">
                        @can('roles.manage')
                            <a class="btn btn-sm btn-outline-secondary flex-grow-1"
                                href="{{ route('admin.roles.show', $role->id) }}">
                                <i class="fas fa-eye me-1"></i> Ver permisos
                            </a>
                            <a class="btn btn-sm btn-light text-primary" href="{{ route('admin.roles.edit', $role->id) }}"
                                title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!$isSystem)
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('¿Eliminar el rol {{ $role->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="fas fa-shield-alt fa-3x mb-3 d-block opacity-25"></i>
                No hay roles registrados.
                <a href="{{ route('admin.roles.create') }}">Crear el primero</a>
            </div>
        @endforelse
    </div>

    <div class="mt-2">{{ $roles->links() }}</div>

@endsection