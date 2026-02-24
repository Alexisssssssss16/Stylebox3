<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'StyleBox - Admin')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #1a1a1a;
            --accent-color: #d4a017;
            /* Gold/Mustard accent for fashion feel */
            --bg-color: #f4f6f9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: #333;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ffffff;
            border-right: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand span {
            color: var(--accent-color);
        }

        .nav-links {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .nav-item {
            list-style: none;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
        }

        .nav-link i {
            width: 24px;
            margin-right: 10px;
            text-align: center;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-color);
            background-color: #f8f9fa;
            border-right: 3px solid var(--accent-color);
        }

        .user-profile {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Utilities */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            background: white;
            overflow: hidden;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            padding: 0.5rem 1.2rem;
        }

        .btn-primary-custom:hover {
            background-color: #333;
            border-color: #333;
        }

        .table-custom th {
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <a href="#" class="sidebar-brand">
            Style<span>Box</span>
        </a>
        <ul class="nav-links list-unstyled">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('products.index') }}"
                    class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-tshirt"></i> Productos
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('measurement_units.index') }}"
                    class="nav-link {{ request()->routeIs('measurement_units.*') ? 'active' : '' }}">
                    <i class="fas fa-ruler"></i> Unidades de Medida
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clients.index') }}"
                    class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Clientes
                </a>
            </li>
        </ul>
        <div class="user-profile">
            <div class="user-avatar">AD</div>
            <div>
                <div class="fw-bold">Admin User</div>
                <small class="text-muted">admin@stylebox.com</small>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Mobile Toggle -->
        <button class="btn btn-light d-md-none mb-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');

        if (toggle) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }
    </script>
    @stack('scripts')
</body>

</html>