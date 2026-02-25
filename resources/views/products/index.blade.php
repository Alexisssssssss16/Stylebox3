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
                <div class="row g-3 mb-4">
                    <div class="col-md-9">
                        <form action="{{ route('products.index') }}" method="GET" id="searchFilterForm">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0"
                                    placeholder="Buscar por código o prenda..." value="{{ request('search') }}">
                                @foreach(request()->except(['search', 'page']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                            </div>
                        </form>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-dark w-100 shadow-sm" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasFilters">
                            <i class="fas fa-filter me-2"></i>Todos los filtros
                        </button>
                    </div>
                </div>

                @if(request()->anyFilled(['brand', 'gender', 'category', 'color', 'talla', 'sort']))
                    <div class="mb-4 d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted small fw-bold">Filtros activos:</span>
                        @if(request('brand')) <span class="badge bg-light text-dark border p-2 fw-normal">Marca:
                        {{ request('brand') }}</span> @endif
                        @if(request('gender')) <span class="badge bg-light text-dark border p-2 fw-normal">Género:
                        {{ request('gender') }}</span> @endif
                        @if(request('category')) <span class="badge bg-light text-dark border p-2 fw-normal">Categoría:
                        {{ request('category') }}</span> @endif
                        @if(request('color')) <span class="badge bg-light text-dark border p-2 fw-normal">Color Filtro</span>
                        @endif
                        <a href="{{ route('products.index') }}" class="btn btn-link btn-sm text-danger p-0 ms-2">Limpiar
                            todo</a>
                    </div>
                @endif

                <div class="card card-custom">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Producto / Marca</th>
                                        <th>Género</th>
                                        <th>Categoría</th>
                                        <th>Precios (S/)</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                        <th class="text-end pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-3 position-relative overflow-hidden shadow-sm"
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
                                                        <div class="d-flex align-items-center gap-2">
                                                            <small class="text-muted">COD: {{ $product->code }}</small>
                                                            <span
                                                                class="badge bg-primary bg-opacity-10 text-primary uppercase-xs"
                                                                style="font-size: 0.65rem;">{{ $product->brand ?? 'Sin Marca' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-muted small fw-medium">{{ $product->gender ?? '-' }}</span>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $product->category }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">S/ {{ number_format($product->price, 2) }}</div>
                                                <small class="text-muted">Costo: {{ number_format($product->cost, 2) }}</small>
                                            </td>
                                            <td>
                                                @if($product->stock <= 5)
                                                    <span class="text-danger fw-bold"><i
                                                            class="fas fa-exclamation-triangle me-1"></i>{{ $product->stock }}</span>
                                                @else
                                                    <span class="fw-bold text-dark">{{ $product->stock }}</span>
                                                @endif
                                                <small class="text-muted ms-1">{{ $product->measurementUnit->code }}</small>
                                            </td>
                                            <td>
                                                @if($product->status)
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill small">Activo</span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill small">Inactivo</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-light text-success me-1 shadow-xs"
                                                    onclick="openTallasModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ addslashes($product->category) }}')"
                                                    title="Gestionar Tallas">
                                                    <i class="fas fa-ruler-horizontal"></i>
                                                </button>
                                                <button class="btn btn-sm btn-light text-primary me-1 shadow-xs"
                                                    onclick="openEditModal({{ $product }})" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                                    class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light text-danger shadow-xs"
                                                        title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted italic">
                                                No se encontraron productos con los filtros seleccionados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($products->hasPages())
                        <div class="card-footer bg-white border-0 py-3">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- SIDEBAR FILTROS (OFFCANVAS) -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFilters"
                aria-labelledby="offcanvasFiltersLabel">
                <div class="offcanvas-header border-bottom py-3">
                    <h5 class="offcanvas-title fw-bold" id="offcanvasFiltersLabel">Todos los filtros</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <form action="{{ route('products.index') }}" method="GET" id="advancedFilterForm">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <div class="accordion accordion-flush" id="accordionFilters">
                            <!-- Ordenar por -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button fw-bold py-3" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseSort">
                                        Ordenar por
                                    </button>
                                </h2>
                                <div id="collapseSort" class="accordion-collapse collapse show">
                                    <div class="accordion-body">
                                        <select name="sort" class="form-select border-0 bg-light">
                                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Más
                                                recientes</option>
                                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                                Precio: Menor a Mayor</option>
                                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                                            <option value="a_z" {{ request('sort') == 'a_z' ? 'selected' : '' }}>A - Z
                                            </option>
                                            <option value="z_a" {{ request('sort') == 'z_a' ? 'selected' : '' }}>Z - A
                                            </option>
                                            <option value="stock_low" {{ request('sort') == 'stock_low' ? 'selected' : '' }}>
                                                Menor Stock</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Marca -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold py-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseBrand">
                                        Marca
                                    </button>
                                </h2>
                                <div id="collapseBrand" class="accordion-collapse collapse">
                                    <div class="accordion-body p-0">
                                        @foreach($uniqueBrands as $brand)
                                            <label
                                                class="list-group-item list-group-item-action d-flex align-items-center border-0 px-4 py-2 cursor-pointer">
                                                <input class="form-check-input me-2 mt-0" type="radio" name="brand"
                                                    value="{{ $brand }}" {{ request('brand') == $brand ? 'checked' : '' }}>
                                                <span class="small">{{ $brand }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Género -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold py-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseGender">
                                        Género
                                    </button>
                                </h2>
                                <div id="collapseGender" class="accordion-collapse collapse">
                                    <div class="accordion-body p-0 text-center py-3">
                                        <div class="btn-group btn-group-sm w-75 mx-auto" role="group">
                                            @foreach(['Hombre', 'Mujer', 'Unisex', 'Niño/a'] as $g)
                                                <input type="radio" class="btn-check" name="gender" id="gender_{{ $g }}"
                                                    value="{{ $g }}" {{ request('gender') == $g ? 'checked' : '' }}>
                                                <label class="btn btn-outline-dark" for="gender_{{ $g }}">{{ $g }}</label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Categoría -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold py-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseCategory">
                                        Categoría
                                    </button>
                                </h2>
                                <div id="collapseCategory" class="accordion-collapse collapse">
                                    <div class="accordion-body p-0">
                                        @foreach($uniqueCategories as $cat)
                                            <label
                                                class="list-group-item list-group-item-action d-flex align-items-center border-0 px-4 py-2">
                                                <input class="form-check-input me-2 mt-0" type="radio" name="category"
                                                    value="{{ $cat }}" {{ request('category') == $cat ? 'checked' : '' }}>
                                                <span class="small">{{ $cat }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Color -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold py-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseColor">
                                        Color
                                    </button>
                                </h2>
                                <div id="collapseColor" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                                            @foreach($colors as $color)
                                                <div class="color-filter-item">
                                                    <input type="radio" class="btn-check" name="color"
                                                        id="color_{{ $color->id }}" value="{{ $color->id }}" {{ request('color') == $color->id ? 'checked' : '' }}>
                                                    <label class="color-swatch-filter" for="color_{{ $color->id }}"
                                                        style="background-color: {{ $color->hex_code }};"
                                                        title="{{ $color->name }}"></label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Talla -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold py-3" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseTalla">
                                        Talla
                                    </button>
                                </h2>
                                <div id="collapseTalla" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                                            @foreach($tallas as $talla)
                                                <input type="radio" class="btn-check" name="talla" id="talla_{{ $talla->id }}"
                                                    value="{{ $talla->id }}" {{ request('talla') == $talla->id ? 'checked' : '' }}>
                                                <label class="btn btn-outline-secondary btn-xs-filter"
                                                    for="talla_{{ $talla->id }}">{{ $talla->nombre }}</label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="offcanvas-footer border-top p-3 d-flex gap-2">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-dark w-50 fw-bold">Limpiar
                                filtros</a>
                            <button type="submit" class="btn btn-danger w-50 fw-bold">Mostrar resultados</button>
                        </div>
                    </form>
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
                            <div class="modal-body pb-0">
                                <div class="row g-3">
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
                                        <input type="text" class="form-control" id="category" name="category"
                                            placeholder="Ej: Polos" list="categoriesList">
                                        <datalist id="categoriesList">
                                            @foreach($uniqueCategories as $c) <option value="{{ $c }}"> @endforeach
                                        </datalist>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Marca</label>
                                        <input type="text" class="form-control" id="brand" name="brand"
                                            placeholder="Ej: Hypnotic" list="brandsList">
                                        <datalist id="brandsList">
                                            @foreach($uniqueBrands as $b) <option value="{{ $b }}"> @endforeach
                                        </datalist>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Género</label>
                                        <select class="form-select" id="gender" name="gender">
                                            <option value="Unisex">Unisex</option>
                                            <option value="Hombre">Hombre</option>
                                            <option value="Mujer">Mujer</option>
                                            <option value="Niño/a">Niño/a</option>
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
                                        <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">
                                            Inventario y
                                            Costos</h6>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Unidad Medida</label>
                                        <select class="form-select" id="measurement_unit_id" name="measurement_unit_id"
                                            required>
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
                                            <input type="number" step="0.01" class="form-control border-start-0 ps-1"
                                                id="cost" name="cost" required min="0">
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
                                            <input class="form-check-input ms-0 me-2" type="checkbox" id="status"
                                                name="status" value="1" checked>
                                            <label class="form-check-label fw-bold" for="status">Producto Disponible para
                                                Venta</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary btn-primary-custom px-4">Guardar
                                    Producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

    {{-- CSS ADICIONAL PARA FILTROS PREMIUM --}}
    @push('styles')
    <style>
        .color-swatch-filter {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
            border: 2px solid #eee;
            transition: all 0.2s;
        }
        .btn-check:checked + .color-swatch-filter {
            transform: scale(1.2);
            border-color: #000;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .btn-xs-filter {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }
        .uppercase-xs {
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .cursor-pointer { cursor: pointer; }
        .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .offcanvas-body .accordion-button:not(.collapsed) {
            background-color: transparent;
            color: #000;
        }
        .offcanvas-body .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0,0,0,.125);
        }
        .list-group-item-action:hover {
            background-color: #f8f9fa;
        }
    </style>
    @endpush

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
                                {{-- Formulario para añadir nueva variante --}}
                                <div class="card bg-light border-0 mb-4">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-3 small text-uppercase">Añadir Nueva Variante</h6>
                                        <form id="formNuevaVariante" class="row g-2 align-items-end">
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Color</label>
                                                <select class="form-select form-select-sm" id="nuevaVarianteColor" required>
                                                    <option value="">Seleccione Color...</option>
                                                    @foreach($colors as $color)
                                                        <option value="{{ $color->id }}" data-hex="{{ $color->hex_code }}">
                                                            {{ $color->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Talla</label>
                                                <select class="form-select form-select-sm" id="nuevaVarianteTalla" required>
                                                    <option value="">Seleccione Talla...</option>
                                                    <optgroup label="Tallas Superiores">
                                                        @foreach($tallasSuperiores as $t)
                                                            <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                    <optgroup label="Tallas Inferiores">
                                                        @foreach($tallasInferiores as $t)
                                                            <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold">Stock</label>
                                                <input type="number" class="form-control form-control-sm"
                                                    id="nuevaVarianteStock" value="0" min="0" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-dark btn-sm w-100"
                                                    id="btnGuardarNuevaVariante">
                                                    <i class="fas fa-plus me-1"></i>Añadir
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="alert alert-info border-0 py-2 mb-3" style="background:#f0fdf4;">
                                    <i class="fas fa-info-circle text-success me-1"></i>
                                    <small>El stock total se sincroniza con el inventario general al guardar.</small>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="tallasTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Color</th>
                                                <th>Talla</th>
                                                <th style="width:100px">Stock</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
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
                document.getElementById('category').value = product.category || '';
                document.getElementById('brand').value = product.brand || '';
                document.getElementById('gender').value = product.gender || 'Unisex';
                document.getElementById('description').value = product.description || '';
                document.getElementById('cost').value = product.cost;
                document.getElementById('price').value = product.price;
                document.getElementById('stock').value = product.stock;
                document.getElementById('measurement_unit_id').value = product.measurement_unit_id;
                document.getElementById('status').checked = product.status == 1;

                productModal.show();
            }

            // Sync search and filters
            document.getElementById('advancedFilterForm').addEventListener('submit', function() {
                const searchInput = document.querySelector('input[name="search"]');
                if(searchInput && searchInput.value) {
                    let hiddenSearch = this.querySelector('input[name="search"]');
                    if(!hiddenSearch) {
                        hiddenSearch = document.createElement('input');
                        hiddenSearch.type = 'hidden';
                        hiddenSearch.name = 'search';
                        this.appendChild(hiddenSearch);
                    }
                    hiddenSearch.value = searchInput.value;
                }
            });

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
                                            ? '<span class="badge bg-danger">Agotado</span>'
                                            : (pt.stock < 5
                                                ? '<span class="badge bg-warning text-dark">Bajo</span>'
                                                : '<span class="badge bg-success">OK</span>');

                                        const colorBadge = `<span class="badge bg-light text-dark border d-flex align-items-center gap-1">
                                                    <span style="width:10px; height:10px; border-radius:50%; background:${pt.color_hex || '#ccc'}; border:1px solid #ddd;"></span>
                                                    ${pt.color_name}
                                                </span>`;

                                        tbody.insertAdjacentHTML('beforeend', `
                                                    <tr id="row-${pt.id}" class="${pt.stock === 0 ? 'table-danger bg-opacity-25' : (pt.stock < 5 ? 'table-warning bg-opacity-25' : '')}">
                                                        <td>${colorBadge}</td>
                                                        <td><span class="fw-bold">${pt.talla}</span></td>
                                                        <td>
                                                            <input type="number" class="form-control form-control-sm talla-stock-input text-center fw-bold"
                                                                id="stock-${pt.id}" value="${pt.stock}" min="0" style="width:80px">
                                                        </td>
                                                        <td class="text-center">${estadoBadge}</td>
                                                        <td class="text-center">
                                                            <div class="btn-group">
                                                                <button class="btn btn-sm btn-success" onclick="guardarTalla(${pt.id}, ${productoId})" title="Guardar">
                                                                    <i class="fas fa-save"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarTalla(${pt.id}, ${productoId})" title="Eliminar">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </div>
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
                            .catch(() => Swal.fire('Error', 'Error de conexión.', 'error'))
                            .finally(() => {
                                this.innerHTML = '<i class="fas fa-magic me-1"></i>Asignar tallas automáticamente';
                                this.disabled = false;
                            });
                    });

                    // Guardar Nueva Variante
                    document.getElementById('btnGuardarNuevaVariante').addEventListener('click', function () {
                        const colorId = document.getElementById('nuevaVarianteColor').value;
                        const tallaId = document.getElementById('nuevaVarianteTalla').value;
                        const stock = document.getElementById('nuevaVarianteStock').value;

                        if (!colorId || !tallaId) {
                            Swal.fire('Atención', 'Seleccione color y talla para la nueva variante.', 'warning');
                            return;
                        }

                        this.disabled = true;
                        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                        fetch(`/products/${tallasProductoId}/tallas`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                color_id: colorId,
                                talla_id: tallaId,
                                stock: stock,
                                activo: 1
                            })
                        })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    cargarTallas(tallasProductoId);
                                    document.getElementById('formNuevaVariante').reset();
                                } else {
                                    Swal.fire('Error', data.message || 'Error al crear variante.', 'error');
                                }
                            })
                            .catch(err => Swal.fire('Error', 'Error de conexión.', 'error'))
                            .finally(() => {
                                this.disabled = false;
                                this.innerHTML = '<i class="fas fa-plus me-1"></i>Añadir';
                            });
                    });

                    function eliminarTalla(ptId, productoId) {
                        Swal.fire({
                            title: '¿Eliminar variante?',
                            text: "Se borrará esta combinación de color/talla permanentemente.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch(`/products/${productoId}/tallas/${ptId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                })
                                    .then(r => r.json())
                                    .then(data => {
                                        if (data.success) cargarTallas(productoId);
                                        else Swal.fire('Error', data.message, 'error');
                                    })
                                    .catch(() => Swal.fire('Error', 'Error de conexión.', 'error'));
                            }
                        });
                    }
                </script>
            @endpush
@endsection