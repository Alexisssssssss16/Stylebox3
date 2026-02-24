@extends('layouts.admin')

@section('title', 'Crear Nuevo Permiso')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header bg-white pt-4 pb-0">
                    <h5 class="mb-0 fw-bold">Crear Nuevo Permiso</h5>
                </div>
                <div class="card-body p-4">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>¡Ups!</strong> Hubo algunos problemas con tu entrada.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.permissions.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="fw-bold mb-2">Nombre del Permiso:</label>
                                    <input type="text" name="name" class="form-control" placeholder="Ej: users_create">
                                    <small class="text-muted">Se recomienda usar formato: <code>modulo_accion</code></small>
                                </div>
                            </div>
                            <div class="col-md-12 text-center pt-3 border-top">
                                <a class="btn btn-outline-secondary" href="{{ route('admin.permissions.index') }}">
                                    Cancelar</a>
                                <button type="submit" class="btn btn-primary-custom px-4">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection