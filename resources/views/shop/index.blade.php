@extends('layouts.shop')

@section('title', 'Catálogo — StyleBox')

@push('styles')
    <style>
        /* ============================================
           HERO BANNER
        ============================================ */
        .catalog-hero {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
            color: #fff;
            padding: 3.5rem 0 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .catalog-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(201, 168, 76, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .catalog-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .catalog-hero h1 span {
            color: var(--accent);
        }

        .catalog-hero p {
            color: rgba(255, 255, 255, 0.65);
            font-size: 1rem;
        }

        /* ============================================
           FILTER BAR
        ============================================ */
        .filter-bar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 0;
            position: sticky;
            top: 64px;
            z-index: 100;
            transition: background 0.3s;
        }

        .filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            background: var(--surface2);
            border: 1.5px solid transparent;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Outfit', sans-serif;
            white-space: nowrap;
        }

        .filter-btn:hover {
            background: var(--border);
        }

        .filter-btn.active {
            background: var(--primary);
            color: var(--bg);
            border-color: var(--primary);
        }

        .filter-dropdown-menu {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
            padding: 1.25rem;
            min-width: 260px;
        }

        [data-theme="dark"] .filter-dropdown-menu {
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
        }

        .filter-dropdown-menu .fw-bold {
            color: var(--text);
        }

        .filter-dropdown-menu .form-control {
            background: var(--surface2);
            border: 1.5px solid var(--border);
            color: var(--text);
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
        }

        .filter-dropdown-menu .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-light);
            background: var(--surface);
        }

        .talla-btn {
            padding: 8px 4px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            border: 1.5px solid var(--border);
            background: var(--surface);
            color: var(--text);
            text-decoration: none;
            display: block;
        }

        .talla-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .talla-btn.active-talla {
            background: var(--primary);
            color: var(--bg);
            border-color: var(--primary);
        }

        .color-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text);
            font-size: 0.82rem;
            font-weight: 500;
            transition: background 0.2s;
        }

        .color-option:hover {
            background: var(--surface2);
            color: var(--text);
        }

        .color-option.active-color {
            background: var(--accent-light);
            font-weight: 700;
        }

        .color-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .cat-link {
            display: block;
            padding: 8px 10px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-2);
            font-size: 0.87rem;
            transition: all 0.2s;
            font-weight: 500;
        }

        .cat-link:hover {
            background: var(--surface2);
            color: var(--text);
        }

        .cat-link.active-cat {
            background: var(--accent-light);
            color: var(--accent);
            font-weight: 700;
        }

        /* Active filter pills */
        .filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text);
        }

        .filter-pill .remove-pill {
            cursor: pointer;
            color: var(--text-3);
            font-size: 1.1rem;
            line-height: 1;
            transition: color 0.2s;
        }

        .filter-pill .remove-pill:hover {
            color: var(--danger);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 42px;
            height: 22px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--border);
            transition: .3s;
            border-radius: 22px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 3px;
            bottom: 3px;
            background: white;
            transition: .3s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background: var(--success);
        }

        input:checked+.slider:before {
            transform: translateX(20px);
        }

        /* ============================================
           RESULTS HEADER
        ============================================ */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .results-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
        }

        .results-count {
            color: var(--text-3);
            font-size: 0.85rem;
            font-weight: 400;
        }

        .sort-select {
            padding: 7px 14px;
            border-radius: 50px;
            border: 1.5px solid var(--border);
            background: var(--surface);
            color: var(--text);
            font-size: 0.82rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 500;
            cursor: pointer;
            outline: none;
        }

        .sort-select:focus {
            border-color: var(--accent);
        }

        /* ============================================
           PRODUCT GRID  
        ============================================ */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.25rem;
        }

        @media (max-width: 576px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }

            .catalog-hero {
                padding: 2rem 0 1.5rem;
            }
        }

        @media (min-width: 1400px) {
            .products-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px dashed var(--border);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--text-3);
            margin-bottom: 1rem;
        }

        .empty-state h4 {
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-2);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        /* Clear all */
        .clear-all-btn {
            background: none;
            border: none;
            color: var(--text-2);
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: underline;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
        }

        .clear-all-btn:hover {
            color: var(--danger);
        }

        /* ============================================
           PAGINATION
        ============================================ */
        .pagination .page-link {
            background: var(--surface);
            border-color: var(--border);
            color: var(--text);
            font-weight: 500;
            border-radius: 8px !important;
            margin: 0 2px;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--bg);
        }

        .pagination .page-link:hover {
            background: var(--surface2);
            color: var(--text);
        }

        /* ============================================
           MOBILE FEED (TikTok)
        ============================================ */
        @media (max-width: 767.98px) {
            body {
                overflow: hidden;
            }

            .mobile-feed-container {
                height: calc(100vh - 64px - var(--bottom-nav-height));
                overflow-y: scroll;
                scroll-snap-type: y mandatory;
                scroll-behavior: smooth;
            }

            .feed-item {
                height: calc(100vh - 64px - var(--bottom-nav-height));
                position: relative;
                background: #000;
                display: flex;
                justify-content: center;
                align-items: center;
                overflow: hidden;
                scroll-snap-align: start;
            }

            .feed-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                opacity: 0.88;
            }

            .feed-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 65%;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.95), transparent);
                pointer-events: none;
            }

            .feed-content {
                position: absolute;
                bottom: 24px;
                left: 20px;
                right: 72px;
                color: #fff;
                z-index: 2;
            }

            .feed-actions {
                position: absolute;
                bottom: 44px;
                right: 14px;
                display: flex;
                flex-direction: column;
                gap: 18px;
                z-index: 2;
            }

            .action-btn {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(8px);
                border: 1px solid rgba(255, 255, 255, 0.25);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
                cursor: pointer;
                transition: 0.2s;
                text-decoration: none;
            }

            .action-btn:active {
                transform: scale(0.9);
            }

            .action-btn:hover {
                color: #fff;
            }
        }

        @media (min-width: 768px) {
            .mobile-feed-container {
                display: none !important;
            }

            body {
                overflow: auto;
            }
        }
    </style>
