<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'StyleBox')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #f3c332;
            --primary-solid: #f3c332;
            --bg-gradient: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
            --card-white: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --input-bg: #ffffff;
            --border-heavy: #e2e8f0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 2rem;
            color: var(--text-dark);
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            z-index: 10;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-card {
            background: var(--card-white);
            border-radius: 28px;
            padding: 3.5rem 3rem;
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(0, 0, 0, 0.05);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .brand-logo:hover {
            transform: scale(1.02);
        }

        .logo-box {
            width: 56px;
            height: 56px;
            background: var(--primary-solid);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #000;
            box-shadow: 0 8px 16px rgba(243, 195, 50, 0.25);
            animation: pulse-soft 3s infinite ease-in-out;
        }

        @keyframes pulse-soft {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 8px 16px rgba(243, 195, 50, 0.25);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 12px 24px rgba(243, 195, 50, 0.35);
            }
        }

        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 2.4rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -1px;
            margin-bottom: 0;
        }

        .brand-name span {
            color: var(--primary-solid);
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }

            .auth-card {
                padding: 2.5rem 1.75rem;
                border-radius: 24px;
            }

            .brand-name {
                font-size: 2rem;
            }

            .logo-box {
                width: 48px;
                height: 48px;
                font-size: 1.25rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="auth-container">
        <div class="brand-header">
            <a href="{{ url('/') }}" class="brand-logo">
                <div class="logo-box"><i class="fas fa-layer-group"></i></div>
                <span class="brand-name">Style<span>Box</span></span>
            </a>
        </div>
        <div class="auth-card">
            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>