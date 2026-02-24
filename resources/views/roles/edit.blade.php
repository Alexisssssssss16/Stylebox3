@extends('layouts.admin')

@section('title', 'Editar Rol')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card card-custom">
                <div class="card-header bg-white pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-warning"></i>Editar Rol: <span
                            class="text-primary">{{ $role->name }}</span></h5>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle me-1"></i>Errores encontrados:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label for="name" class="form-label text-muted small fw-bold">NOMBRE DEL ROL</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $role->name) }}"
                                {{ in_array($role->name, ['admin','vendedor','comprador']) ? 'readonly' : '' }}
                                required>
                            @if(in_array($role->name, ['admin','vendedor','comprador']))
                                <small class="text-muted"><i class="fas fa-lock me-1"></i>Los roles base del sistema no pueden renombrarse.</small>
                            @endif
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold d-block mb-3">
                                PERMISOS DEL ROL
                                <span class="text-muted fw-normal ms-2">— Los marcados están activos</span>
                            </label>

                            {{-- $grouped comes pre-built from RoleController::edit() --}}
                            @foreach ($grouped as $module => $actions)
                                @php $slug = Str::slug($module, '_'); @endphp
                                <div class="border rounded p-3 mb-3" style="border-color:#e9ecef !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="text-uppercase fw-bold text-primary mb-0 small">
                                            <i class="fas fa-cube me-1"></i> {{ ucfirst($module) }}
                                        </h6>
                                        <button type="button" class="btn btn-link btn-sm p-0 text-muted select-all-btn"
                                            data-module="{{ $slug }}">
                                            Seleccionar todos
                                        </button>
                                    </div>
                                    <div class="row">
                                        @foreach ($allPermissions->where('name', 'like', $module . '.%') as $perm)
                                            <div class="col-md-4 col-sm-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input perm-{{ $slug }}" type="checkbox"
                                                        name="permission[]" value="{{ $perm->id }}"
                                                        id="perm_{{ $perm->id }}"
                                                        {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="perm_{{ $perm->id }}">
                                                        <code style="font-size:.8em;">{{ $perm->name }}</code>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary-custom px-4">
                                <i class="fas fa-save me-1"></i> Actualizar Rol
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
        document.querySelectorAll('.select-all-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const module = this.dataset.module;
                const checkboxes = document.querySelectorAll('.perm-' + module);
                const allChecked = [...checkboxes].every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);
                this.textContent = allChecked ? 'Seleccionar todos' : 'Desmarcar todos';
            });
        });
    </script>
@endpush