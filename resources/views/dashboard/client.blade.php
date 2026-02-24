@extends('layouts.public')

@section('title', 'Mis Compras')

@push('styles')
    <style>
        .status-badge {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .status-pendiente_pago {
            background: #fff3cd;
            color: #856404;
        }

        .status-pagado {
            background: #cff4fc;
            color: #055160;
        }

        .status-listo_recojo {
            background: #d1e7dd;
            color: #0a3622;
        }

        .status-recogido {
            background: #e2e3e5;
            color: #41464b;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">Historial de Pedidos</h6>
                </div>
                <div class="card-body p-0">
                    @if($myPurchases->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aún no has realizado compras con este correo.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4"># Compra</th>
                                        <th>Fecha</th>
                                        <th>Boleta</th>
                                        <th>Productos</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myPurchases as $sale)
                                        <tr>
                                            <td class="ps-4 fw-bold">#{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td>{{ $sale->date->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <code class="small fw-bold text-dark">{{ $sale->numero_boleta ?? '---' }}</code>
                                            </td>
                                            <td>
                                                <ul class="list-unstyled mb-0 small">
                                                    @foreach($sale->details as $detail)
                                                        <li>
                                                            {{ $detail->quantity }}x {{ $detail->product->name }}
                                                            @if($detail->talla)
                                                                <span class="text-muted small">(Talla: {{ $detail->talla->nombre }})</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td class="text-end fw-bold">S/ {{ number_format($sale->total, 2) }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge status-badge status-{{ $sale->estado_pedido }} px-3 py-2 rounded-pill">
                                                    {{ $sale->estadoPedidoLabel() }}
                                                </span>
                                            </td>
                                            <td class="text-center pe-4">
                                                <a href="{{ route('checkout.boleta', $sale) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-dark rounded-pill">
                                                    <i class="fas fa-file-invoice me-1"></i>Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection