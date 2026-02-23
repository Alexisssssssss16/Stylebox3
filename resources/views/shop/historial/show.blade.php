@extends('layouts.public')

@section('title', 'Detalle de Pedido #' . $sale->id . ' — StyleBox')

@push('styles')
    <style>
        .historial-wrapper {
            max-width: 800px;
            margin: 2rem auto 6rem;
        }

        .detail-header {
            background: #1a1a1a;
            color: #fff;
            border-radius: 16px 16px 0 0;
            padding: 2rem;
        }

        .status-badge {
            font-size: 0.85rem;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .status-pendiente_pago {
            background: #fff3cd;
            color: #856404;
        }

        \n .status-pagado {
            background: #d1e7dd;
            color: #0a3622;
        }

        \n .status-preparando {
            background: #cff4fc;
            color: #055160;
        }

        \n .status-enviado {
            background: #e0e7ff;
            color: #3730a3;
        }

        \n .status-entregado {
            background: #f3f4f6;
            color: #111827;
        }

        \n .status-cancelado {
            background: #fee2e2;
            color: #991b1b;
        }

        .item-card {
            border: none;
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }

        .item-card:last-child {
            border-bottom: none;
        }

        .item-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="historial-wrapper">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <a href="{{ route('historial.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Volver al historial
                </a>
                @if($sale->numero_boleta)
                    <a href="{{ route('checkout.boleta', $sale) }}" target="_blank"
                        class="btn btn-dark btn-sm rounded-pill px-3">
                        <i class="fas fa-print me-1"></i> Ver Comprobante
                    </a>
                @endif
            </div>

            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="detail-header shadow-sm">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div class="small opacity-75 text-uppercase fw-bold mb-1">CÓDIGO DE PEDIDO:
                                #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</div>
                            <h3 class="fw-bold mb-0">Detalle de tu compra</h3>
                            <div class="mt-2 small opacity-75">
                                Realizada el {{ $sale->date->format('d \d\e F, Y \a \l\a\s H:i') }}
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <span class="status-badge status-{{ $sale->estado }}">
                                @if($sale->estado === 'pagado') 🟢 @endif {{ $sale->estadoLabel() }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Productos comprados</h5>
                    <div class="mb-4">
                        @foreach($sale->details as $detail)
                            <div class="item-card d-flex align-items-center">
                                @if($detail->product?->image)
                                    <img src="{{ asset('storage/' . $detail->product->image) }}" class="item-img"
                                        alt="{{ $detail->product->name }}">
                                @else
                                    <div class="item-img bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-tshirt text-muted"></i>
                                    </div>
                                @endif
                                <div class="ms-3 flex-grow-1">
                                    <h6 class="fw-bold mb-0">{{ $detail->product?->name ?? 'Producto no disponible' }}</h6>
                                    <div class="small text-muted">Precio Unitario: S/
                                        {{ number_format($detail->unit_price, 2) }}
                                    </div>
                                    <div class="small fw-semibold mt-1">Cantidad: {{ (int) $detail->quantity }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold fs-6">S/ {{ number_format($detail->subtotal, 2) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span>S/ {{ number_format($sale->total / 1.18, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">IGV (18%):</span>
                                <span>S/ {{ number_format($sale->total - ($sale->total / 1.18), 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2 border-top">
                                <h5 class="fw-bold">Total Pagado:</h5>
                                <h5 class="fw-bold text-dark">S/ {{ number_format($sale->total, 2) }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 border-top pt-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold small text-uppercase mb-3">Información de pago</h6>
                                @if($sale->payments->isNotEmpty())
                                    @foreach($sale->payments as $payment)
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-credit-card text-muted"></i>
                                            <span>{{ $payment->paymentMethod?->name ?? 'Método registrado' }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-muted small">Pendiente de registro de pago</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold small text-uppercase mb-3">Opciones adicionales</h6>
                                <form action="{{ route('historial.repeat', $sale) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-dark btn-sm rounded-pill w-100">
                                        <i class="fas fa-redo me-1"></i> Volver a comprar estos productos
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection