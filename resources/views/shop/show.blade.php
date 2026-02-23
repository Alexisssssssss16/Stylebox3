@extends('layouts.shop')

@section('title', $product->name . ' — StyleBox')

@push('styles')
    <style>
        /* ── MOBILE (full-screen product detail) ── */
        @media (max-width: 767.98px) {
            .product-hero-mobile {
                position: relative;
                height: 65vh;
                background: #000;
                overflow: hidden;
            }

            .product-hero-mobile img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                opacity: 0.88;
            }

            .product-hero-mobile .no-img {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: rgba(255, 255, 255, 0.3);
                font-size: 5rem;
            }

            .product-hero-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 50%;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.95), transparent);
                pointer-events: none;
            }

            .product-hero-back {
                position: absolute;
                top: 16px;
                left: 16px;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(4px);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
                text-decoration: none;
                z-index: 5;
            }

            .product-hero-badge {
                position: absolute;
                top: 16px;
                right: 16px;
                z-index: 5;
            }

            .product-info-mobile {
                background: #fff;
                border-radius: 24px 24px 0 0;
                margin-top: -24px;
                position: relative;
                z-index: 3;
                padding: 1.5rem 1.25rem 6rem;
                min-height: 40vh;
            }

            .buy-bar-mobile {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #fff;
                border-top: 1px solid #e9ecef;
                padding: 1rem 1.25rem;
                z-index: 99;
                display: flex;
                gap: 0.75rem;
            }
        }

        /* ── DESKTOP ── */
        @media (min-width: 768px) {

            .product-hero-mobile,
            .product-info-mobile,
            .buy-bar-mobile {
                display: none !important;
            }

            .product-img-desktop {
                border-radius: 16px;
                overflow: hidden;
                aspect-ratio: 1 / 1;
                background: #f0f0f0;
            }

            .product-img-desktop img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .product-img-desktop .no-img {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #bbb;
                font-size: 5rem;
            }

            .badge-category {
                background: #f4f6f9;
                color: #495057;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                padding: 5px 10px;
                border-radius: 20px;
            }

            .stock-indicator {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 0.85rem;
            }

            .stock-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
            }

            .meta-row {
                display: flex;
                gap: 1.5rem;
                padding: 1rem 0;
                border-top: 1px solid #f0f0f0;
                border-bottom: 1px solid #f0f0f0;
                margin: 1rem 0;
            }

            .meta-item {
                font-size: 0.85rem;
                color: #6c757d;
            }

            .meta-item strong {
                display: block;
                color: #1a1a1a;
                font-size: 0.95rem;
            }

            .btn-buy-desktop {
                background: #1a1a1a;
                color: #fff;
                border: none;
                border-radius: 12px;
                padding: 14px 36px;
                font-weight: 600;
                font-size: 1rem;
                transition: background 0.2s, transform 0.15s;
            }

            .btn-buy-desktop:hover {
                background: #000;
                transform: translateY(-1px);
            }

            .btn-buy-desktop:disabled {
                background: #adb5bd;
            }
        }

        /* Quantity selector (shared) */
        .qty-control {
            display: inline-flex;
            align-items: center;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            overflow: hidden;
        }

        .qty-control button {
            width: 38px;
            height: 38px;
            border: none;
            background: #f8f9fa;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.15s;
            line-height: 1;
        }

        .qty-control button:hover {
            background: #e9ecef;
        }

        .qty-control input {
            width: 50px;
            border: none;
            text-align: center;
            font-weight: 600;
            font-size: 1rem;
            background: #fff;
            outline: none;
        }

        /* Hide arrows in number input */
        .qty-control input::-webkit-outer-spin-button,
        .qty-control input::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }

        .qty-control input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endpush

