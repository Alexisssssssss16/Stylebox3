@extends('layouts.admin')

@section('title', 'Rol: ' . ucfirst($role->name))

@push('styles')
    <style>
        .module-section {
            margin-bottom: 1.2rem;
        }

        .module-header {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            font-weight: 700;
            color: #6c757d;
            padding: 6px 0 4px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 8px;
        }

        .perm-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f1f3f5;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 3px 10px;
            font-size: 0.75rem;
            font-family: monospace;
            color: #343a40;
            margin: 2px;
        }

        .role-hero {
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            color: #fff;
        }

        .module-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            background: #e9ecef;
            border-radius: 4px;
            font-size: 0.65rem;
            margin-right: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver a Roles
        </a>
        @can('roles.manage')
            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-primary-custom btn-sm">
                <i class="fas fa-edit me-1"></i> Editar Rol
            </a>
        @endcan
    </div>

    {{-- Role Hero --}}
    @if($cfg)
        <div class="role-hero" style="background: {{ $cfg['color_bg'] }};">
            <div class="d-flex align-items-center gap-3 mb-2">
                <i class="{{ $cfg['icon'] }}" style="font-size:2rem; color:{{ $cfg['color_text'] }};"></i>
                <div>
                    <h4 class="fw-bold mb-0">{{ $cfg['label'] }}</h4>
                    <p class="mb-0 opacity-75 small">{{ $cfg['description'] }}</p>
                </div>
                <div class="ms-auto text-end">
                    <div class="small opacity-75">Usuarios con este rol</div>
                    <div class="fw-bold fs-4">{{ $role->users()->count() }}</div>
                </div>
            </div>

            @if(!empty($cfg['modules']))
                <div class="mt-3 pt-3 border-top border-white border-opacity-25">
                    <div class="small opacity-75 mb-2">Módulos de acceso</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($cfg['modules'] as $mod)
                            <span class="badge bg-white bg-opacity-25 text-white">
                                <i class="fas fa-check me-1"></i>{{ $mod }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="card card-custom mb-4">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div>
                    <h5 class="fw-bold mb-0">{{ ucfirst($role->name) }}</h5>
                    <small class="text-muted">Rol personalizado</small>
                </div>
                <div class="ms-auto">
                    <span class="badge bg-secondary">{{ $role->users()->count() }} usuarios</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Permissions grouped by module --}}
    <div class="card card-custom">
        <div class="card-header bg-white pt-4 pb-0 border-bottom-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-uppercase small text-muted mb-0">
                <i class="fas fa-key me-2 text-warning"></i>Permisos asignados
                <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1">{{ $role->permissions->count() }}</span>
            </h6>
        </div>
        <div class="card-body p-4">
            @if(empty($grouped))
                <p class="text-muted fst-italic small">Este rol no tiene permisos asignados.</p>
            @else
                <div class="row g-4">
                    @foreach($grouped as $module => $actions)
                        <div class="col-md-6 col-lg-4 module-section">
                            <div class="module-header">
                                <i class="fas fa-cube module-icon"></i>{{ $module }}
                            </div>
                            @foreach($actions as $action)
                                <span class="perm-badge">
                                    <i class="fas fa-dot-circle" style="color:#198754; font-size:.55rem;"></i>
                                    {{ $action }}
                                </span>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

@endsection