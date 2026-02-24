@extends('layouts.app')

@section('title', 'Inventario por Tallas')

@section('content')
    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-boxes me-2 text-primary"></i>Inventario por Tallas
                </h2>
                <p class="text-muted small mb-0">Gestión de stock por producto y talla</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver a Productos
            </a>
        </div>

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fs-2 fw-bold text-primary">{{ $stats['total'] }}</div>
                    <div class="text-muted small">Total Registros</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fs-2 fw-bold text-success">{{ $stats['disponibles'] }}</div>
                    <div class="text-muted small">Disponibles</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $stats['bajos'] }}</div>
                    <div class="text-muted small">Stock Bajo (&lt;5)</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fs-2 fw-bold text-danger">{{ $stats['agotados'] }}</div>
                    <div class="text-muted small">Agotados</div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('inventario.tallas') }}" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Buscar producto</label>
                        <input type="text" name="producto" class="form-control form-control-sm"
                            placeholder="Nombre del producto..." value="{{ request('producto') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Tipo de talla</label>
                        <select name="tipo" class="form-select form-select-sm">
                            <option value="">Todos los tipos</option>
                            <option value="superior" @selected(request('tipo') === 'superior')>Superior (S - XXL)</option>
                            <option value="inferior" @selected(request('tipo') === 'inferior')>Inferior (28 - 40)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="disponible" @selected(request('estado') === 'disponible')>✅ Disponible</option>
                            <option value="bajo" @selected(request('estado') === 'bajo')>⚠️ Bajo</option>
                            <option value="agotado" @selected(request('estado') === 'agotado')>❌ Agotado</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="{{ route('inventario.tallas') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Producto</th>
                                <th>Categoría</th>
                                <th>Talla</th>
                                <th>Tipo</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Activo</th>
                                <th class="text-center pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($registros as $registro)
                                <tr
                                    class="{{ $registro->stock === 0 ? 'table-danger bg-opacity-25' : ($registro->stock < 5 ? 'table-warning bg-opacity-25' : '') }}">
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $registro->producto->name }}</div>
                                        <div class="text-muted small">{{ $registro->producto->code }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            {{ $registro->producto->category }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold fs-5">{{ $registro->talla->nombre }}</span>
                                    </td>
                                    <td>
                                        @if($registro->talla->tipo === 'superior')
                                            <span class="badge bg-info bg-opacity-10 text-info">👕 Superior</span>
                                        @else
                                            <span class="badge bg-purple bg-opacity-10 text-secondary">👖 Inferior</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="fw-bold {{ $registro->stock === 0 ? 'text-danger' : ($registro->stock < 5 ? 'text-warning' : 'text-success') }}">
                                            {{ $registro->stock }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($registro->stock === 0)
                                            <span class="badge bg-danger">❌ Agotado</span>
                                        @elseif($registro->stock < 5)
                                            <span class="badge bg-warning text-dark">⚠️ Bajo</span>
                                        @else
                                            <span class="badge bg-success">✅ Disponible</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($registro->activo)
                                            <span class="badge bg-success bg-opacity-10 text-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editStockModal" data-pt-id="{{ $registro->id }}"
                                            data-producto="{{ $registro->producto->name }}"
                                            data-talla="{{ $registro->talla->nombre }}" data-stock="{{ $registro->stock }}"
                                            data-activo="{{ $registro->activo ? '1' : '0' }}"
                                            data-url="{{ route('product.tallas.update', [$registro->producto_id, $registro->id]) }}"
                                            title="Editar stock">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        No hay registros de tallas. Ve a
                                        <a href="{{ route('products.index') }}">Productos</a>
                                        y crea productos para asignar tallas automáticamente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Editar Stock --}}
    <div class="modal fade" id="editStockModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i>Editar Stock
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editStockForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            Producto: <strong id="modal-producto"></strong><br>
                            Talla: <strong id="modal-talla"></strong>
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Stock</label>
                            <input type="number" name="stock" id="modal-stock" class="form-control" min="0" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="activo" id="modal-activo" value="1">
                            <label class="form-check-label" for="modal-activo">Talla activa</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-check-lg me-1"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('editStockModal');
            modal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                document.getElementById('modal-producto').textContent = btn.dataset.producto;
                document.getElementById('modal-talla').textContent = btn.dataset.talla;
                document.getElementById('modal-stock').value = btn.dataset.stock;
                document.getElementById('modal-activo').checked = btn.dataset.activo === '1';
                document.getElementById('editStockForm').action = btn.dataset.url;
            });
        });
    </script>
@endpush