@endpush

@section('content')

    {{-- ===== MOBILE FEED ===== --}}
    <div class="mobile-feed-container d-md-none">
        @forelse($products as $product)
            <div class="feed-item">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="feed-img" alt="{{ $product->name }}">
                @else
                    <div class="feed-img d-flex align-items-center justify-content-center bg-dark text-white-50">
                        <i class="fas fa-tshirt fa-5x opacity-25"></i>
                    </div>
                @endif

                <div class="feed-overlay"></div>

                <div class="feed-content">
                    <span class="product-badge badge-new mb-2"
                        style="font-size:0.65rem;">{{ $product->category ?? 'General' }}</span>
                    <h2 class="fw-bold mb-1" style="font-size: 1.4rem; text-shadow: 0 2px 6px rgba(0,0,0,0.6);">
                        {{ $product->name }}</h2>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="stars-icons" style="color:#c9a84c; font-size:0.75rem;">★★★★★</div>
                        <span class="fw-bold" style="font-size: 1.25rem;">S/ {{ number_format($product->price, 2) }}</span>
                        @if($product->stock < 5 && $product->stock > 0)
                            <span class="product-badge badge-low">¡{{ $product->stock }} left!</span>
                        @elseif($product->stock <= 0)
                            <span class="product-badge badge-out">Agotado</span>
                        @endif
                    </div>
                    <p class="small mb-0"
                        style="color:rgba(255,255,255,0.7); display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $product->description }}</p>
                </div>

                <div class="feed-actions">
                    <div class="text-center">
                        <button class="action-btn" onclick="addToCart({{ $product->id }})" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            <i class="fas fa-plus"></i>
                        </button>
                        <span class="small text-white mt-1 d-block" style="font-size:0.65rem;">Agregar</span>
                    </div>
                    <div class="text-center">
                        <button class="action-btn" style="background:rgba(201,168,76,0.4);" onclick="buyNow({{ $product->id }})"
                            {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                        <span class="small text-white mt-1 d-block" style="font-size:0.65rem;">Comprar</span>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('shop.show', $product) }}" class="action-btn">
                            <i class="fas fa-eye"></i>
                        </a>
                        <span class="small text-white mt-1 d-block" style="font-size:0.65rem;">Ver</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="feed-item">
                <div class="text-center text-white">
                    <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                    <h4>No hay productos</h4>
                </div>
            </div>
        @endforelse
    </div>

    {{-- ===== DESKTOP VIEW ===== --}}
    <div class="d-none d-md-block">

        {{-- HERO --}}
        <div class="catalog-hero">
            <div class="container">
                <h1>Moda que <span>inspira</span>, estilo que <span>define</span></h1>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="filter-bar">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center gap-2">

                    {{-- Todos los filtros --}}
                    <div class="dropdown">
                        <button class="filter-btn {{ request()->hasAny(['min_price', 'max_price']) ? 'active' : '' }}"
                            type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-sliders-h"></i> Filtros
                        </button>
                        <div class="dropdown-menu filter-dropdown-menu" style="width:280px;">
                            <form action="{{ route('shop.index') }}" method="GET">
                                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if(request('category')) <input type="hidden" name="category"
                                value="{{ request('category') }}"> @endif
                                @if(request('talla')) <input type="hidden" name="talla" value="{{ request('talla') }}">
                                @endif
                                @if(request('gender')) <input type="hidden" name="gender" value="{{ request('gender') }}"> @endif
                                @if(request('color')) <input type="hidden" name="color" value="{{ request('color') }}">
                                @endif

                                <p class="fw-bold mb-3"
                                    style="font-size:0.8rem; letter-spacing:1px; text-transform:uppercase; color:var(--text-3);">
                                    Rango de Precio</p>
                                <div class="d-flex gap-2 mb-4">
                                    <input type="number" name="min_price" class="form-control" placeholder="Mín S/"
                                        value="{{ request('min_price') }}" min="0">
                                    <input type="number" name="max_price" class="form-control" placeholder="Máx S/"
                                        value="{{ request('max_price') }}" min="0">
                                </div>

                                <div class="d-flex gap-2 align-items-center justify-content-between mb-2">
                                    <span class="fw-semibold small">Envío Hoy</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" {{ request('envio_hoy') ? 'checked' : '' }}
                                            onchange="Swal.fire({title:'Próximamente',icon:'info',timer:1200,showConfirmButton:false})">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                <div class="d-flex gap-2 align-items-center justify-content-between mb-3">
                                    <span class="fw-semibold small">Solo Ofertas</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox"
                                            onchange="Swal.fire({title:'Próximamente',icon:'info',timer:1200,showConfirmButton:false})">
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <button type="submit" class="btn w-100 fw-bold"
                                    style="background:var(--primary);color:var(--bg);border-radius:50px;">Aplicar
                                    filtros</button>
                            </form>
                        </div>
                    </div>

                    {{-- Género --}}
                    <div class="dropdown">
                        <button class="filter-btn {{ request('gender') ? 'active' : '' }}" type="button"
                            data-bs-toggle="dropdown">
                            Género <i class="fas fa-chevron-down ms-1" style="font-size:0.7rem;"></i>
                        </button>
                        <div class="dropdown-menu filter-dropdown-menu">
                            <a href="{{ request()->fullUrlWithQuery(['gender' => null]) }}"
                                class="cat-link {{ !request('gender') ? 'active-cat' : '' }}">
                                <i class="fas fa-venus-mars me-2"></i>Todos
                            </a>
                            @foreach($genders as $gen)
                                <a href="{{ request()->fullUrlWithQuery(['gender' => $gen]) }}"
                                    class="cat-link {{ request('gender') == $gen ? 'active-cat' : '' }}">
                                    <i class="fas fa-{{ $gen == 'Hombre' ? 'mars' : ($gen == 'Mujer' ? 'venus' : 'unisex') }} me-2"></i>{{ $gen }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Categorías --}}
                    <div class="dropdown">
                        <button class="filter-btn {{ request('category') ? 'active' : '' }}" type="button"
                            data-bs-toggle="dropdown">
                            @if(request('gender'))
                                Categorías de {{ request('gender') }}
                            @else
                                Categorías
                            @endif
                            <i class="fas fa-chevron-down ms-1" style="font-size:0.7rem;"></i>
                        </button>
                        <div class="dropdown-menu filter-dropdown-menu">
                            <a href="{{ route('shop.index') }}"
                                class="cat-link {{ !request('category') ? 'active-cat' : '' }}">
                                <i class="fas fa-th-large me-2"></i>Todos
                            </a>
                            @foreach($categories as $cat)
                                <a href="{{ request()->fullUrlWithQuery(['category' => $cat]) }}"
                                    class="cat-link {{ request('category') == $cat ? 'active-cat' : '' }}">
                                    <i class="fas fa-tag me-2"></i>{{ $cat }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tallas --}}
                    <div class="dropdown">
                        <button class="filter-btn {{ request('talla') ? 'active' : '' }}" type="button"
                            data-bs-toggle="dropdown">
                            Tallas <i class="fas fa-chevron-down ms-1" style="font-size:0.7rem;"></i>
                        </button>
                        <div class="dropdown-menu filter-dropdown-menu">
                            <div class="row g-2">
                                @foreach($tallas as $t)
                                    <div class="col-4">
                                        <a class="talla-btn {{ request('talla') == $t->id ? 'active-talla' : '' }}"
                                            href="{{ request()->fullUrlWithQuery(['talla' => $t->id]) }}">
                                            {{ $t->nombre }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Colores --}}
                    <div class="dropdown">
                        <button class="filter-btn {{ request('color') ? 'active' : '' }}" type="button"
                            data-bs-toggle="dropdown">
                            Color <i class="fas fa-chevron-down ms-1" style="font-size:0.7rem;"></i>
                        </button>
                        <div class="dropdown-menu filter-dropdown-menu">
                            @foreach($colors as $color)
                                <a href="{{ request()->fullUrlWithQuery(['color' => $color->id]) }}"
                                    class="color-option {{ request('color') == $color->id ? 'active-color' : '' }}">
                                    <span class="color-dot" style="background-color: {{ $color->hex_code ?? '#ccc' }}"></span>
                                    {{ $color->name }}
                                    @if(request('color') == $color->id)
                                        <i class="fas fa-check ms-auto" style="font-size:0.7rem; color:var(--accent);"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Marca --}}
                    @if(isset($brands) && $brands->count())
                        <div class="dropdown">
                            <button class="filter-btn {{ request('brand') ? 'active' : '' }}" type="button"
                                data-bs-toggle="dropdown">
                                Marca <i class="fas fa-chevron-down ms-1" style="font-size:0.7rem;"></i>
                            </button>
                            <div class="dropdown-menu filter-dropdown-menu">
                                @foreach($brands as $brand)
                                    <a href="{{ request()->fullUrlWithQuery(['brand' => $brand]) }}"
                                        class="cat-link {{ request('brand') == $brand ? 'active-cat' : '' }}">
                                        {{ $brand }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Separator --}}
                    <div class="ms-auto"></div>

                    {{-- Sort --}}
                    <form action="{{ route('shop.index') }}" method="GET" id="sortForm">
                        @foreach(request()->except('sort') as $key => $val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        <select name="sort" class="sort-select" onchange="document.getElementById('sortForm').submit()">
                            <option value="" {{ !request('sort') ? 'selected' : '' }}>Ordenar por</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Precio: Menor a
                                Mayor</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Precio: Mayor a
                                Menor</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nombre A-Z</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Más Nuevos</option>
                        </select>
                    </form>
                </div>

                {{-- Active Pills --}}
                @php $hasFilters = request()->hasAny(['talla', 'color', 'category', 'min_price', 'max_price', 'search', 'brand', 'gender']); @endphp
                @if($hasFilters)
                    <div class="d-flex align-items-center flex-wrap gap-2 mt-3">
                        @if(request('gender'))
                            <div class="filter-pill">
                                <i class="fas fa-venus-mars" style="font-size:0.7rem;"></i> {{ request('gender') }}
                                <span class="remove-pill"
                                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['gender' => null]) }}'">×</span>
                            </div>
                        @endif
                        @if(request('search'))
                            <div class="filter-pill">
                                <i class="fas fa-search" style="font-size:0.7rem;"></i> "{{ request('search') }}"
                                <span class="remove-pill"
                                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['search' => null]) }}'">×</span>
                            </div>
                        @endif
                        @if(request('category'))
                            <div class="filter-pill">
                                <i class="fas fa-tag" style="font-size:0.7rem;"></i> {{ request('category') }}
                                <span class="remove-pill"
                                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['category' => null]) }}'">×</span>
                            </div>
                        @endif
                        @if(request('talla'))
                            @php $tName = $tallas->where('id', request('talla'))->first()?->nombre ?? 'Talla'; @endphp
                            <div class="filter-pill">Talla {{ $tName }} <span class="remove-pill"
                                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['talla' => null]) }}'">×</span>
                            </div>
                        @endif
                        @if(request('color'))
                            @php $cName = $colors->where('id', request('color'))->first()?->name ?? 'Color'; @endphp
                            <div class="filter-pill">Color {{ $cName }} <span class="remove-pill"
                                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['color' => null]) }}'">×</span>
                            </div>
                        @endif
                        @if(request('min_price') || request('max_price'))
                            <div class="filter-pill">
                                S/{{ request('min_price', 0) }} – S/{{ request('max_price', '∞') }}
                                <span class="remove-pill"
                                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}'">×</span>
                            </div>
                        @endif
                        <button class="clear-all-btn" onclick="window.location.href='{{ route('shop.index') }}'">Limpiar
                            todo</button>
                    </div>
                @endif
            </div>
        </div>

        {{-- PRODUCT GRID --}}
        <div class="container my-4">

            <div class="results-header">
                <div class="results-title">
                    Resultados <span class="results-count">({{ $products->total() }} productos)</span>
                </div>
            </div>

            <div class="products-grid">
                @forelse($products as $product)
                    @php
                        $isNew = $product->created_at >= now()->subDays(14);
                        $isLow = $product->stock > 0 && $product->stock < 5;
                        $isOut = $product->stock <= 0;
                        // Simulate "hot" for first few products or if sold a lot
                        $isHot = $product->id % 7 === 0;
                    @endphp

                    <div class="product-card">
                        <div class="product-img-wrapper">
                            {{-- Image --}}
                            <a href="{{ route('shop.show', $product) }}">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy" 
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="product-placeholder" style="display:none;">
                                        <i class="fas fa-tshirt"></i>
                                    </div>
                                @else
                                    <div class="product-placeholder">
                                        <i class="fas fa-tshirt"></i>
                                    </div>
                                @endif
                            </a>

                            {{-- Badges --}}
                            <div class="badge-wrap">
                                @if($isOut)
                                    <span class="product-badge badge-out"><i class="fas fa-times"></i> Agotado</span>
                                @elseif($isLow)
                                    <span class="product-badge badge-low"><i class="fas fa-fire"></i> Stock bajo</span>
                                @endif
                                @if($isNew && !$isOut)
                                    <span class="product-badge badge-new">Nuevo</span>
                                @endif
                                @if($isHot && !$isOut)
                                    <span class="product-badge badge-hot"><i class="fas fa-star"></i> + Vendido</span>
                                @endif
                            </div>

                            {{-- Wish --}}
                            <button class="wish-btn" title="Favorito" onclick="toggleWish(this)">
                                <i class="far fa-heart"></i>
                            </button>

                            {{-- Quick Add --}}
                                @if(!$isOut)
                                <div class="quick-add-overlay">
                                    <button class="quick-add-btn" onclick="addToCart({{ $product->id }}, {{ $product->producto_tallas_count > 0 ? 'true' : 'false' }})">
                                        <i class="fas fa-plus me-1"></i> Agregar al carrito
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div class="product-info">
                            <div class="product-category">{{ $product->category ?? 'General' }}</div>
                            <a href="{{ route('shop.show', $product) }}" class="product-name"
                                title="{{ $product->name }}">{{ $product->name }}</a>
                            <div class="product-stars">
                                <div class="stars-icons">★★★★<span style="opacity:0.4;">★</span></div>
                                <span class="star-score">4.5</span>
                                <span class="review-count">({{ rand(12, 240) }})</span>
                            </div>
                            <div class="product-price-row">
                                <div>
                                    <div class="price-current">S/ {{ number_format($product->price, 2) }}</div>
                                    @if($isHot)
                                        <div class="price-original">S/ {{ number_format($product->price * 1.2, 2) }}</div>
                                    @endif
                                </div>
                                <button class="add-btn" onclick="addToCart({{ $product->id }}, {{ $product->producto_tallas_count > 0 ? 'true' : 'false' }})" {{ $isOut ? 'disabled' : '' }}
                                    title="Agregar">
                                    <i class="fas fa-plus" style="font-size:0.85rem;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="fas fa-search"></i>
                        <h4>No encontramos productos</h4>
                        <p>Intenta con otra categoría o término de búsqueda.</p>
                        <a href="{{ route('shop.index') }}" class="btn fw-bold px-4"
                            style="background:var(--primary);color:var(--bg);border-radius:50px;">Ver todo el catálogo</a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-5 d-flex justify-content-center">
                {{ $products->appends(request()->query())->links() }}
            </div>


    {{-- ===== SIZE SELECTION MODAL ===== --}}
    <div class="modal fade" id="sizeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0 bg-dark text-white py-3">
                    <h5 class="modal-title fw-bold">Seleccionar Talla</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="modalLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Cargando opciones...</p>
                    </div>
                    
                    <div id="modalContent" style="display: none;">
                        <h6 id="modalProductName" class="fw-bold mb-1 fs-5"></h6>
                        <p id="modalProductPrice" class="text-accent fw-bold mb-3"></p>
                        
                        <div id="colorSection" class="mb-3">
                            <label class="small fw-bold text-uppercase text-muted mb-2">Selecciona Color:</label>
                            <div id="colorOptionsGrid" class="d-flex flex-wrap gap-2">
                                <!-- Colores via JS -->
                            </div>
                        </div>

                        <div id="sizeSection" class="mb-4" style="display: none;">
                            <label class="small fw-bold text-uppercase text-muted mb-2">Selecciona Talla:</label>
                            <div id="sizeOptionsGrid" class="d-flex flex-wrap gap-2">
                                <!-- Tallas via JS -->
                            </div>
                        </div>
                        
                        <input type="hidden" id="selectedProductId">
                        <input type="hidden" id="selectedColorId">
                        <input type="hidden" id="selectedTallaId">
                        
                        <button type="button" id="btnConfirmSize" class="btn btn-dark w-100 py-3 fw-bold" style="border-radius: 12px;" disabled onclick="confirmSizeAndAdd()">
                            Agregar al Carrito
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TOAST NOTIFICATION ===== --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000;">
        <div id="cartToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i> <span id="toastMessage">Producto añadido</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function toggleWish(btn) {
            const icon = btn.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.replace('far', 'fas');
                btn.style.color = '#e53e3e';
            } else {
                icon.classList.replace('fas', 'far');
                btn.style.color = '';
            }
        }

        const sizeModal = new bootstrap.Modal(document.getElementById('sizeModal'));
        const cartToast = new bootstrap.Toast(document.getElementById('cartToast'));

        let currentColorsData = [];

        function addToCart(productId, hasSizes) {
            if (hasSizes) {
                openSizeModal(productId);
            } else {
                sendAddToCart(productId);
            }
        }

        function openSizeModal(productId) {
            document.getElementById('selectedProductId').value = productId;
            document.getElementById('selectedColorId').value = '';
            document.getElementById('selectedTallaId').value = '';
            
            const btnConfirm = document.getElementById('btnConfirmSize');
            btnConfirm.disabled = true;
            btnConfirm.innerHTML = 'Agregar al Carrito';
            
            document.getElementById('modalLoading').style.display = 'block';
            document.getElementById('modalContent').style.display = 'none';
            document.getElementById('sizeSection').style.display = 'none';
            document.getElementById('colorOptionsGrid').innerHTML = '';
            document.getElementById('sizeOptionsGrid').innerHTML = '';
            
            sizeModal.show();

            fetch(`/shop/product/${productId}/tallas`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalProductName').textContent = data.product_name;
                        document.getElementById('modalProductPrice').textContent = `S/ ${data.product_price.toFixed(2)}`;
                        currentColorsData = data.colors;

                        const colorGrid = document.getElementById('colorOptionsGrid');
                        if (data.colors.length === 0) {
                            colorGrid.innerHTML = '<p class="text-danger small">No hay opciones disponibles.</p>';
                        } else {
                            data.colors.forEach(c => {
                                const chip = document.createElement('div');
                                chip.className = 'color-chip';
                                chip.title = c.color_nombre;
                                chip.style.backgroundColor = c.color_hex;
                                chip.onclick = () => selectColorItem(chip, c.color_id);
                                colorGrid.appendChild(chip);
                            });
                        }
                        document.getElementById('modalLoading').style.display = 'none';
                        document.getElementById('modalContent').style.display = 'block';
                    }
                }).catch(err => {
                    console.error(err);
                    alert("Error al cargar las opciones.");
                    sizeModal.hide();
                });
        }

        function selectColorItem(element, colorId) {
            document.querySelectorAll('.color-chip').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
            
            document.getElementById('selectedColorId').value = colorId;
            document.getElementById('selectedTallaId').value = '';
            document.getElementById('btnConfirmSize').disabled = true;

            const colorData = currentColorsData.find(c => c.color_id == colorId);
            const sizeGrid = document.getElementById('sizeOptionsGrid');
            sizeGrid.innerHTML = '';

            if (colorData && colorData.tallas.length > 0) {
                colorData.tallas.forEach(t => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-outline-dark px-3 py-2 fw-bold size-chip';
                    btn.style.borderRadius = '10px';
                    btn.style.minWidth = '60px';
                    if (t.stock <= 0) {
                        btn.disabled = true;
                        btn.innerHTML = `${t.nombre} <br><small style="font-size:0.6rem;">Agotado</small>`;
                    } else {
                        btn.textContent = t.nombre;
                        btn.onclick = () => selectSizeItem(btn, t.id);
                    }
                    sizeGrid.appendChild(btn);
                });
                document.getElementById('sizeSection').style.display = 'block';
            }
        }

        function selectSizeItem(element, tallaId) {
            document.querySelectorAll('.size-chip').forEach(el => el.classList.remove('btn-dark', 'text-white'));
            document.querySelectorAll('.size-chip').forEach(el => el.classList.add('btn-outline-dark'));
            element.classList.remove('btn-outline-dark');
            element.classList.add('btn-dark', 'text-white');
            document.getElementById('selectedTallaId').value = tallaId;
            document.getElementById('btnConfirmSize').disabled = false;
        }

        function confirmSizeAndAdd() {
            const productId = document.getElementById('selectedProductId').value;
            const colorId = document.getElementById('selectedColorId').value;
            const tallaId = document.getElementById('selectedTallaId').value;
            
            if (!tallaId) return;
            
            const btn = document.getElementById('btnConfirmSize');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Agregando...';
            sendAddToCart(productId, tallaId, colorId);
        }

        function sendAddToCart(productId, tallaId = null, colorId = null) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch("{{ route('cart.store') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": token, "Accept": "application/json" },
                body: JSON.stringify({ product_id: productId, talla_id: tallaId, color_id: colorId, quantity: 1 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('toastMessage').textContent = data.message;
                    cartToast.show();
                    const badges = document.querySelectorAll('.cart-badge, .fc-badge');
                    badges.forEach(b => {
                        b.textContent = data.count;
                        b.style.display = data.count > 0 ? 'flex' : 'none';
                    });
                    sizeModal.hide();
                } else {
                    alert(data.message || "Error al agregar al carrito");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Error de conexión.");
            })
            .finally(() => {
                const btn = document.getElementById('btnConfirmSize');
                if (btn) {
                    btn.innerHTML = 'Agregar al Carrito';
                    btn.disabled = false;
                }
            });
        }
    </script>
    <style>
        .size-chip { transition: all 0.2s ease; margin-bottom: 5px; }
        .color-chip { 
            width: 32px; height: 32px; border-radius: 50%; border: 2px solid #ddd; 
            cursor: pointer; transition: all 0.2s ease; 
        }
        .color-chip:hover { transform: scale(1.1); }
        .color-chip.active { border-color: #000; box-shadow: 0 0 0 2px #fff, 0 0 0 4px #000; }
        .text-accent { color: #c9a84c; }
        .product-img-wrapper {
            position: relative;
            aspect-ratio: 4/5;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-img-wrapper img {
            width: 100%; height: 100%; object-fit: cover;
        }
        .product-placeholder {
            width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
            background: #f0f0f0; color: #ccc; font-size: 3rem;
        }
    </style>
@endpush