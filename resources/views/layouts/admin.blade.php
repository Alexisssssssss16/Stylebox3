<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'StyleBox Admin')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 250px;
            --header-height: 60px;
            --primary-color: #1a1a1a;
            --secondary-color: #f8f9fa;
            --accent-color: #d4a017;
            --text-color: #333;
            --sidebar-bg: #fff;
            --sidebar-hover: #f4f6f9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            color: var(--text-color);
            overflow-x: hidden;
        }

        /* Layout Structure */
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid #e9ecef;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .sidebar-brand span {
            color: var(--accent-color);
        }

        .nav-links {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
        }

        .nav-item-header {
            font-size: 0.75rem;
            font-weight: 700;
            color: #adb5bd;
            text-transform: uppercase;
            padding: 0.75rem 1.5rem 0.25rem;
            margin-top: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #495057;
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: var(--primary-color);
        }

        .nav-link.active {
            background-color: #fff8e1;
            /* Light yellow tint */
            color: var(--primary-color);
            border-left-color: var(--accent-color);
        }

        .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }

        .sidebar-footer {
            border-top: 1px solid #e9ecef;
            padding: 1rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background-color: var(--primary-color);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .logout-btn {
            color: #adb5bd;
            transition: color 0.2s;
        }

        .logout-btn:hover {
            color: #dc3545;
        }

        /* Main Content Area */
        .main-panel {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-panel.expanded {
            margin-left: 0;
        }

        /* Header */
        .top-header {
            height: var(--header-height);
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-wrapper {
            padding: 2rem;
            flex: 1;
        }

        /* Utilities */
        .card-custom {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            background: #fff;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        .btn-primary-custom:hover {
            background-color: #000;
            border-color: #000;
        }

        .table-custom th {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            background-color: #fff;
            border-bottom: 2px solid #e9ecef;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-panel {
                margin-left: 0;
            }

            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .overlay.active {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <div class="wrapper">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Mobile Overlay -->
        <div class="overlay" id="sidebarOverlay"></div>

        <!-- Main Panel -->
        <div class="main-panel" id="mainPanel">
            <!-- Header -->
            @include('partials.header')

            <!-- Content -->
            <main class="content-wrapper">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="text-center py-3 text-muted small">
                &copy; {{ date('Y') }} StyleBox SaaS. Todos los derechos reservados.
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const mainPanel = document.getElementById('mainPanel');
            const toggle = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainPanel.classList.toggle('expanded');
                }
            }

            if (toggle) {
                toggle.addEventListener('click', toggleSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', toggleSidebar);
            }
        });
    </script>
    @stack('scripts')
</body>

</html>