@extends('layouts.admin')

@section('title', 'Nuevo Usuario')

@push('styles')
<style>
    .role-card-sm {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.9rem 1.1rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .role-card-sm:hover { border-color: #adb5bd; }
    .role-card-sm.selected {
        border-color: var(--accent-color, #d4a017);
        background: #fffbf0;
    }
    .role-card-sm input[type="radio"] { display: none; }
    .role-dot {
        width: 10px; height: 10px;
        border: 2px solid #ced4da;
        border-radius: 50%;
        flex-shrink: 0;
        transition: all .15s;
    }
    .role-card-sm.selected .role-dot {
        background: #d4a017;
        border-color: #d4a017;
    }
</style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header bg-white pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-plus me-2 text-warning"></i>Registrar Nuevo Usuario</h5>
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

                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label text-muted small fw-bold">NOMBRE COMPLETO</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label text-muted small fw-bold">CORREO ELECTRÓNICO</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label text-muted small fw-bold">CONTRASEÑA</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Role selector (single role) --}}
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold d-block mb-2">ROL DEL USUARIO</label>
                            <p class="text-muted small mb-3">El rol define el nivel de acceso del usuario en el sistema.</p>

                            @error('role')
                                <div class="alert alert-danger py-2 px-3 small mb-2">{{ $message }}</div>
                            @enderror

                            <div class="d-flex flex-column gap-2">
                                @foreach($roles as $role)
                                    @php
                                        $cfg = $roleConfig[$role->name] ?? [
                                            'label'       => ucfirst($role->name),
                                            'icon'        => 'fas fa-user-tag',
                                            'color_bg'    => '#6c757d',
                                            'color_text'  => '#fff',
                                            'description' => '',
                                        ];
                                        $checked = old('role') === $role->name;
                                    @endphp
                                    <label class="role-card-sm d-flex align-items-center gap-3 {{ $checked ? 'selected' : '' }}"
                                           for="role_{{ $role->id }}">
                                        <input type="radio" id="role_{{ $role->id }}"
                                               name="role" value="{{ $role->name }}"
                                               {{ $checked ? 'checked' : '' }}>
                                        <div style="width:36px; height:36px; border-radius:8px; display:flex; align-items:center;
                                                    justify-content:center; background:{{ $cfg['color_bg'] }}; color:{{ $cfg['color_text'] }};
                                                    font-size:.9rem; flex-shrink:0;">
                                            <i class="{{ $cfg['icon'] }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold small">{{ $cfg['label'] }}</div>
                                            <div class="text-muted" style="font-size:.75rem;">{{ $cfg['description'] }}</div>
                                        </div>
                                        <div class="role-dot"></div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary-custom px-4">
                                <i class="fas fa-save me-1"></i> Guardar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.role-card-sm').forEach(function(card) {
    card.addEventListener('click', function() {
        document.querySelectorAll('.role-card-sm').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input[type="radio"]').checked = true;
    });
});
</script>
@endpush