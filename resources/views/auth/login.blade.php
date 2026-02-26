@extends('layouts.auth')

@section('title', 'Iniciar Sesión — StyleBox')

@section('content')
    <div class="text-center mb-5">
        <h2 class="fw-extrabold text-dark mb-2" style="letter-spacing: -1.5px; font-size: 2.25rem;">Welcome Back</h2>
        <p class="text-muted fw-medium">Ingresa a tu cuenta de StyleBox</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-4 py-3 px-4 mb-4 d-flex align-items-center"
            style="background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2 !important;">
            <i class="fas fa-exclamation-circle me-3 fs-5"></i>
            <span class="fw-semibold small">{{ $errors->first() }}</span>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" id="loginForm">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label small fw-bold text-dark mb-2 ps-1">EMAIL ADDRESS</label>
            <div class="position-relative">
                <i class="far fa-envelope position-absolute text-muted"
                    style="left: 20px; top: 50%; transform: translateY(-50%); font-size: 1.1rem;"></i>
                <input type="email" id="email" name="email" class="form-control fw-semibold"
                    style="border-radius: 16px; border: 2px solid #e2e8f0; background: #fff; padding: 14px 20px 14px 54px; font-size: 1rem; color: #0f172a; transition: all 0.2s ease;"
                    placeholder="name@example.com" required autofocus value="{{ old('email') }}">
            </div>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label for="password" class="form-label small fw-bold text-dark mb-0 ps-1">PASSWORD</label>
                <a href="#" class="text-primary small fw-bold text-decoration-underline">Forgot?</a>
            </div>
            <div class="position-relative">
                <i class="far fa-lock-alt position-absolute text-muted"
                    style="left: 20px; top: 50%; transform: translateY(-50%); font-size: 1.1rem;"></i>
                <input type="password" id="password" name="password" class="form-control fw-semibold"
                    style="border-radius: 16px; border: 2px solid #e2e8f0; background: #fff; padding: 14px 20px 14px 54px; font-size: 1rem; color: #0f172a; transition: all 0.2s ease;"
                    placeholder="••••••••" required>
                <button type="button" class="btn btn-link position-absolute p-0 text-muted"
                    style="right: 20px; top: 50%; transform: translateY(-50%); text-decoration: none;"
                    onclick="togglePassword()">
                    <i class="fas fa-eye" id="pwIcon" style="font-size: 1.1rem;"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn w-100 py-3 fw-bold mb-5 mt-2"
            style="border-radius: 18px; background: #f3c332; border: none; color: #000; font-size: 1.1rem; box-shadow: 0 12px 24px rgba(243, 195, 50, 0.3); transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            Sign In
        </button>

        <div class="position-relative text-center mb-4">
            <hr class="text-muted opacity-25">
            <span class="position-absolute translate-middle px-4 bg-white text-muted fw-bold"
                style="top: 50%; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px;">Or continue
                with</span>
        </div>

        <div class="row g-3 mb-5">
            <div class="col-6">
                <a href="{{ route('social.login', 'google') }}"
                    class="btn w-100 d-flex align-items-center justify-content-center gap-2 py-3 fw-bold"
                    style="border-radius: 16px; border: 2px solid #e2e8f0; background: #fff; color: #334155; font-size: 0.95rem; text-decoration: none; transition: all 0.2s ease;">
                    <img src="https://www.gstatic.com/images/branding/product/1x/googleg_48dp.png" width="22" height="22"
                        alt="Google">
                    Google
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('social.login', 'facebook') }}"
                    class="btn w-100 d-flex align-items-center justify-content-center gap-2 py-3 fw-bold"
                    style="border-radius: 16px; border: 2px solid #e2e8f0; background: #fff; color: #334155; font-size: 0.95rem; text-decoration: none; transition: all 0.2s ease;">
                    <i class="fab fa-facebook" style="font-size: 1.3rem; color: #1877f2;"></i>
                    Facebook
                </a>
            </div>
        </div>

        <div class="text-center pb-2">
            <p class="text-muted fw-medium mb-0">Don't have an account?
                <a href="{{ route('register') }}" class="text-dark fw-bold text-decoration-underline ms-1">Create
                    Account</a>
            </p>
        </div>
    </form>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('pwIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        // Add focus effect via JS for cleaner CSS
        document.querySelectorAll('.form-control').forEach(el => {
            el.addEventListener('focus', () => {
                el.parentElement.querySelector('i').style.color = '#f3c332';
                el.style.borderColor = '#f3c332';
                el.style.boxShadow = '0 0 0 4px rgba(243, 195, 50, 0.15)';
            });
            el.addEventListener('blur', () => {
                el.parentElement.querySelector('i').style.color = '#64748b';
                el.style.borderColor = '#e2e8f0';
                el.style.boxShadow = 'none';
            });
        });
    </script>
@endsection