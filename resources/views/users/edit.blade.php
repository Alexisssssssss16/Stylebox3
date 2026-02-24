@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header bg-white pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-user-edit me-2 text-warning"></i>Editar Perfil: {{ $user->name }}
                    </h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle me-1"></i>Errores:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label text-muted small fw-bold">NOMBRE COMPLETO</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label text-muted small fw-bold">CORREO ELECTRÓNICO</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label text-muted small fw-bold">CONTRASEÑA <span
                                    class="fw-normal text-muted">(dejar en blanco para mantener)</span></label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="••••••••">
                        </div>

                        {{-- Current Role Summary --}}
                        <div class="mb-4 p-3 border rounded" style="background:#f8f9fa;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label text-muted small fw-bold mb-0">ROL ACTUAL</label>
                                @can('usuarios.manage')
                                    <a href="{{ route('admin.users.roles', $user) }}" class="btn btn-sm"
                                        style="background:#d4a017; color:#fff; border-color:#d4a017;">
                                        <i class="fas fa-user-tag me-1"></i> Cambiar Rol
                                    </a>
                                @endcan
                            </div>

                            @php $currentRole = $user->roles->first(); @endphp
                            @if($currentRole)
                                @php $cfg = $roleConfig[$currentRole->name] ?? null; @endphp
                                <div class="d-flex align-items-start gap-3">
                                    @if($cfg)
                                        <span class="badge {{ $cfg['badge_class'] }} px-3 py-2 d-flex align-items-center gap-1">
                                            <i class="{{ $cfg['icon'] }}"></i>
                                            {{ $cfg['label'] }}
                                        </span>
                                        <div>
                                            <div class="small text-muted mb-1">{{ $cfg['description'] }}</div>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($cfg['modules'] as $mod)
                                                    <span class="badge bg-white border text-secondary" style="font-size:0.7rem;">
                                                        <i class="fas fa-check text-success me-1"
                                                            style="font-size:0.6rem;"></i>{{ $mod }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ ucfirst($currentRole->name) }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small fst-italic">Sin roles asignados —
                                    <a href="{{ route('admin.users.roles', $user) }}">asignar ahora</a>
                                </span>
                            @endif

                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Para cambiar el rol usa el botón "Cambiar Rol".
                            </small>
                        </div>

                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary-custom px-4">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection