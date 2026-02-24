@extends('layouts.auth')

@section('title', 'Iniciar Sesión - StyleBox')

@section('content')
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="card shadow-lg border-0" style="width: 100%; max-width: 400px;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-layer-group fa-3x text-warning mb-3"></i>
                    <h3 class="fw-bold">StyleBox</h3>
                    <p class="text-muted">Accede a tu cuenta</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger small p-2 text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Corre Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="nombre@ejemplo.com"
                            required autofocus value="{{ old('email') }}">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••"
                            required>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">Iniciar Sesión</button>
                    </div>

                    <div class="text-center">
                        <p class="mb-0">¿No tienes cuenta? <a href="{{ route('register') }}"
                                class="text-decoration-none">Regístrate aquí</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection