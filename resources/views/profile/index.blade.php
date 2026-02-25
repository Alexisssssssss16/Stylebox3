@extends('layouts.admin')

@section('title', 'Mi Perfil | StyleBox')
@section('page_title', 'Mi Perfil')

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-4">
                <div class="card card-custom mb-4 text-center p-4">
                    <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted small mb-3">{{ $user->email }}</p>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-primary rounded-pill px-3">
                            {{ $user->getRoleNames()->first() ?? 'Usuario' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-custom">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-user-edit me-2"></i>Editar Información</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nombre Completo</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Correo Electrónico</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-4">

                                <h6 class="fw-bold mb-3 text-secondary">Cambiar Contraseña <small
                                        class="fw-normal text-muted">(Opcional)</small></h6>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nueva Contraseña</label>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    <div class="form-text small">Mínimo 8 caracteres.</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Confirmar Contraseña</label>
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top text-end">
                                <button type="submit" class="btn btn-primary-custom px-4 shadow-sm">
                                    <i class="fas fa-save me-2"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection