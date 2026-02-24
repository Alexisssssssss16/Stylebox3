@extends('layouts.public')

@section('title', 'StyleBox - Moda & Estilo')

@push('styles')
    <style>
        .hero-carousel {
            height: 500px;
            background-color: #f8f9fa;
            overflow: hidden;
        }

        .carousel-item {
            height: 500px;
        }

        .carousel-caption {
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary-color);
            padding: 2rem;
            border-radius: 8px;
            bottom: 20%;
            left: 10%;
            right: auto;
            max-width: 500px;
            text-align: left;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .product-card {
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .product-img-wrapper {
            height: 300px;
            overflow: hidden;
            background-color: #f1f1f1;
            position: relative;
        }

        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.05);
        }

        .btn-shop {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-shop:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 3rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background-color: var(--accent-color);
        }
    </style>
@endpush

@section('content')

    <!-- Hero Carousel -->
    @if($featuredProducts->count() > 0)
        <div id="heroCarousel" class="carousel slide hero-carousel mb-5" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @foreach($featuredProducts as $key => $product)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $key }}"
                        class="{{ $key == 0 ? 'active' : '' }}" aria-current="true"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach($featuredProducts as $key => $product)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="d-block w-100 h-100"
                                style="object-fit: cover; filter: brightness(0.9);" alt="{{ $product->name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 bg-secondary text-white">
                                <i class="fas fa-tshirt fa-5x opacity-50"></i>
                            </div>
                        @endif
                        <div class="carousel-caption d-none d-md-block">
                            <span
                                class="text-uppercase fw-bold text-muted small tracking-wide">{{ $product->category ?? 'Nueva Colección' }}</span>
                            <h1 class="display-4 fw-bold mt-2 mb-3">{{ $product->name }}</h1>
                            <p class="fs-4 fw-bold text-primary mb-4">S/ {{ number_format($product->price, 2) }}</p>
                            @auth
                                <a href="{{ route('shop.show', $product) }}" class="btn btn-shop">Ver Detalle</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-shop">Comprar Ahora</a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
    @else
        <!-- Fallback Hero if no products -->
        <div class="container py-5 text-center">
            <div class="p-5 bg-light rounded-3">
                <h1 class="display-4 fw-bold">Bienvenido a StyleBox</h1>
                <p class="lead">La moda que define tu estilo.</p>
            </div>
        </div>
    @endif

    <!-- Product Grid -->
    <div class="container mb-5">
        <div class="text-center">
            <h2 class="section-title fw-bold">Catálogo Disponible</h2>
            <p class="text-muted mb-5">Explora nuestras últimas tendencias y estilos únicos.</p>
        </div>

        @if($products->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3 opacity-50"></i>
                <p class="text-muted">No hay productos disponibles en este momento.</p>
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                @foreach($products as $product)
                    <div class="col">
                        <div class="card product-card h-100">
                            <div class="product-img-wrapper">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="product-img" alt="{{ $product->name }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                                        <i class="fas fa-image fa-3x"></i>
                                    </div>
                                @endif
                                <div class="position-absolute top-0 end-0 p-2">
                                    @if($product->stock <= 5)
                                        <span class="badge bg-danger">¡Últimas unidades!</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-75">Disponible</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body text-center d-flex flex-column">
                                <small class="text-muted text-uppercase mb-1">{{ $product->category ?? 'General' }}</small>
                                <h5 class="card-title fw-bold mb-2 text-truncate" title="{{ $product->name }}">{{ $product->name }}
                                </h5>
                                <p class="card-text fw-bold text-primary fs-5 mb-3">S/ {{ number_format($product->price, 2) }}</p>

                                <div class="mt-auto">
                                    @auth
                                        <a href="{{ route('shop.show', $product) }}" class="btn btn-outline-dark w-100">Ver Producto</a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-dark w-100">
                                            <i class="fas fa-lock me-1"></i> Iniciar Sesión para Comprar
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-5">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Features Section -->
    <div class="bg-light py-5 mt-5">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-md-4">
                    <div class="p-3">
                        <i class="fas fa-shipping-fast fa-2x text-warning mb-3"></i>
                        <h5 class="fw-bold">Envío Rápido</h5>
                        <p class="text-muted small">Recibe tus prendas en tiempo récord.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <i class="fas fa-shield-alt fa-2x text-warning mb-3"></i>
                        <h5 class="fw-bold">Compra Segura</h5>
                        <p class="text-muted small">Protegemos tus datos y transacciones.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <i class="fas fa-undo fa-2x text-warning mb-3"></i>
                        <h5 class="fw-bold">Garantía de Calidad</h5>
                        <p class="text-muted small">Prendas seleccionadas con los mejores materiales.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection