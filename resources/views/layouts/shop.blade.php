<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'StyleBox Shop')</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #000000;
            --accent-color: #D4AF37;
            /* Gold */
            --bg-color: #ffffff;
            --text-color: #212529;
            --bottom-nav-height: 60px;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            padding-bottom: var(--bottom-nav-height);
            /* Space for bottom nav */
        }

        /* Top Bar */
        .shop-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #f0f0f0;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .shop-brand {
            font-weight: 700;
            font-size: 1.25rem;
            text-decoration: none;
            color: var(--primary-color);
            letter-spacing: -0.5px;
        }

        .shop-brand span {
            color: var(--accent-color);
        }

        /* Bottom Navigation (App Style) */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: var(--bottom-nav-height);
            background: #ffffff;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.03);
        }

        .nav-item-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #999;
            font-size: 0.75rem;
            transition: color 0.2s;
            flex: 1;
            height: 100%;
        }

        .nav-item-link i {
            font-size: 1.2rem;
            margin-bottom: 4px;
        }

        .nav-item-link.active {
            color: var(--primary-color);
            font-weight: 500;
        }

        /* Product Card */
        .product-card {
            border: none;
            transition: transform 0.2s;
        }

        .product-card:active {
            transform: scale(0.98);
        }

        .product-img-wrapper {
            position: relative;
            padding-bottom: 100%;
            /* 1:1 Aspect Ratio */
            border-radius: 12px;
            overflow: hidden;
            background: #f8f9fa;
            margin-bottom: 0.75rem;
        }

        /* Scoped strictly to product card grid images — does NOT affect checkout thumbnails */
        .product-card .product-img-wrapper img,
        .product-img-wrapper > img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 1;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
    </style>
    @stack('styles')
</head>

<body>

    <!-- Header -->
    <header class="shop-header">
        <a href="{{ route('shop.index') }}" class="shop-brand">Style<span>Box</span></a>
        <div>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="fas fa-user-circle me-1"></i> Mi Cuenta
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-dark rounded-pill px-3">Ingresar</a>
            @endauth
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="{{ route('shop.index') }}"
            class="nav-item-link {{ request()->routeIs('shop.index') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <a href="#" class="nav-item-link">
            <i class="fas fa-search"></i>
            <span>Buscar</span>
        </a>
        <a href="{{ auth()->check() ? route('cart.index') : route('login') }}"
            class="nav-item-link {{ request()->routeIs('cart.index', 'checkout.*') ? 'active' : '' }}">
            <i class="fas fa-shopping-bag"></i>
            <span>Bolsa</span>
        </a>
        <a href="{{ route('dashboard') }}" class="nav-item-link">
            <i class="fas fa-user"></i>
            <span>Perfil</span>
        </a>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple Interaction Feedback
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function () {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>

</html>