@extends('layouts.admin')

@section('title', 'Gestión de Permisos')

@section('content')
    <div class="card card-custom">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-key me-2 text-warning"></i>Permisos del Sistema</h5>
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary-custom btn-sm">
                <i class="fas fa-plus me-1"></i> Nuevo Permiso
            </a>
        </div>
        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Group permissions by module for display --}}
            @php
                $grouped = $permissions->getCollection()->groupBy(function ($p) {
                    return explode('_', $p->name)[0];
                });
            @endphp

            @foreach($grouped as $module => $perms)
                <div class="mb-4">
                    <h6 class="text-uppercase fw-bold text-muted small border-bottom pb-2 mb-3">
                        <i class="fas fa-layer-group me-1"></i> {{ ucfirst($module) }}
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-custom table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">ID</th>
                                    <th>Nombre del Permiso</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($perms as $permission)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $permission->id }}</td>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded small">{{ $permission->name }}</code>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a class="btn btn-light btn-sm text-primary me-1"
                                                href="{{ route('admin.permissions.edit', $permission->id) }}" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Eliminar el permiso {{ $permission->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light btn-sm text-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <div class="mt-3">
                {{ $permissions->links() }}
            </div>

        </div>
    </div>
@endsection