@extends('layouts.shop')

@section('title', 'Explorar - StyleBox')

@push('styles')
    <style>
        /* --- MOBILE TIKTOK STYLE (< 768px) --- */
        @media (max-width: 767.98px) {
            body {
                overflow: hidden; /* Prevent body scroll, handle in container */
            }
            
            .mobile-feed-container {
                height: 100vh;
                height: calc(100vh - var(--bottom-nav-height));
                width: 100%;
                overflow-y: scroll;
                scroll-snap-type: y mandatory;
                scroll-behavior: smooth;
            }

            .feed-item {
                height: 100%;
                width: 100%;
                scroll-snap-align: start;
                position: relative;
                background-color: #000;
                display: flex;
                justify-content: center;
                align-items: center;
                overflow: hidden;
            }

            .feed-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                opacity: 0.85;
            }

            .feed-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 60%;
                background: linear-gradient(to top, rgba(0,0,0,0.95), transparent);
                pointer-events: none;
            }

            .feed-content {
                position: absolute;
                bottom: 20px;
                left: 20px;
                right: 70px; /* Space for actions */
                color: #fff;
                z-index: 2;
                pointer-events: auto;
            }

            .feed-actions {
                position: absolute;
                bottom: 40px;
                right: 15px;
                display: flex;
                flex-direction: column;
                gap: 20px;
                z-index: 2;
                pointer-events: auto;
            }

            .action-btn {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                background: rgba(255,255,255,0.15);
                backdrop-filter: blur(5px);
                border: 1px solid rgba(255,255,255,0.2);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                transition: 0.2s;
            }
            
            .action-btn:active { transform: scale(0.9); }
            .badge-stock { font-size: 0.75rem; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.3); }
        }

        /* --- DESKTOP GRID STYLE (>= 768px) --- */
        @media (min-width: 768px) {
            .mobile-feed-container { display: none; }
        }

        /* --- OECHSLE STYLE FILTERS --- */
        .filter-bar {
            background: #fff;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .filter-btn {
            background: #f4f4f4;
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .filter-btn:hover {
            background: #e9e9e9;
        }
        .filter-btn.active {
            border-color: #333;
            background: #fff;
        }
        .filter-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #333;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .filter-pill .remove-pill {
            margin-left: 8px;
            cursor: pointer;
            color: #999;
            font-size: 1rem;
        }
        .filter-pill .remove-pill:hover {
            color: #ff0000;
        }
        .clear-all {
            color: #333;
            font-weight: 600;
            text-decoration: underline;
            font-size: 0.85rem;
            background: none;
            border: none;
            cursor: pointer;
        }
        
        /* Dropdown content custom */
        .filter-dropdown-menu {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid #eee;
            padding: 1rem;
            min-width: 250px;
        }
        .color-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: inline-block;
            border: 1px solid #ddd;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px; width: 18px;
            left: 3px; bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider { background-color: #333; }
        input:checked + .slider:before { transform: translateX(20px); }
    </style>
@endpush

@section('content')

    <!-- === MOBILE VIEW (TikTok Style) === -->
    <div class="d-md-none mobile-feed-container">
        @forelse($products as $product)
            <div class="feed-item">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="feed-img" alt="{{ $product->name }}">
                @else
                    <div class="feed-img d-flex align-items-center justify-content-center bg-dark text-white-50">
                        <i class="fas fa-tshirt fa-4x"></i>
                    </div>
                @endif

                <div class="feed-overlay"></div>

                <div class="feed-content">
                    <span class="badge badge-stock mb-2">{{ $product->category ?? 'General' }}</span>
                    <h2 class="fw-bold mb-1" style="font-size: 1.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">{{ $product->name }}</h2>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="text-warning fw-bold fs-4">S/ {{ number_format($product->price, 2) }}</span>
                        @if($product->stock < 5 && $product->stock > 0)
                            <span class="badge bg-warning text-dark">¡Quedan {{ $product->stock }}!</span>
                        @elseif($product->stock <= 0)
                            <span class="badge bg-danger">Agotado</span>
                        @endif
                    </div>
                    <p class="small opacity-75 mb-0 text-truncate">{{ $product->description }}</p>
                </div>

                <div class="feed-actions">
                    <div class="text-center">
                        <button class="action-btn" onclick="addToCart({{ $product->id }})" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            <i class="fas fa-plus"></i>
                        </button>
                        <span class="small text-white mt-1 d-block shadow-sm">Agregar</span>
                    </div>
                    
                    <div class="text-center">
                        <button class="action-btn bg-primary border-0" onclick="buyNow({{ $product->id }})" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                        <span class="small text-white mt-1 d-block shadow-sm">Comprar</span>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('shop.show', $product) }}" class="action-btn text-decoration-none">
                            <i class="fas fa-eye"></i>
                        </a>
                        <span class="small text-white mt-1 d-block shadow-sm">Ver</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="feed-item bg-dark">
                <div class="text-center text-white">
                    <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
                    <h3>No hay productos disponibles</h3>
                </div>
            </div>
        @endforelse
    </div>

    <!-- === DESKTOP VIEW (Classic with Oechsle Filters) === -->
    <div class="d-none d-md-block">
        
        <div class="filter-bar">
            <div class="container d-flex flex-wrap align-items-center gap-3">
                
                {{-- Todos los filtros button --}}
                <div class="dropdown">
                    <button class="filter-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-sliders-h"></i> Todos los filtros
                    </button>
                    <div class="dropdown-menu filter-dropdown-menu p-4" style="width: 300px;">
                        <form action="{{ route('shop.index') }}" method="GET">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                            
                            <label class="fw-bold mb-2">Rango de Precio</label>
                            <div class="d-flex gap-2 mb-3">
                                <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}">
                                <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}">
                            </div>
                            
                            <button type="submit" class="btn btn-dark w-100 rounded-pill">Aplicar</button>
                        </form>
                    </div>
                </div>

                {{-- Tallas Dropdown --}}
                <div class="dropdown">
                    <button class="filter-btn {{ request('talla') ? 'active' : '' }}" type="button" data-bs-toggle="dropdown">
                        Tallas <i class="fas fa-chevron-down small ms-1"></i>
                    </button>
                    <div class="dropdown-menu filter-dropdown-menu">
                        <div class="row g-2">
                            @foreach($tallas as $t)
                                <div class="col-4">
                                    <a class="btn btn-sm w-100 {{ request('talla') == $t->id ? 'btn-dark' : 'btn-outline-secondary' }}" 
                                       href="{{ request()->fullUrlWithQuery(['talla' => $t->id]) }}">
                                        {{ $t->nombre }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Categorías/Marcas Dropdown --}}
                <div class="dropdown">
                    <button class="filter-btn {{ request('category') ? 'active' : '' }}" type="button" data-bs-toggle="dropdown">
                        Categorías <i class="fas fa-chevron-down small ms-1"></i>
                    </button>
                    <div class="dropdown-menu filter-dropdown-menu">
                        @foreach($categories as $cat)
                            <a class="dropdown-item py-2 {{ request('category') == $cat ? 'fw-bold bg-light' : '' }}" 
                               href="{{ request()->fullUrlWithQuery(['category' => $cat]) }}">
                                {{ $cat }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Color Dropdown --}}
                <div class="dropdown">
                    <button class="filter-btn {{ request('color') ? 'active' : '' }}" type="button" data-bs-toggle="dropdown">
                        Color <i class="fas fa-chevron-down small ms-1"></i>
                    </button>
                    <div class="dropdown-menu filter-dropdown-menu">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($colors as $color)
                                <a href="{{ request()->fullUrlWithQuery(['color' => $color->id]) }}" 
                                   class="p-1 border rounded d-flex align-items-center gap-2 text-decoration-none text-dark hover-light"
                                   title="{{ $color->name }}"
                                   style="min-width: 100px;">
                                    <span class="color-dot" style="background-color: {{ $color->hex_code }}"></span>
                                    <small>{{ $color->name }}</small>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Envío Hoy Toggle (Simulado) --}}
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <span class="fw-bold small">Envío hoy</span>
                    <label class="toggle-switch">
                        <input type="checkbox" onchange="Swal.fire({title: 'Filtro de envío', text: 'Esta función llegará pronto', icon: 'info', timer: 1500, showConfirmButton: false})">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            {{-- Active Filter Pills --}}
            <div class="container mt-3">
                @php
                    $hasFilters = request()->hasAny(['talla', 'color', 'category', 'min_price', 'max_price', 'search']);
                @endphp

                @if($hasFilters)
                    <div class="d-flex align-items-center flex-wrap">
                        @if(request('search'))
                            <div class="filter-pill">Búsqueda: "{{ request('search') }}" <span class="remove-pill" onclick="window.location.href='{{ request()->fullUrlWithQuery(['search' => null]) }}'">&times;</span></div>
                        @endif
                        @if(request('category'))
                            <div class="filter-pill">{{ request('category') }} <span class="remove-pill" onclick="window.location.href='{{ request()->fullUrlWithQuery(['category' => null]) }}'">&times;</span></div>
                        @endif
                        @if(request('talla'))
                            @php $tName = $tallas->where('id', request('talla'))->first()?->nombre ?? 'Talla'; @endphp
                            <div class="filter-pill">Talla {{ $tName }} <span class="remove-pill" onclick="window.location.href='{{ request()->fullUrlWithQuery(['talla' => null]) }}'">&times;</span></div>
                        @endif
                        @if(request('color'))
                            @php $cName = $colors->where('id', request('color'))->first()?->name ?? 'Color'; @endphp
                            <div class="filter-pill">Color {{ $cName }} <span class="remove-pill" onclick="window.location.href='{{ request()->fullUrlWithQuery(['color' => null]) }}'">&times;</span></div>
                        @endif
                        @if(request('min_price') || request('max_price'))
                            <div class="filter-pill">Precio: S/{{ request('min_price', 0) }} - S/{{ request('max_price', '+') }} <span class="remove-pill" onclick="window.location.href='{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}'">&times;</span></div>
                        @endif

                        <button class="clear-all ms-2 mb-2" onclick="window.location.href='{{ route('shop.index') }}'">Limpiar todo</button>
                    </div>
                @endif
            </div>
        </div>

        <div class="container mb-5">
            <!-- Header Info -->
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Resultados ({{ $products->total() }})</h2>
                </div>
                
                <form action="{{ route('shop.index') }}" method="GET" class="d-flex gap-2">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" name="search" class="form-control rounded-start-pill border-end-0" placeholder="¿Qué estás buscando hoy?" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary rounded-end-pill border-start-0 px-3" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>

        <!-- Product Grid -->
        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm product-card hover-lift">
                        <div class="position-relative">
                            <a href="{{ route('shop.show', $product) }}">
                                @if($product->image)
                                    <div class="ratio ratio-1x1 bg-light">
                                        <img src="{{ asset('storage/' . $product->image) }}" class="object-fit-cover rounded-top" alt="{{ $product->name }}">
                                    </div>
                                @else
                                    <div class="ratio ratio-1x1 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center text-muted">
                                        <i class="fas fa-tshirt fa-2x"></i>
                                    </div>
                                @endif
                            </a>
                            @if($product->stock <= 0)
                                <span class="position-absolute top-0 end-0 badge bg-danger m-2">Agotado</span>
                            @elseif($product->stock < 5)
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">¡Pocas unidades!</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted text-uppercase fw-semibold">{{ $product->category ?? 'General' }}</small>
                                <small class="text-muted"><i class="fas fa-star text-warning"></i> 4.5</small>
                            </div>
                            <h5 class="card-title fw-bold text-truncate"><a href="{{ route('shop.show', $product) }}" class="text-dark text-decoration-none">{{ $product->name }}</a></h5>
                            <p class="card-text small text-muted text-truncate">{{ $product->description }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="h5 mb-0 fw-bold text-primary">S/ {{ number_format($product->price, 2) }}</span>
                                <button class="btn btn-dark btn-sm rounded-pill px-3" onclick="addToCart({{ $product->id }})" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-plus me-1"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-5 text-center">
                    <div class="py-5 bg-light rounded-3">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>No encontramos productos</h4>
                        <p class="text-muted">Intenta con otra categoría o término de búsqueda.</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-outline-dark">Ver todo</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-5 d-flex justify-content-center">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        async function addToCart(productId) {
            try {
                const res = await fetch("{{ route('cart.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: productId, quantity: 1 })
                });

                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Agregado!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                    
                    // Update global badge etc. if needed
                } else {
                    // If 401, redirect to login or show confirm
                    if (res.status === 401) {
                         const loginConfirm = await Swal.fire({
                            title: 'Inicia sesión',
                            text: "Para agregar productos al carrito debes ingresar a tu cuenta.",
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#000',
                            confirmButtonText: 'Ir al Login'
                        });
                        if (loginConfirm.isConfirmed) window.location.href = "{{ route('login') }}";
                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo agregar el producto.',
                        confirmButtonColor: '#000'
                    });
                }
            } catch (err) {
                console.error(err);
            }
        }

        function buyNow(productId) {
            // Option 1: Add to cart and redirect to cart
            // Option 2: Just go to product page
            window.location.href = "{{ url('/shop') }}/" + productId;
        }
    </script>
@endpush