@section('content')

    {{-- ══════════════════════════════════════════ --}}
    {{-- MOBILE VIEW --}}
    {{-- ══════════════════════════════════════════ --}}
    <div class="d-md-none">
        {{-- Hero Image --}}
        <div class="product-hero-mobile">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid">
            @else
                <div class="no-img"><i class="fas fa-tshirt"></i></div>
            @endif

            <div class="product-hero-overlay"></div>

            {{-- Back button --}}
            <a href="{{ route('shop.index') }}" class="product-hero-back">
                <i class="fas fa-arrow-left"></i>
            </a>

            {{-- Stock badge --}}
            <div class="product-hero-badge">
                @if($product->stock <= 0)
                    <span class="badge bg-danger">Agotado</span>
                @elseif($product->stock < 5)
                    <span class="badge bg-warning text-dark">¡Solo {{ $product->stock }} left!</span>
                @endif
            </div>
        </div>

        {{-- Product Info Card --}}
        <div class="product-info-mobile">
            <span class="badge mb-2"
                style="background:#f4f6f9; color:#495057; font-size:0.7rem; font-weight:700; letter-spacing:0.5px; text-transform:uppercase;">
                {{ $product->category ?? 'General' }}
            </span>
            <h1 class="fw-bold mb-1" style="font-size: 1.6rem;">{{ $product->name }}</h1>
            <div class="h3 fw-bold mb-3" style="color:#d4a017;">S/ {{ number_format($product->price, 2) }}</div>

            @if($product->description)
                <p class="text-muted mb-4" style="font-size: 0.9rem; line-height: 1.6;">{{ $product->description }}</p>
            @endif

            {{-- Meta --}}
            <div class="d-flex gap-4 py-3 mb-4" style="border-top:1px solid #f0f0f0; border-bottom:1px solid #f0f0f0;">
                @if($product->measurementUnit)
                    <div class="text-muted small">
                        <span class="d-block fw-bold text-dark">{{ $product->measurementUnit->name }}</span>
                        Unidad
                    </div>
                @endif
                @if($product->code)
                    <div class="text-muted small">
                        <span class="d-block fw-bold text-dark">{{ $product->code }}</span>
                        Código
                    </div>
                @endif
                <div class="text-muted small">
                    <span class="d-block fw-bold {{ $product->stock > 5 ? 'text-success' : 'text-warning' }}">
                        {{ $product->stock }}
                    </span>
                    En stock
                </div>
            </div>

            {{-- Quantity selector --}}
            @if($product->stock > 0)
                <label class="text-muted small fw-bold mb-2 d-block">CANTIDAD</label>
                <div class="qty-control mb-4">
                    <button type="button" onclick="changeQty(-1)">−</button>
                    <input type="number" id="qty-mobile" value="1" min="1" max="{{ $product->stock }}" readonly>
                    <button type="button" onclick="changeQty(1, {{ $product->stock }})">+</button>
                </div>
            @endif
        </div>

        {{-- Fixed Buy Bar --}}
        <div class="buy-bar-mobile">
            @auth
                @if($product->stock > 0)
                    <button class="btn btn-outline-dark flex-shrink-0" style="border-radius:10px;" onclick="handleWishlist()">
                        <i class="fas fa-heart"></i>
                    </button>
                    <button class="btn w-100 fw-bold" style="background:#1a1a1a; color:#fff; border-radius:10px;"
                        onclick="handleBuy('mobile')">
                        <i class="fas fa-shopping-bag me-2"></i>Comprar ahora
                    </button>
                @else
                    <button class="btn w-100 fw-bold" disabled style="background:#adb5bd; color:#fff; border-radius:10px;">
                        <i class="fas fa-ban me-2"></i>Sin stock
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn w-100 fw-bold"
                    style="background:#1a1a1a; color:#fff; border-radius:10px;">
                    <i class="fas fa-sign-in-alt me-2"></i>Inicia sesión para comprar
                </a>
            @endauth
        </div>
    </div>


    {{-- ══════════════════════════════════════════ --}}
    {{-- DESKTOP VIEW --}}
    {{-- ══════════════════════════════════════════ --}}
    <div class="d-none d-md-block">
        <div class="container py-5" style="max-width: 1100px;">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"
                            class="text-decoration-none text-muted">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shop.index') }}"
                            class="text-decoration-none text-muted">Tienda</a></li>
                    @if($product->category)
                        <li class="breadcrumb-item text-muted">{{ $product->category }}</li>
                    @endif
                    <li class="breadcrumb-item active fw-semibold">{{ $product->name }}</li>
                </ol>
            </nav>

            <div class="row g-5 align-items-start">

                {{-- ── Product Image ── --}}
                <div class="col-md-5">
                    <div class="product-img-desktop">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid">
                        @else
                            <div class="no-img"><i class="fas fa-tshirt"></i></div>
                        @endif
                    </div>
                </div>

                {{-- ── Product Detail ── --}}
                <div class="col-md-7">
                    {{-- Category + Stock --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge-category">{{ $product->category ?? 'General' }}</span>
                        @if($product->stock <= 0)
                            <span class="badge bg-danger">Agotado</span>
                        @elseif($product->stock < 5)
                            <span class="stock-indicator text-warning">
                                <span class="stock-dot bg-warning"></span> Solo {{ $product->stock }} disponibles
                            </span>
                        @else
                            <span class="stock-indicator text-success">
                                <span class="stock-dot bg-success"></span> En stock
                            </span>
                        @endif
                    </div>

                    <h1 class="fw-bold mb-2" style="font-size: 2rem; line-height: 1.2;">{{ $product->name }}</h1>

                    {{-- Price --}}
                    <div class="mb-3" style="color:#d4a017; font-size: 2.2rem; font-weight: 800; letter-spacing: -0.5px;">
                        S/ {{ number_format($product->price, 2) }}
                    </div>

                    @if($product->description)
                        <p class="text-muted" style="font-size: 0.95rem; line-height: 1.7;">{{ $product->description }}</p>
                    @endif

                    {{-- Meta row --}}
                    <div class="meta-row">
                        @if($product->code)
                            <div class="meta-item">
                                <strong>{{ $product->code }}</strong>
                                Código
                            </div>
                        @endif
                        @if($product->measurementUnit)
                            <div class="meta-item">
                                <strong>{{ $product->measurementUnit->name }}</strong>
                                Unidad
                            </div>
                        @endif
                        <div class="meta-item">
                            <strong class="{{ $product->stock > 5 ? 'text-success' : 'text-warning' }}">
                                {{ $product->stock }} uds.
                            </strong>
                            Disponibles
                        </div>
                    </div>

                    {{-- Quantity --}}
                    @if($product->stock > 0)
                        <div class="mb-4">
                            <label class="text-muted small fw-bold mb-2 d-block">CANTIDAD</label>
                            <div class="qty-control">
                                <button type="button" onclick="changeQty(-1)">−</button>
                                <input type="number" id="qty-desktop" value="1" min="1" max="{{ $product->stock }}" readonly>
                                <button type="button" onclick="changeQty(1, {{ $product->stock }})">+</button>
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    @auth
                        @if($product->stock > 0)
                            <div class="d-flex gap-3 mt-2">
                                <button class="btn-buy-desktop flex-grow-1" onclick="handleBuy('desktop')">
                                    <i class="fas fa-shopping-bag me-2"></i>Comprar ahora
                                </button>
                                <button class="btn btn-outline-secondary" style="border-radius:12px; padding: 14px 16px;"
                                    onclick="handleWishlist()">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        @else
                            <button class="btn-buy-desktop w-100" disabled style="background:#adb5bd;">
                                <i class="fas fa-ban me-2"></i>Sin stock disponible
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                            class="btn-buy-desktop d-inline-block text-center text-decoration-none w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Inicia sesión para comprar
                        </a>
                    @endauth

                    {{-- Trust badges --}}
                    <div class="d-flex gap-4 mt-4 pt-3" style="border-top:1px solid #f0f0f0;">
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="fas fa-shield-alt text-success"></i> Compra segura
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="fas fa-undo text-primary"></i> Devoluciones fáciles
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="fas fa-tag text-warning"></i> Precio justo
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Back link ── --}}
            <div class="mt-5 pt-3" style="border-top:1px solid #f0f0f0;">
                <a href="{{ route('shop.index') }}" class="text-decoration-none text-muted small">
                    <i class="fas fa-arrow-left me-1"></i> Volver a la tienda
                </a>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const maxStock = {{ $product->stock }};

        function changeQty(delta, max) {
            ['qty-mobile', 'qty-desktop'].forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                let val = parseInt(el.value) + delta;
                val = Math.max(1, Math.min(val, max ?? maxStock));
                el.value = val;
            });
        }

        function getQty() {
            const el = document.getElementById('qty-desktop') || document.getElementById('qty-mobile');
            return el ? parseInt(el.value) : 1;
        }

        async function handleBuy(source) {
            const qty = getQty();
            const btn = source === 'desktop'
                ? document.querySelector('.btn-buy-desktop')
                : document.querySelector('.buy-bar-mobile .btn');

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Agregando…';
            }

            try {
                const res = await fetch("{{ route('cart.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: {{ $product->id }},
                        quantity: qty,
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    window.location.href = "{{ route('checkout.show') }}";
                } else {
                    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-shopping-bag me-2"></i>Comprar ahora'; }
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#1a1a1a' });
                }
            } catch (err) {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-shopping-bag me-2"></i>Comprar ahora'; }
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo agregar el producto.', confirmButtonColor: '#1a1a1a' });
            }
        }

        function handleWishlist() {
            Swal.fire({
                icon: 'info',
                title: 'Lista de deseos',
                text: 'Esta función estará disponible próximamente.',
                confirmButtonColor: '#1a1a1a',
                timer: 2500,
                showConfirmButton: false,
            });
        }
    </script>
@endpush