<!DOCTYPE html>
<html lang="es" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'StyleBox — Moda Premium')</title>
    <meta name="description"
        content="StyleBox — Tu tienda de moda premium. Encuentra ropa, calzado y accesorios de las mejores marcas.">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">

    <style>
        /* ============================================
           DESIGN SYSTEM — TOKENS
        ============================================ */
        :root {
            --font-main: 'Outfit', sans-serif;
            --font-display: 'Playfair Display', serif;

            /* Light Mode */
            --bg: #f8f8f6;
            --surface: #ffffff;
            --surface2: #f2f2f0;
            --border: #e8e8e6;
            --text: #1a1a1a;
            --text-2: #6b6b6b;
            --text-3: #9e9e9e;
            --primary: #1a1a1a;
            --primary-hover: #333;
            --accent: #c9a84c;
            --accent-light: #f5edd8;
            --danger: #e53e3e;
            --success: #38a169;
            --warning: #d69e2e;
            --info: #3182ce;
            --card-shadow: 0 2px 16px rgba(0, 0, 0, 0.07);
            --card-shadow-hover: 0 12px 40px rgba(0, 0, 0, 0.15);
            --header-bg: rgba(255, 255, 255, 0.92);
            --bottom-nav-height: 64px;
            --radius: 16px;
            --radius-sm: 10px;
        }

        [data-theme="dark"] {
            --bg: #0f0f0f;
            --surface: #1a1a1a;
            --surface2: #242424;
            --border: #2e2e2e;
            --text: #f0f0f0;
            --text-2: #a0a0a0;
            --text-3: #666;
            --primary: #f0f0f0;
            --primary-hover: #fff;
            --accent: #c9a84c;
            --accent-light: #2a2210;
            --card-shadow: 0 2px 16px rgba(0, 0, 0, 0.4);
            --card-shadow-hover: 0 12px 40px rgba(0, 0, 0, 0.6);
            --header-bg: rgba(15, 15, 15, 0.95);
        }

        /* ============================================
           BASE
        ============================================ */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-main);
            background: var(--bg);
            color: var(--text);
            padding-bottom: var(--bottom-nav-height);
            transition: background 0.3s, color 0.3s;
            -webkit-font-smoothing: antialiased;
        }

        /* ============================================
           HEADER
        ============================================ */
        .shop-header {
            position: sticky;
            top: 0;
            z-index: 1050;
            background: var(--header-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 0 1.5rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            transition: background 0.3s;
        }

        .shop-brand {
            font-family: var(--font-display);
            font-size: 1.4rem;
            font-weight: 700;
            text-decoration: none;
            color: var(--text);
            letter-spacing: -0.5px;
            flex-shrink: 0;
        }

        .shop-brand span {
            color: var(--accent);
        }

        /* Search Bar */
        .header-search {
            flex: 1;
            max-width: 480px;
            position: relative;
        }

        .header-search input {
            width: 100%;
            padding: 0.6rem 1rem 0.6rem 2.8rem;
            border-radius: 50px;
            border: 1.5px solid var(--border);
            background: var(--surface2);
            color: var(--text);
            font-size: 0.9rem;
            font-family: var(--font-main);
            outline: none;
            transition: all 0.2s;
        }

        .header-search input:focus {
            border-color: var(--accent);
            background: var(--surface);
            box-shadow: 0 0 0 3px var(--accent-light);
        }

        .header-search input::placeholder {
            color: var(--text-3);
        }

        .header-search .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-3);
            font-size: 0.85rem;
        }

        /* Header Actions */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .header-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1.5px solid var(--border);
            background: var(--surface);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            font-size: 0.95rem;
        }

        .header-btn:hover {
            background: var(--surface2);
            color: var(--text);
            border-color: var(--accent);
        }

        .cart-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 18px;
            height: 18px;
            background: var(--danger);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--bg);
        }

        /* ============================================
           FLOATING CART BUTTON
        ============================================ */
        .floating-cart {
            position: fixed;
            bottom: calc(var(--bottom-nav-height) + 20px);
            right: 20px;
            width: 56px;
            height: 56px;
            background: var(--primary);
            color: var(--bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            text-decoration: none;
            z-index: 999;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .floating-cart:hover {
            transform: scale(1.1) translateY(-2px);
            color: var(--bg);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
        }

        .floating-cart .fc-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 20px;
            height: 20px;
            background: var(--accent);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ============================================
           BOTTOM NAV
        ============================================ */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: var(--bottom-nav-height);
            background: var(--surface);
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.06);
        }

        .nav-item-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--text-3);
            font-size: 0.72rem;
            font-weight: 500;
            transition: color 0.2s;
            flex: 1;
            height: 100%;
            gap: 4px;
        }

        .nav-item-link i {
            font-size: 1.15rem;
        }

        .nav-item-link.active {
            color: var(--accent);
        }

        .nav-item-link.active i {
            transform: scale(1.1);
        }

        /* ============================================
           PRODUCT CARD (SHARED)
        ============================================ */
        .product-card {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--card-shadow-hover);
        }

        .product-img-wrapper {
            position: relative;
            aspect-ratio: 3/4;
            overflow: hidden;
            background: var(--surface2);
        }

        .product-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-img-wrapper img {
            transform: scale(1.08);
        }

        .product-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-3);
            font-size: 3rem;
        }

        /* Badges */
        .badge-wrap {
            position: absolute;
            top: 10px;
            left: 10px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            z-index: 2;
        }

        .product-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .badge-new {
            background: #1a1a1a;
            color: #fff;
        }

        .badge-hot {
            background: #e53e3e;
            color: #fff;
        }

        .badge-offer {
            background: var(--accent);
            color: #fff;
        }

        .badge-low {
            background: #ff8c00;
            color: #fff;
        }

        .badge-out {
            background: #9e9e9e;
            color: #fff;
        }

        .badge-ship {
            background: #38a169;
            color: #fff;
        }

        /* Wish button */
        .wish-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            z-index: 2;
        }

        .wish-btn:hover {
            color: #e53e3e;
            transform: scale(1.1);
        }

        [data-theme="dark"] .wish-btn {
            background: rgba(30, 30, 30, 0.85);
            color: #666;
        }

        /* Quick add overlay */
        .quick-add-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
            padding: 1rem 0.75rem 0.75rem;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .product-card:hover .quick-add-overlay {
            opacity: 1;
            transform: translateY(0);
        }

        .quick-add-btn {
            width: 100%;
            padding: 0.5rem;
            border-radius: 8px;
            border: none;
            background: #fff;
            color: #1a1a1a;
            font-weight: 700;
            font-size: 0.8rem;
            font-family: var(--font-main);
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .quick-add-btn:hover {
            background: var(--accent);
            color: #fff;
        }

        /* Card Body */
        .product-info {
            padding: 0.9rem;
        }

        .product-category {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-3);
            margin-bottom: 4px;
        }

        .product-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text);
            text-decoration: none;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 6px;
        }

        .product-name:hover {
            color: var(--accent);
        }

        /* Stars */
        .product-stars {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 8px;
        }

        .stars-icons {
            color: var(--accent);
            font-size: 0.72rem;
        }

        .star-score {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-2);
        }

        .review-count {
            font-size: 0.7rem;
            color: var(--text-3);
        }

        /* Price */
        .product-price-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .price-current {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text);
        }

        .price-original {
            font-size: 0.8rem;
            color: var(--text-3);
            text-decoration: line-through;
        }

        .add-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: none;
            background: var(--primary);
            color: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .add-btn:hover {
            background: var(--accent);
            transform: scale(1.1);
        }

        .add-btn:disabled {
            background: var(--border);
            color: var(--text-3);
            cursor: not-allowed;
        }

        [data-theme="dark"] .add-btn {
            color: #000;
        }

        /* ============================================
           FOOTER
        ============================================ */
        .shop-footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 3rem 0 2rem;
            margin-top: 4rem;
        }

        .footer-brand {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .footer-brand span {
            color: var(--accent);
        }

        .footer-tagline {
            font-size: 0.85rem;
            color: var(--text-2);
            margin-bottom: 1.5rem;
        }

        .footer-social a {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--surface2);
            border: 1px solid var(--border);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-2);
            text-decoration: none;
            margin-right: 8px;
            transition: all 0.2s;
        }

        .footer-social a:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .footer-heading {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-3);
            margin-bottom: 1rem;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: var(--text-2);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--accent);
        }

        .footer-bottom {
            border-top: 1px solid var(--border);
            padding-top: 1.5rem;
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .footer-bottom p {
            font-size: 0.8rem;
            color: var(--text-3);
            margin: 0;
        }

        .payment-icons {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .payment-icon {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 3px 8px;
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-2);
            letter-spacing: 0.5px;
        }

        /* ============================================
           UTILITIES
        ============================================ */
        @media (max-width: 767.98px) {
            .header-search {
                display: none;
            }

            .floating-cart {
                bottom: calc(var(--bottom-nav-height) + 12px);
                right: 12px;
            }

            .shop-footer {
                margin-top: 2rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- ===== HEADER ===== -->
    <header class="shop-header">
        <a href="{{ route('shop.index') }}" class="shop-brand">Style<span>Box</span></a>

        <form class="header-search" action="{{ route('shop.index') }}" method="GET">
            <i class="fas fa-search search-icon"></i>
            <input type="text" name="search" placeholder="Buscar productos, marcas..." value="{{ request('search') }}"
                autocomplete="off">
        </form>

        <div class="header-actions">
            <!-- Dark Mode Toggle -->
            <button class="header-btn" id="themeToggle" title="Cambiar tema" onclick="toggleTheme()">
                <i class="fas fa-moon" id="themeIcon"></i>
            </button>

            @auth
                <!-- Cart -->
                <a href="{{ route('cart.index') }}" class="header-btn" title="Carrito">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-badge" id="cartBadge" style="display:none">0</span>
                </a>
                <!-- Account -->
                <a href="{{ route('dashboard') }}" class="header-btn" title="Mi Cuenta">
                    <i class="fas fa-user"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="header-btn" title="Ingresar">
                    <i class="fas fa-sign-in-alt"></i>
                </a>
            @endauth
        </div>
    </header>

    <!-- ===== MAIN ===== -->
    <main>
        @yield('content')
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="shop-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="footer-brand">Style<span>Box</span></div>
                    <p class="footer-tagline">Moda premium al alcance de todos. Descubre las últimas tendencias.</p>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-pinterest-p"></i></a>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <p class="footer-heading">Tienda</p>
                    <ul class="footer-links">
                        <li><a href="{{ route('shop.index') }}">Catálogo</a></li>
                        <li><a href="{{ route('shop.index') }}?category=Hombre">Hombre</a></li>
                        <li><a href="{{ route('shop.index') }}?category=Mujer">Mujer</a></li>
                        <li><a href="{{ route('shop.index') }}?category=Niños">Niños</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-2">
                    <p class="footer-heading">Ayuda</p>
                    <ul class="footer-links">
                        <li><a href="#">Sobre Nosotros</a></li>
                        <li><a href="#">Contacto</a></li>
                        <li><a href="#">Devoluciones</a></li>
                        <li><a href="#">Seguimiento</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-2">
                    <p class="footer-heading">Legal</p>
                    <ul class="footer-links">
                        <li><a href="#">Términos de Uso</a></li>
                        <li><a href="#">Privacidad</a></li>
                        <li><a href="#">Cookies</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-2">
                    <p class="footer-heading">Mi Cuenta</p>
                    <ul class="footer-links">
                        @auth
                            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li><a href="{{ route('cart.index') }}">Mi Carrito</a></li>
                            <li><a href="{{ route('profile.edit') }}">Perfil</a></li>
                        @else
                            <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
                            <li><a href="{{ route('register') }}">Registrarse</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© {{ date('Y') }} StyleBox. Todos los derechos reservados.</p>
                <div class="payment-icons">
                    <span class="payment-icon">VISA</span>
                    <span class="payment-icon">MC</span>
                    <span class="payment-icon">YAPE</span>
                    <span class="payment-icon">PLIN</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- ===== FLOATING CART ===== -->
    @auth
        <a href="{{ route('cart.index') }}" class="floating-cart" id="floatingCart" title="Ver Carrito">
            <i class="fas fa-shopping-bag"></i>
            <span class="fc-badge" id="fcBadge" style="display:none">0</span>
        </a>
    @endauth

    <!-- ===== BOTTOM NAV ===== -->
    <nav class="bottom-nav">
        <a href="{{ route('shop.index') }}"
            class="nav-item-link {{ request()->routeIs('shop.index') ? 'active' : '' }}">
            <i class="fas fa-home"></i><span>Inicio</span>
        </a>
        <a href="{{ route('shop.index') }}" class="nav-item-link" id="mobileSearchBtn">
            <i class="fas fa-search"></i><span>Buscar</span>
        </a>
        <a href="{{ auth()->check() ? route('cart.index') : route('login') }}"
            class="nav-item-link {{ request()->routeIs('cart.*', 'checkout.*') ? 'active' : '' }}">
            <i class="fas fa-shopping-bag"></i><span>Bolsa</span>
        </a>
        <a href="{{ route('dashboard') }}" class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-user"></i><span>Perfil</span>
        </a>
    </nav>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ─── DARK MODE ───────────────────────────────────
        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('themeIcon');
            if (html.getAttribute('data-theme') === 'dark') {
                html.setAttribute('data-theme', 'light');
                icon.className = 'fas fa-moon';
                localStorage.setItem('sb_theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                icon.className = 'fas fa-sun';
                localStorage.setItem('sb_theme', 'dark');
            }
        }
        // Persist theme
        (function () {
            const saved = localStorage.getItem('sb_theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
            const icon = document.getElementById('themeIcon');
            if (icon) icon.className = saved === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        })();

        // ─── CART COUNT ──────────────────────────────────
        @auth
            async function updateCartCount() {
                try {
                    const r = await fetch('{{ route("cart.count") }}', { headers: { 'Accept': 'application/json' } });
                    if (!r.ok) return;
                    const d = await r.json();
                    const count = d.count ?? 0;
                    ['cartBadge', 'fcBadge'].forEach(id => {
                        const el = document.getElementById(id);
                        if (!el) return;
                        el.textContent = count > 99 ? '99+' : count;
                        el.style.display = count > 0 ? 'flex' : 'none';
                    });
                } catch (e) { }
            }
            updateCartCount();
        @endauth

            // ─── GLOBAL addToCart ─────────────────────────────
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
                        Swal.fire({ icon: 'success', title: '¡Agregado!', text: data.message, timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
                        if (typeof updateCartCount === 'function') updateCartCount();
                    } else {
                        if (res.status === 401) {
                            const c = await Swal.fire({ title: 'Inicia sesión', text: 'Para agregar productos al carrito debes ingresar.', icon: 'info', showCancelButton: true, confirmButtonColor: '#1a1a1a', confirmButtonText: 'Ir al Login' });
                            if (c.isConfirmed) window.location.href = "{{ route('login') }}";
                            return;
                        }
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo agregar.', confirmButtonColor: '#1a1a1a' });
                    }
                } catch (e) { console.error(e); }
            }

        function buyNow(productId) {
            window.location.href = "{{ url('/shop') }}/" + productId;
        }
    </script>
    @stack('scripts')
</body>

</html>