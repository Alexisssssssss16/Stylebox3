@extends('layouts.public')

@section('content')
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 60vh;">
        <div class="card shadow border-0" style="max-width: 600px; width: 100%;">
            <div class="card-body p-5 text-center">
                <i class="fas fa-envelope-open-text fa-4x text-primary mb-3"></i>
                <h2 class="fw-bold mb-3">Verifica tu Correo</h2>
                <p class="text-muted mb-4">
                    ¡Gracias por registrarte! Antes de comenzar, por favor verifica tu dirección de correo electrónico
                    haciendo clic en el enlace que acabamos de enviarte a tu correo.
                </p>
                <p class="small text-muted mb-4">
                    Si no recibiste el correo, con gusto te enviaremos otro.
                </p>

                @if (session('message') == 'Verification link sent!')
                    <div class="alert alert-success mb-4" role="alert">
                        Se ha enviado un nuevo enlace de verificación a tu correo.
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        Reenviar Correo de Verificación
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-link text-muted text-decoration-none">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection