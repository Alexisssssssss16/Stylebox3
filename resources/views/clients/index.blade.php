@extends('layouts.admin')

@section('title', 'Clientes - StyleBox')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-bold">Gestión de Clientes</h2>
            <button type="button" class="btn btn-primary btn-primary-custom" onclick="openCreateModal()">
                <i class="fas fa-plus me-2"></i>Nuevo Cliente
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card card-custom">
            <div class="card-body">
                <!-- Search & Filters -->
                <form action="{{ route('clients.index') }}" method="GET" class="mb-4">
                    <div class="row g-2">
                        <div class="col-md-7">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0"
                                    placeholder="Buscar por nombre, email o teléfono..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select border-start-0">
                                <option value="">Todos los tipos</option>
                                <option value="virtual" {{ request('type') == 'virtual' ? 'selected' : '' }}>Cliente Virtual
                                </option>
                                <option value="presencial" {{ request('type') == 'presencial' ? 'selected' : '' }}>Cliente
                                    Presencial</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="btn-group w-100">
                                <button class="btn btn-dark" type="submit">Filtrar</button>
                                @if(request('search') || request('type'))
                                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary"><i
                                            class="fas fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle table-custom">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Contacto</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clients as $client)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $client->name }}</div>
                                        <small class="text-muted">ID: #{{ $client->id }}</small>
                                    </td>
                                    <td>
                                        <div><i class="fas fa-envelope text-muted me-2"></i>{{ $client->email }}</div>
                                        @if($client->phone)
                                            <div class="small text-muted"><i
                                                    class="fas fa-phone text-muted me-2"></i>{{ $client->phone }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($client->client_type == 'virtual')
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 small">
                                                <i class="fas fa-globe me-1"></i>VIRTUAL
                                            </span>
                                        @else
                                            <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 small">
                                                <i class="fas fa-store me-1"></i>PRESENCIAL
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($client->status)
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Activo</span>
                                        @else
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('clients.show', $client->id) }}"
                                            class="btn btn-sm btn-light text-dark me-1" title="Ver Historial">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <button class="btn btn-sm btn-light text-primary me-1"
                                            onclick="openEditModal({{ $client }})" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST"
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
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <p>No se encontraron clientes.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($clients->hasPages())
                    <div class="mt-4">
                        {{ $clients->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Client Modal -->
    <div class="modal fade" id="clientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="clientForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="methodField" value="POST">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="clientModalLabel">Nuevo Cliente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">TIPO DE CLIENTE</label>
                                <div class="d-flex gap-3 mt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="client_type" id="type_virtual"
                                            value="virtual" required>
                                        <label class="form-check-label" for="type_virtual">Virtual (Online)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="client_type" id="type_presencial"
                                            value="presencial" checked required>
                                        <label class="form-check-label" for="type_presencial">Presencial (Tienda)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold mt-2">INFORMACIÓN PERSONAL</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Nombre Completo"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Correo Electrónico" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Teléfono">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold mt-2">UBICACIÓN</label>
                                <textarea class="form-control" id="address" name="address" rows="2"
                                    placeholder="Dirección completa"></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                        checked>
                                    <label class="form-check-label" for="status">Cliente Activo</label>
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
            const clientModal = new bootstrap.Modal(document.getElementById('clientModal'));
            const form = document.getElementById('clientForm');
            const modalTitle = document.getElementById('clientModalLabel');
            const methodField = document.getElementById('methodField');

            function openCreateModal() {
                form.reset();
                form.action = "{{ route('clients.store') }}";
                methodField.value = "POST";
                modalTitle.innerText = "Nuevo Cliente";
                document.getElementById('type_presencial').checked = true;
                document.getElementById('status').checked = true;
                clientModal.show();
            }

            function openEditModal(client) {
                form.action = `/clients/${client.id}`;
                methodField.value = "PUT";
                modalTitle.innerText = "Editar Cliente";

                document.getElementById('name').value = client.name;
                document.getElementById('email').value = client.email;
                document.getElementById('phone').value = client.phone || '';
                document.getElementById('address').value = client.address || '';
                document.getElementById('status').checked = client.status == 1;

                if (client.client_type === 'virtual') {
                    document.getElementById('type_virtual').checked = true;
                } else {
                    document.getElementById('type_presencial').checked = true;
                }

                clientModal.show();
            }

            // Delete Confirmation
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Eliminar cliente?',
                        text: "Esta acción no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1a1a1a',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });

            @if($errors->any())
                clientModal.show();
            @endif
        </script>
    @endpush
@endsection