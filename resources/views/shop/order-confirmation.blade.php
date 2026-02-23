@extends('layouts.shop')

@section('title', 'Pedido Confirmado — StyleBox')

@push('styles')
    <style>
        .confirmation-wrapper {
            max-width: 680px;
            margin: 0 auto;
            padding: 2.5rem 1rem 6rem;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d4edda 0%, #b8f0c8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: #198754;
            margin: 0 auto 1.5rem;
        }

        .detail-card {
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 16px;
            padding: 1.4rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f8f8f8;
            gap: 1rem;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 0.82rem;
            color: #888;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            flex-shrink: 0;
        }

        .detail-value {
            font-size: 0.92rem;
            color: #1a1a1a;
            text-align: right;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
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

        .delivery-info-alert {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #93c5fd;
            border-radius: 12px;
            padding: 1rem 1.1rem;
        }

        .order-item-line {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.55rem 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .order-item-line:last-child {
            border-bottom: none;
        }

        .item-thumb {
            width: 44px;
            height: 44px;
            border-radius: 7px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .item-thumb-placeholder {
            width: 44px;
            height: 44px;
            border-radius: 7px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')
    <div class="confirmation-wrapper">

        {{-- ── Success header ── --}}
        <div class="text-center mb-4">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="fw-bold mb-1" style="font-size: 1.7rem;">¡Pedido registrado!</h1>
            <p class="text-muted">
                Tu pedido <strong>#{{ $sale->id }}</strong> ha sido confirmado.<br>
                <span class="small text-muted">Boleta: <strong>{{ $sale->numero_boleta }}</strong></span>
            </p>
        </div>

        {{-- ── Status Banner ── --}}
        <div class="detail-card text-center">
            <p class="text-muted small fw-semibold mb-2">ESTADO DEL PEDIDO</p>
            <span class="status-badge status-{{ $sale->estado_pedido }}">
                <i class="fas fa-circle" style="font-size:0.5rem;"></i>
                {{ $sale->estadoPedidoLabel() }}
            </span>
            <p class="text-muted small mt-2 mb-0">
                {{ $sale->date->format('d/m/Y H:i') }}
            </p>
        </div>

        {{-- ── Tipo de entrega ── --}}
        <div class="detail-card">
            <p class="detail-label mb-2"><i class="fas fa-store me-1"></i>Tipo de entrega</p>

            @if($sale->esDeliveryPropio())
                <div class="delivery-info-alert">
                    <div class="fw-bold mb-2" style="color:#1d4ed8;">
                        <i class="fas fa-motorcycle me-1"></i>Mi delivery recoge en tienda
                    </div>
                    <p class="small mb-3" style="color:#1e40af;">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Tu delivery puede recoger el pedido cuando esté listo.</strong>
                        Te avisaremos en cuanto el pedido esté disponible en tienda.
                    </p>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="detail-label">Repartidor</div>
                            <div class="detail-value fw-semibold">{{ $sale->nombre_repartidor }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">DNI / ID</div>
                            <div class="detail-value">{{ $sale->dni_repartidor }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Teléfono</div>
                            <div class="detail-value">{{ $sale->telefono_repartidor }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Empresa</div>
                            <div class="detail-value">{{ $sale->empresa_delivery }}</div>
                        </div>
                        @if($sale->placa_vehiculo)
                            <div class="col-6">
                                <div class="detail-label">Placa</div>
                                <div class="detail-value">{{ $sale->placa_vehiculo }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="d-flex align-items-center gap-2 text-muted">
                    <i class="fas fa-walking fs-5"></i>
                    <span>Recojo personal en tienda</span>
                </div>
            @endif
        </div>

        {{-- ── Order lines ── --}}
        <div class="detail-card">
            <p class="detail-label mb-2"><i class="fas fa-box me-1"></i>Productos</p>

            @foreach($sale->details as $detail)
                <div class="order-item-line">
                    @if($detail->product?->image)
                        <img src="{{ asset('storage/' . $detail->product->image) }}" class="item-thumb"
                            alt="{{ $detail->product->name }}">
                    @else
                        <div class="item-thumb-placeholder"><i class="fas fa-tshirt"></i></div>
                    @endif
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:0.9rem;">
                            {{ $detail->product?->name ?? 'Producto eliminado' }}
                        </div>
                        <div class="text-muted small">
                            {{ $detail->quantity }} × S/ {{ number_format($detail->unit_price, 2) }}
                        </div>
                    </div>
                    <div class="fw-bold small">S/ {{ number_format($detail->subtotal, 2) }}</div>
                </div>
            @endforeach

            <div class="d-flex justify-content-between align-items-center mt-3 pt-2" style="border-top: 2px solid #1a1a1a;">
                <span class="fw-bold">Total</span>
                <span class="fw-bold" style="color:#d4a017; font-size:1.1rem;">
                    S/ {{ number_format($sale->total, 2) }}
                </span>
            </div>
        </div>

        {{-- ── Payment ── --}}
        @if($sale->payments->isNotEmpty())
            <div class="detail-card">
                <p class="detail-label mb-2"><i class="fas fa-credit-card me-1"></i>Método de pago</p>
                @foreach($sale->payments as $payment)
                    <div class="d-flex justify-content-between">
                        <span>{{ $payment->paymentMethod?->name ?? 'N/A' }}</span>
                        <span class="fw-semibold">S/ {{ number_format($payment->amount, 2) }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ── CTA ── --}}
        <div class="text-center mt-4">
            <a href="{{ route('checkout.boleta', $sale) }}" target="_blank"
                class="btn btn-primary rounded-pill px-4 py-2 me-2">
                <i class="fas fa-file-invoice me-1"></i>Ver Comprobante
            </a>
            <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-4 py-2 me-2">
                <i class="fas fa-shopping-bag me-1"></i>Seguir comprando
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark rounded-pill px-4 py-2">
                <i class="fas fa-th-large me-1"></i>Mis pedidos
            </a>
        </div>

    </div>
@endsection