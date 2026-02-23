@extends('layouts.admin')

@section('title', 'Perfil de Cliente - StyleBox')

@push('styles')
    <style>
        .client-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        .client-header {
            background: #1a1a1a;
            color: white;
            padding: 2.5rem 2rem;
        }

        .stat-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #eee;
        }

        .history-table th {
            background: #f8f9fa;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .badge-soft-primary {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .badge-soft-info {
            background: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <a href="{{ route('clients.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Volver a Clientes
            </a>
        </div>

        <div class="card client-card shadow-sm mb-4">
            <div class="client-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <h2 class="fw-bold mb-0">{{ $client->name }}</h2>
                            @if($client->client_type == 'virtual')
                                <span class="badge badge-soft-primary px-3 py-2 rounded-pill"><i
                                        class="fas fa-globe me-1"></i>VIRTUAL</span>
                            @else
                                <span class="badge badge-soft-info px-3 py-2 rounded-pill"><i
                                        class="fas fa-store me-1"></i>PRESENCIAL</span>
                            @endif
                        </div>
                        <div class="opacity-75 d-flex gap-4">
                            <span><i class="fas fa-envelope me-2"></i>{{ $client->email }}</span>
                            @if($client->phone)
                                <span><i class="fas fa-phone me-2"></i>{{ $client->phone }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="small opacity-75">MIEMBRO DESDE</div>
                        <div class="fw-bold fs-5">{{ $client->created_at->format('d M, Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="stat-card text-center">
                            <div class="text-muted small mb-1">TOTAL COMPRAS</div>
                            <div class="h3 fw-bold mb-0">S/ {{ number_format($sales->sum('total'), 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center">
                            <div class="text-muted small mb-1">CANTIDAD PEDIDOS</div>
                            <div class="h3 fw-bold mb-0">{{ $sales->total() }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center">
                            <div class="text-muted small mb-1">ÚLTIMA ACTIVIDAD</div>
                            <div class="h3 fw-bold mb-0">{{ $sales->first()?->date->diffForHumans() ?? 'Sin registros' }}
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-4">Historial Omnicanal de Ventas</h5>
                <div class="table-responsive">
                    <table class="table align-middle history-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Boleta / Pedido</th>
                                <th>Canal</th>
                                <th>Productos</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td>{{ $sale->date->format('d/m/Y H:i') }}</td>
                                    <td class="fw-bold">
                                        {{ $sale->numero_boleta ?? '#' . str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        @if($sale->buyer_id)
                                            <span class="small text-primary fw-bold"><i
                                                    class="fas fa-shopping-cart me-1"></i>ONLINE</span>
                                        @else
                                            <span class="small text-info fw-bold"><i class="fas fa-cash-register me-1"></i>POS /
                                                TIENDA</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small text-truncate" style="max-width: 250px;">
                                            {{ $sale->details->map(fn($d) => $d->product?->name)->join(', ') }}
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold">S/ {{ number_format($sale->total, 2) }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-{{ $sale->estado_pedido == 'pagado' ? 'success' : 'warning' }} bg-opacity-10 text-{{ $sale->estado_pedido == 'pagado' ? 'success' : 'warning' }} rounded-pill px-3">
                                            {{ ucfirst($sale->estado_pedido) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('checkout.boleta', $sale) }}" target="_blank"
                                            class="btn btn-sm btn-outline-dark rounded-pill">
                                            <i class="fas fa-print me-1"></i> Boleta
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">No se registran ventas para este
                                        cliente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sales->hasPages())
                    <div class="mt-4">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection