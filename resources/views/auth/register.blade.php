@extends('layouts.auth')

@section('content')
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="card shadow-lg border-0" style="width: 100%; max-width: 400px;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-shopping-bag fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">Crear Cuenta</h3>
                    <p class="text-muted">Únete a StyleBox</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="name" name="name" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            required>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">Registrarse</button>
                    </div>

                    <div class="text-center">
                        <p class="mb-0">¿Ya tienes cuenta? <a href="{{ route('login') }}"
                                class="text-decoration-none">Inicia Sesión</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection