<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'StyleBox App')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #000000;
            --accent-color: #ffffff;
            --bottom-nav-height: 60px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 0;
            overflow: hidden;
            /* Prevent body scroll, handle in containers */
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: var(--bottom-nav-height);
            background-color: #000;
            border-top: 1px solid #222;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1000;
            padding-bottom: env(safe-area-inset-bottom);
        }

        .nav-item-link {
            color: #888;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.75rem;
            transition: color 0.3s;
        }

        .nav-item-link.active {
            color: #fff;
        }

        .nav-item-link i {
            font-size: 1.2rem;
            margin-bottom: 4px;
        }

        /* Main Content Container */
        .app-content {
            height: calc(100vh - var(--bottom-nav-height));
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
            padding-bottom: var(--bottom-nav-height);
            /* Safe space */
        }

        /* Hide Scrollbar */
        .app-content::-webkit-scrollbar {
            display: none;
        }

        .app-content {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @stack('styles')
    </style>
</head>

<body>

    <main class="app-content">
        @yield('content')
    </main>

    <nav class="bottom-nav">
        <a href="{{ route('catalogo.index') }}"
            class="nav-item-link {{ request()->routeIs('catalogo.index') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <a href="{{ route('shop.feed') }}" class="nav-item-link {{ request()->routeIs('shop.feed') ? 'active' : '' }}">
            <i class="fas fa-compass"></i>
            <span>Explorar</span>
        </a>
        <a href="#" class="nav-item-link" onclick="alert('Carrito próximamente')">
            <i class="fas fa-shopping-bag"></i>
            <span>Carrito</span>
        </a>
        @auth
            <a href="{{ route('dashboard') }}" class="nav-item-link">
                <i class="fas fa-user"></i>
                <span>Perfil</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="nav-item-link">
                <i class="fas fa-user-circle"></i>
                <span>Entrar</span>
            </a>
        @endauth
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Common App JS
    </script>
    @stack('scripts')
</body>

</html>