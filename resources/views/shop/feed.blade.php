@extends('layouts.app-mobile')

@section('title', 'Explorar - StyleBox')

@push('styles')
    <style>
        .product-section {
            height: 100vh;
            /* Full viewport height */
            /* Adjust for bottom nav */
            height: calc(100vh - var(--bottom-nav-height));
            width: 100%;
            scroll-snap-align: start;
            position: relative;
            background-color: #111;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
            opacity: 0.8;
        }

        .overlay-gradient {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
            z-index: 2;
        }

        .product-info {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 80px;
            /* Space for right-side buttons */
            z-index: 3;
            color: white;
            text-align: left;
        }

        .product-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .product-price {
            font-size: 1.2rem;
            color: #ffc107;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .product-desc {
            font-size: 0.9rem;
            color: #ddd;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 15px;
        }

        /* Right Action Bar */
        .action-bar {
            position: absolute;
            right: 15px;
            bottom: 50px;
            z-index: 3;
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }

        .action-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            transition: transform 0.2s, background-color 0.2s;
            cursor: pointer;
        }

        .action-btn:active {
            transform: scale(0.9);
            background-color: rgba(255, 255, 255, 0.3);
        }

        .action-label {
            font-size: 0.7rem;
            margin-top: 5px;
            text-align: center;
            text-shadow: 1px 1px 2px black;
        }

        .buy-btn {
            background-color: #ff3b30;
            border: none;
            box-shadow: 0 4px 10px rgba(255, 59, 48, 0.4);
        }

        /* Floating "No more products" */
        .end-message {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            scroll-snap-align: start;
            color: #666;
        }
    </style>
@endpush

@section('content')
    @forelse($products as $product)
        <section class="product-section">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="product-image" alt="{{ $product->name }}">
            @else
                <!-- Placeholder if no image -->
                <div class="product-image bg-secondary d-flex justify-content-center align-items-center">
                    <i class="fas fa-tshirt fa-5x text-white-50"></i>
                </div>
            @endif

            <div class="overlay-gradient"></div>

            <div class="product-info">
                <h2 class="product-title">{{ $product->name }}</h2>
                <div class="product-price">S/ {{ number_format($product->price, 2) }}</div>
                <p class="product-desc">{{ $product->description }}</p>
                <div class="badges">
                    @if($product->stock > 0)
                        <span class="badge bg-success bg-opacity-75">Disponible: {{ $product->stock }}</span>
                    @else
                        <span class="badge bg-danger">Agotado</span>
                    @endif
                </div>
            </div>

            <div class="action-bar">
                <div class="text-center">
                    <button class="action-btn" onclick="addToCart({{ $product->id }})">
                        <i class="fas fa-cart-plus"></i>
                    </button>
                    <div class="action-label">Agregar</div>
                </div>

                <div class="text-center">
                    <button class="action-btn buy-btn" onclick="buyNow({{ $product->id }})">
                        <i class="fas fa-bolt"></i>
                    </button>
                    <div class="action-label">Comprar</div>
                </div>

                <div class="text-center">
                    <a href="{{ route('shop.show', $product->id) }}" class="action-btn" style="text-decoration: none;">
                        <i class="fas fa-info"></i>
                    </a>
                    <div class="action-label">Detalles</div>
                </div>
            </div>
        </section>
    @empty
        <section class="end-message">
            <h3>No hay productos disponibles por ahora.</h3>
        </section>
    @endforelse

    <section class="end-message">
        <div class="text-center">
            <i class="fas fa-check-circle fa-3x mb-3 text-muted"></i>
            <h3>¡Has visto todo!</h3>
            <p>Vuelve pronto para más novedades.</p>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function addToCart(productId) {
            // Check Auth using a metatag or JS variable would be better, but simplest is to try query
            // For now, we simulate success or redirect if not auth logic handles it.
            // Actually, let's look at how the shop index does it. It just has a button.
            // We need a route for adding to cart. Usually it's a POST request.
            // Since we don't have a specific 'cart' controller visible in file list, 
            // I will assume for now we might need to create one or use a placeholder alerts. 
            // NOTE: The previous turn didn't show a CartController, implies logic might be missing or client-side.
            // I will implement a placeholder that prompts user.

            @auth
                alert('¡Producto ' + productId + ' agregado al carrito! (Simulación)');
                // Implement actual Fetch/AJAX here to a Cart route
            @else
                     if (confirm('Debes iniciar sesión para comprar. ¿Ir al login?')) {
                    window.location.href = "{{ route('login') }}";
                }
            @endauth
        }

        function buyNow(productId) {
            addToCart(productId);
            // Redirect to checkout
        }
    </script>
@endpush