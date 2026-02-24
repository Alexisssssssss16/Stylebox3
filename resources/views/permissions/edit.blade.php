@extends('layouts.admin')

@section('title', 'Editar Permiso')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header bg-white pt-4 pb-0">
                    <h5 class="mb-0 fw-bold">Editar Permiso</h5>
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

                    <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="fw-bold mb-2">Nombre del Permiso:</label>
                                    <input type="text" name="name" value="{{ $permission->name }}" class="form-control"
                                        placeholder="Nombre">
                                </div>
                            </div>
                            <div class="col-md-12 text-center pt-3 border-top">
                                <a class="btn btn-outline-secondary" href="{{ route('admin.permissions.index') }}">
                                    Cancelar</a>
                                <button type="submit" class="btn btn-primary-custom px-4">Actualizar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection