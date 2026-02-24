@extends('layouts.admin')

@section('title', 'Productos - StyleBox')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0 fw-bold">Inventario de Ropa</h2>
                <p class="text-muted mb-0">Gestión de catálogo y stock</p>
            </div>
            <button type="button" class="btn btn-primary btn-primary-custom" onclick="openCreateModal()">
                <i class="fas fa-plus me-2"></i>Nuevo Producto
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
                <!-- Search Filter -->
                <form action="{{ route('products.index') }}" method="GET" class="mb-4">
                    <div class="row g-2">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0"
                                    placeholder="Buscar por código, prenda o categoría..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4 d-grid gap-2 d-md-block">
                            <button class="btn btn-dark w-100" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle table-custom">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Producto</th>
                                <th>Categoría</th>
                                <th>Precios (S/)</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3 position-relative overflow-hidden"
                                                style="width: 50px; height: 50px;">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}"
                                                        class="position-absolute w-100 h-100 object-fit-cover" alt="img">
                                                @else
                                                    <i class="fas fa-tshirt text-secondary fa-lg"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $product->name }}</div>
                                                <small class="text-muted d-block">COD: {{ $product->code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $product->category }}</span></td>
                                    <td>
                                        <div class="fw-bold text-dark">S/ {{ number_format($product->price, 2) }}</div>
                                        <small class="text-muted">Costo: {{ number_format($product->cost, 2) }}</small>
                                    </td>
                                    <td>
                                        @if($product->stock <= 5)
                                            <span class="text-danger fw-bold"><i
                                                    class="fas fa-exclamation-triangle me-1"></i>{{ $product->stock }}</span>
                                        @else
                                            <span class="fw-bold">{{ $product->stock }}</span>
                                        @endif
                                        <small class="text-muted ms-1">{{ $product->measurementUnit->code }}</small>
                                    </td>
                                    <td>
                                        @if($product->status)
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Activo</span>
                                        @else
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        {{-- Botón Tallas --}}
                                        <button class="btn btn-sm btn-light text-success me-1"
                                            onclick="openTallasModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ addslashes($product->category) }}')"
                                            title="Gestionar Tallas">
                                            <i class="fas fa-ruler-horizontal"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light text-primary me-1"
                                            onclick="openEditModal({{ $product }})" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST"
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
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3"></i>
                                            <p>No se encontraron productos en el inventario.</p>
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

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="productForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="methodField" value="POST">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="productModalLabel">Nuevo Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Sección 1: Detalles Básicos -->
                            <div class="col-12">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Información
                                    Básica</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Nombre de Prenda</label>
                                <input type="text" class="form-control" id="name" name="name" required
                                    placeholder="Ej: Polo Pique">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Código</label>
                                <input type="text" class="form-control" id="code" name="code" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Categoría</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Polos">Polos</option>
                                    <option value="Camisas">Camisas</option>
                                    <option value="Pantalones">Pantalones</option>
                                    <option value="Vestidos">Vestidos</option>
                                    <option value="Accesorios">Accesorios</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Descripción</label>
                                <textarea class="form-control" id="description" name="description" rows="2"
                                    placeholder="Detalles de tela, corte, etc."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Imagen del Producto</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Se recomienda una imagen cuadrada (1:1).</div>
                            </div>

                            <!-- Sección 2: Inventario y Precios -->
                            <div class="col-12 mt-4">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Inventario y
                                    Costos</h6>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Unidad Medida</label>
                                <select class="form-select" id="measurement_unit_id" name="measurement_unit_id" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Stock Inicial</label>
                                <input type="number" class="form-control" id="stock" name="stock" required min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Costo (S/)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">S/</span>
                                    <input type="number" step="0.01" class="form-control border-start-0 ps-1" id="cost"
                                        name="cost" required min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-primary">Precio Venta</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white border-primary">S/</span>
                                    <input type="number" step="0.01"
                                        class="form-control border-primary fw-bold text-primary show-focus-primary"
                                        id="price" name="price" required min="0">
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="col-12 mt-3">
                                <div class="form-check form-switch p-3 bg-light rounded">
                                    <input type="hidden" name="status" value="0">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" id="status" name="status"
                                        value="1" checked>
                                    <label class="form-check-label fw-bold" for="status">Producto Disponible para
                                        Venta</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-primary-custom px-4">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ====== MODAL GESTIÓN DE TALLAS ====== --}}
    <div class="modal fade" id="tallasModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            <i class="fas fa-ruler-horizontal me-2 text-success"></i>
                            Gestión de Tallas
                        </h5>
                        <small class="text-muted" id="tallasProductoNombre"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Indicador de carga --}}
                    <div id="tallasLoading" class="text-center py-4">
                        <div class="spinner-border text-success" role="status"></div>
                        <p class="text-muted mt-2 mb-0">Cargando tallas...</p>
                    </div>

                    {{-- Tabla de tallas --}}
                    <div id="tallasContent" style="display:none;">
                        <div class="alert alert-info border-0 py-2 mb-3" style="background:#f0fdf4;">
                            <i class="fas fa-info-circle text-success me-1"></i>
                            <small>El stock total del producto se calcula automáticamente sumando todas las tallas.</small>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="tallasTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Talla</th>
                                        <th>Tipo</th>
                                        <th style="width:120px">Stock</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Activo</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tallasTableBody">
                                </tbody>
                            </table>
                        </div>
                        <div id="sinTallas" class="text-center py-4" style="display:none;">
                            <i class="fas fa-exclamation-circle text-muted fa-2x mb-2 d-block"></i>
                            <p class="text-muted mb-2">No hay tallas asignadas aún.</p>
                            <button type="button" class="btn btn-success btn-sm" id="btnAsignarTallas">
                                <i class="fas fa-magic me-1"></i>Asignar tallas automáticamente
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <span class="text-muted small me-auto" id="tallasStockTotal"></span>
                    <a id="btnVerInventario" href="{{ route('inventario.tallas') }}"
                        class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-boxes me-1"></i>Ver Inventario General
                    </a>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const productModal = new bootstrap.Modal(document.getElementById('productModal'));
            const form = document.getElementById('productForm');
            const modalTitle = document.getElementById('productModalLabel');
            const methodField = document.getElementById('methodField');

            function openCreateModal() {
                form.reset();
                form.action = "{{ route('products.store') }}";
                methodField.value = "POST";
                modalTitle.innerText = "Nuevo Producto";
                document.getElementById('status').checked = true;
                productModal.show();
            }

            function openEditModal(product) {
                form.action = `/products/${product.id}`;
                methodField.value = "PUT";
                modalTitle.innerText = "Editar Producto";

                document.getElementById('code').value = product.code;
                document.getElementById('name').value = product.name;
                document.getElementById('category').value = product.category;
                document.getElementById('description').value = product.description || '';
                document.getElementById('cost').value = product.cost;
                document.getElementById('price').value = product.price;
                document.getElementById('stock').value = product.stock;
                document.getElementById('measurement_unit_id').value = product.measurement_unit_id;
                document.getElementById('status').checked = product.status == 1;

                productModal.show();
            }

            // Delete Confirmation
            document.querySelectorAll('.delete-form').forEach(function (deleteForm) {
                deleteForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formToSubmit = this;
                    Swal.fire({
                        title: '¿Eliminar producto?',
                        text: "Esta acción retirará el item del inventario permanentemente.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1a1a1a',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formToSubmit.submit();
                        }
                    });
                });
            });

            @if($errors->any())
                productModal.show();
            @endif

            // ═══════════════════════════════════════════
            //  GESTIÓN DE TALLAS
            // ═══════════════════════════════════════════
            let tallasProductoId = null;
            const tallasModal = new bootstrap.Modal(document.getElementById('tallasModal'));

            function openTallasModal(productoId, nombre, categoria) {
                tallasProductoId = productoId;
                document.getElementById('tallasProductoNombre').textContent = nombre + ' — ' + categoria;
                document.getElementById('tallasLoading').style.display = 'block';
                document.getElementById('tallasContent').style.display = 'none';
                document.getElementById('tallasStockTotal').textContent = '';
                tallasModal.show();
                cargarTallas(productoId);
            }

            function cargarTallas(productoId) {
                fetch(`/products/${productoId}/tallas/json`)
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('tallasLoading').style.display = 'none';
                        document.getElementById('tallasContent').style.display = 'block';
                        const tbody = document.getElementById('tallasTableBody');
                        tbody.innerHTML = '';

                        if (!data.length) {
                            document.getElementById('tallasTable').style.display = 'none';
                            document.getElementById('sinTallas').style.display = 'block';
                        } else {
                            document.getElementById('tallasTable').style.display = '';
                            document.getElementById('sinTallas').style.display = 'none';
                            let totalStock = 0;

                            data.forEach(pt => {
                                totalStock += pt.stock;
                                const estadoBadge = pt.stock === 0
                                    ? '<span class="badge bg-danger">❌ Agotado</span>'
                                    : (pt.stock < 5
                                        ? '<span class="badge bg-warning text-dark">⚠️ Bajo</span>'
                                        : '<span class="badge bg-success">✅ OK</span>');
                                const activoBadge = pt.activo
                                    ? '<span class="badge bg-success bg-opacity-10 text-success">Activo</span>'
                                    : '<span class="badge bg-secondary bg-opacity-10 text-secondary">Inactivo</span>';

                                tbody.insertAdjacentHTML('beforeend', `
                                                                            <tr id="row-${pt.id}" class="${pt.stock === 0 ? 'table-danger bg-opacity-25' : (pt.stock < 5 ? 'table-warning bg-opacity-25' : '')}">
                                                                                <td><span class="fw-bold fs-5">${pt.talla}</span></td>
                                                                                <td><small class="text-muted">${pt.tipo === 'superior' ? '👕 Superior' : '👖 Inferior'}</small></td>
                                                                                <td>
                                                                                    <input type="number" class="form-control form-control-sm talla-stock-input text-center fw-bold"
                                                                                        id="stock-${pt.id}" value="${pt.stock}" min="0" style="width:80px">
                                                                                </td>
                                                                                <td class="text-center" id="estado-${pt.id}">${estadoBadge}</td>
                                                                                <td class="text-center" id="activo-${pt.id}">${activoBadge}</td>
                                                                                <td class="text-center">
                                                                                    <button class="btn btn-sm btn-success" onclick="guardarTalla(${pt.id}, ${productoId})">
                                                                                        <i class="fas fa-save"></i>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        `);
                            });

                            document.getElementById('tallasStockTotal').innerHTML =
                                `<i class="fas fa-box me-1"></i>Stock total: <strong>${totalStock}</strong> unidades`;
                        }
                    })
                    .catch(() => {
                        document.getElementById('tallasLoading').innerHTML =
                            '<p class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Error al cargar tallas.</p>';
                    });
            }

            function guardarTalla(ptId, productoId) {
                const stockInput = document.getElementById(`stock-${ptId}`);
                const stock = parseInt(stockInput.value);
                if (isNaN(stock) || stock < 0) {
                    Swal.fire('Error', 'El stock debe ser un número mayor o igual a 0.', 'error');
                    return;
                }

                const btn = stockInput.closest('tr').querySelector('button');
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                btn.disabled = true;

                console.log(`Guardando talla: ptId=${ptId}, productoId=${productoId}, stock=${stock}`);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                if (!csrfToken) {
                    console.error("No se encontró el token CSRF en la meta etiqueta 'csrf-token'");
                    Swal.fire('Error', 'No se pudo encontrar el token de seguridad. Recarga la página.', 'error');
                    btn.innerHTML = '<i class="fas fa-save"></i>';
                    btn.disabled = false;
                    return;
                }

                fetch(`/products/${productoId}/tallas/${ptId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        _method: 'PUT',
                        _token: csrfToken,
                        stock: stock,
                        activo: stock > 0 ? 1 : 0
                    })
                })
                    .then(async r => {
                        const text = await r.text();
                        if (!r.ok) {
                            let msg = `HTTP ${r.status}`;
                            try { msg += ': ' + JSON.parse(text).message; } catch { msg += ': ' + text.substring(0, 200); }
                            throw new Error(msg);
                        }
                        return JSON.parse(text);
                    })
                    .then(data => {
                        if (data.success) {
                            btn.innerHTML = '<i class="fas fa-check"></i>';
                            btn.classList.replace('btn-success', 'btn-outline-success');
                            setTimeout(() => {
                                btn.innerHTML = '<i class="fas fa-save"></i>';
                                btn.classList.replace('btn-outline-success', 'btn-success');
                                btn.disabled = false;
                            }, 1500);
                            cargarTallas(productoId);
                        } else {
                            Swal.fire('Error', data.message || 'No se pudo guardar.', 'error');
                            btn.innerHTML = '<i class="fas fa-save"></i>';
                            btn.disabled = false;
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error al guardar', err.message || 'Error de conexión.', 'error');
                        btn.innerHTML = '<i class="fas fa-save"></i>';
                        btn.disabled = false;
                    });
            }

            // Asignar tallas automáticas cuando no hay ninguna
            document.getElementById('btnAsignarTallas')?.addEventListener('click', function () {
                if (!tallasProductoId) return;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Procesando...';
                this.disabled = true;

                fetch(`/products/${tallasProductoId}/tallas`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ auto: true })
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) cargarTallas(tallasProductoId);
                        else Swal.fire('Error', data.message || 'No se pudo asignar.', 'error');
                    })
                    .catch(() => Swal.fire('Error', 'Error de conexión.', 'error'));
            });
        </script>
    @endpush
@endsection