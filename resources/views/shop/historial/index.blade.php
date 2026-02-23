@extends('layouts.public')

@section('title', 'Mi Historial de Compras — StyleBox')

@push('styles')
    <style>
        .historial-wrapper {
            max-width: 900px;
            margin: 2rem auto 6rem;
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pendiente_pago { background: #fff3cd; color: #856404; }\n        .status-pagado { background: #d1e7dd; color: #0a3622; }\n        .status-preparando { background: #cff4fc; color: #055160; }\n        .status-enviado { background: #e0e7ff; color: #3730a3; }\n        .status-entregado { background: #f3f4f6; color: #111827; }\n        .status-cancelado { background: #fee2e2; color: #991b1b; }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="historial-wrapper">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">Mi Historial de Compras</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-dark">Inicio</a></li>
                        <li class="breadcrumb-item active">Mi Historial</li>
                    </ol>
                </nav>
            </div>

            @if($purchases->isEmpty())
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <div class="mb-3 text-muted">
                            <i class="fas fa-shopping-bag fa-4x opacity-25"></i>
                        </div>
                        <h5 class="fw-bold">Aún no has realizado compras</h5>
                        <p class="text-muted">Descubre las últimas tendencias y estrena hoy mismo.</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-4">Ir a la tienda</a>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3"># Boleta / Pedido</th>
                                    <th class="py-3">Fecha</th>
                                    <th class="py-3">Productos</th>
                                    <th class="text-end py-3">Total</th>
                                    <th class="text-center py-3">Estado</th>
                                    <th class="text-center pe-4 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases as $sale)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">
                                                {{ $sale->numero_boleta ?? '#' . str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</div>
                                            <div class="small text-muted">Transacción: #{{ $sale->id }}</div>
                                        </td>
                                        <td>{{ $sale->date->format('d/m/Y') }}<br><span
                                                class="small text-muted">{{ $sale->date->format('H:i') }}</span></td>
                                        <td>
                                            <div class="small fw-semibold text-truncate" style="max-width: 200px;">
                                                {{ $sale->details->count() }}
                                                {{ $sale->details->count() > 1 ? 'productos' : 'producto' }}
                                            </div>
                                            <span class="small text-muted">
                                                {{ $sale->details->take(2)->map(fn($d) => $d->product?->name)->join(', ') }}
                                                @if($sale->details->count() > 2) ... @endif
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">S/ {{ number_format($sale->total, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge status-badge status-{{ $sale->estado }} px-3 py-2 rounded-pill">
                                                @if($sale->estado === 'pagado') 🟢 @endif {{ $sale->estadoLabel() }}
                                            </span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="btn-group">
                                                <a href="{{ route('historial.show', $sale) }}"
                                                    class="btn btn-sm btn-outline-dark rounded-pill me-1">
                                                    Ver Detalle
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($purchases->hasPages())
                        <div class="card-footer bg-white py-3">
                            {{ $purchases->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection