@extends('layouts.admin')

@section('title', 'Gestión de Pedidos Virtuales - StyleBox')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">📦 Gestión de Pedidos Virtuales</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card card-custom">
        <div class="card-body">
            <!-- Filters -->
            <form action="{{ route('admin.pedidos.index') }}" method="GET" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                   placeholder="Nº Pedido o Cliente..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="estado" class="form-select">
                            <option value="">Todos los estados</option>
                            @foreach($estados as $key => $label)
                                <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="btn-group w-100">
                            <button class="btn btn-dark" type="submit">Filtrar</button>
                            @if(request()->anyFilled(['search', 'estado', 'fecha']))
                                <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-custom">
                    <thead>
                        <tr>
                            <th>Nº Pedido</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Método Pago</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="fw-bold">{{ $order->numero_boleta ?? '#'.str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="fw-bold">{{ $order->buyer->name ?? ($order->client->name ?? 'N/A') }}</div>
                                    <small class="text-muted">{{ $order->buyer->email ?? ($order->client->email ?? '') }}</small>
                                </td>
                                <td>{{ $order->date->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold">S/ {{ number_format($order->total, 2) }}</td>
                                <td>
                                    @php $pm = $order->payments->first()?->paymentMethod; @endphp
                                    <span class="small">{{ $pm ? $pm->name : 'N/A' }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($order->estado) {
                                            'pendiente_pago' => 'warning',
                                            'pagado' => 'success',
                                            'preparando' => 'info',
                                            'enviado' => 'primary',
                                            'entregado' => 'dark',
                                            'cancelado' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }} bg-opacity-10 text-{{ $badgeClass }} px-3 py-2 rounded-pill">
                                        {{ $order->estadoLabel() }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.pedidos.show', $order->id) }}" class="btn btn-sm btn-light text-primary" title="Ver Detalle">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($order->estado == 'pendiente_pago')
                                            <form action="{{ route('admin.pedidos.confirmar', $order->id) }}" method="POST" class="d-inline confirm-payment-form">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-light text-success" title="Confirmar Pago">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-shopping-basket fa-3x mb-3"></i>
                                        <p>No se encontraron pedidos virtuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.confirm-payment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Confirmar Pago?',
                text: "Esta acción cambiará el estado a PAGADO y registrará tu usuario como confirmador.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a1a1a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, confirmar pago',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
