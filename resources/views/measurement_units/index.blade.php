@extends('layouts.admin')

@section('title', 'Unidades - StyleBox')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-bold">Unidades de Medida</h2>
            <button type="button" class="btn btn-primary btn-primary-custom" onclick="openCreateModal()">
                <i class="fas fa-plus me-2"></i>Nueva Unidad
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card card-custom">
            <div class="card-body">
                <!-- Search Filter -->
                <form action="{{ route('measurement_units.index') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="Buscar por código, nombre..." value="{{ request('search') }}">
                        <button class="btn btn-dark" type="submit">Buscar</button>
                        @if(request('search'))
                            <a href="{{ route('measurement_units.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle table-custom">
                        <thead>
                            <tr>
                                <th>Identificación</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($units as $unit)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 fw-bold text-center" style="width: 45px;">
                                                {{ $unit->code }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $unit->name }}</div>
                                                <small class="text-muted">SUNAT: {{ $unit->code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $unit->description }}</td>
                                    <td>
                                        @if($unit->status)
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Activo</span>
                                        @else
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-light text-primary me-1"
                                            onclick="openEditModal({{ $unit }})" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('measurement_units.destroy', $unit->id) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-ruler fa-3x mb-3"></i>
                                            <p>No se encontraron unidades de medida.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Unit Modal -->
    <div class="modal fade" id="unitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="unitForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="methodField" value="POST">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="unitModalLabel">Nueva Unidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">CÓDIGO (SUNAT)</label>
                                <input type="text" class="form-control" id="code" name="code" placeholder="NIU"
                                    maxlength="10" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label text-muted small fw-bold">NOMBRE COMERCIAL</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Unidad"
                                    maxlength="50" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">DESCRIPCIÓN</label>
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="Descripción oficial" required>
                            </div>
                            <div class="col-12 mt-4">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                        checked>
                                    <label class="form-check-label" for="status">Unidad Activa</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-primary-custom px-4">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const unitModal = new bootstrap.Modal(document.getElementById('unitModal'));
            const form = document.getElementById('unitForm');
            const modalTitle = document.getElementById('unitModalLabel');
            const methodField = document.getElementById('methodField');

            function openCreateModal() {
                form.reset();
                form.action = "{{ route('measurement_units.store') }}";
                methodField.value = "POST";
                modalTitle.innerText = "Nueva Unidad";
                document.getElementById('status').checked = true;
                unitModal.show();
            }

            function openEditModal(unit) {
                form.action = `/measurement_units/${unit.id}`;
                methodField.value = "PUT";
                modalTitle.innerText = "Editar Unidad";

                document.getElementById('code').value = unit.code;
                document.getElementById('name').value = unit.name;
                document.getElementById('description').value = unit.description;
                document.getElementById('status').checked = unit.status == 1;

                unitModal.show();
            }

            // Delete Confirmation
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Eliminar unidad?',
                        text: "Si está asociada a productos, no podrá eliminarse.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1a1a1a',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });

            @if($errors->any())
                unitModal.show();
            @endif
        </script>
    @endpush
@endsection