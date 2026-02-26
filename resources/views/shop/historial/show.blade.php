@extends('layouts.shop')

@section('title', 'Detalle de Pedido #' . $sale->id . ' — StyleBox Premium')

@push('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --premium-bg: #f8fafc;
            --premium-surface: #ffffff;
            --premium-accent: #c9a84c;
            --premium-text: #0f172a;
            --premium-text-muted: #64748b;
            --premium-border: #e2e8f0;
            --premium-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        .detail-page {
            font-family: 'Inter', sans-serif;
            background: var(--premium-bg);
            min-height: 100vh;
            padding: 3rem 0 6rem;
        }

        .detail-container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* ── TOP NAVIGATION ── */
        .top-nav-pro {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .btn-back-pro {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--premium-text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .btn-back-pro:hover {
            color: var(--premium-text);
        }

        /* ── MAIN CARD ── */
        .order-detail-card {
            background: var(--premium-surface);
            border-radius: 2rem;
            box-shadow: var(--premium-shadow);
            overflow: hidden;
            border: 1px solid var(--premium-border);
        }

        /* Detail Header */
        .detail-header-pro {
            background: #1e293b;
            color: #ffffff;
            padding: 2.5rem;
            position: relative;
        }

        .detail-header-pro .order-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }

        .detail-header-pro h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }

        .detail-header-pro .order-date {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Status Badge in Detail */
        .status-badge-detail {
            position: absolute;
            top: 2.5rem;
            right: 2.5rem;
            padding: 0.75rem 1.75rem;
            border-radius: 50px;
            font-weight: 800;
            font-size: 0.85rem;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* ── SHIPMENT & TRACKING ── */
        .section-pro {
            padding: 2.5rem;
            border-bottom: 1px solid var(--premium-border);
        }

        .section-title-pro {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 1.75rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--premium-text);
        }

        .section-title-pro i {
            color: var(--premium-accent);
        }

        /* Tracking Timeline (Larger for Detail) */
        .tracking-detail-pro {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 2rem 0;
        }

        .tracking-detail-pro::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 2rem;
            right: 2rem;
            height: 4px;
            background: #f1f5f9;
            z-index: 1;
        }

        .td-step {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100px;
        }

        .td-dot {
            width: 32px;
            height: 32px;
            background: #ffffff;
            border: 4px solid #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .td-label {
            font-size: 0.7rem;
            font-weight: 700;
            margin-top: 12px;
            color: #94a3b8;
            text-transform: uppercase;
        }

        .td-step.done .td-dot {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }

        .td-step.done .td-label {
            color: #1e293b;
        }

        .td-step.active .td-dot {
            background: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.1);
        }

        .td-step.active .td-label {
            color: #6366f1;
        }

        /* ── PRODUCT LIST ── */
        .product-row-pro {
            display: flex;
            align-items: center;
            padding: 1.5rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .product-row-pro:last-child {
            border-bottom: none;
        }

        .product-img-detail {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--premium-border);
            flex-shrink: 0;
        }

        .product-img-detail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info-pro {
            flex-grow: 1;
            padding: 0 1.5rem;
        }

        .product-info-pro h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: var(--premium-text);
        }

        .product-variant-badge {
            display: inline-block;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.65rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            margin-right: 6px;
        }

        .product-price-pro {
            text-align: right;
            min-width: 100px;
        }

        .product-price-pro .total {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--premium-text);
        }

        .product-price-pro .sub {
            font-size: 0.75rem;
            color: var(--premium-text-muted);
        }

        /* ── TOTALS ── */
        .totals-section-pro {
            background: #f8fafc;
            padding: 2.5rem;
        }

        .total-row-pro {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .total-row-pro.grand-total {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid var(--premium-border);
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
        }

        /* ── INFO BLOCKS ── */
        .info-grid-pro {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            padding: 2.5rem;
        }

        .info-block-pro h5 {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .info-content-pro {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--premium-text);
        }

        /* ── MOBILE ── */
        @media (max-width: 768px) {
            .detail-header-pro {
                padding: 1.5rem;
            }

            .status-badge-detail {
                position: static;
                margin-top: 1rem;
                display: block;
                text-align: center;
            }

            .tracking-detail-pro {
                display: none;
            }

            .info-grid-pro {
                grid-template-columns: 1fr;
            }

            .product-row-pro {
                flex-wrap: wrap;
            }

            .product-price-pro {
                width: 100%;
                text-align: left;
                margin-top: 1rem;
                margin-left: 95px;
            }
        }
    </style>
@endpush

@php
    $statusConfig = [
        'pendiente_pago' => ['label' => 'Por Pagar', 'class' => 'bg-pending', 'icon' => 'fa-wallet'],
        'pendiente' => ['label' => 'Pendiente', 'class' => 'bg-pending', 'icon' => 'fa-clock'],
        'pagado' => ['label' => 'Confirmado', 'class' => 'bg-completed', 'icon' => 'fa-check-circle'],
        'preparando' => ['label' => 'Empacando', 'class' => 'bg-shipping', 'icon' => 'fa-box-open'],
        'enviado' => ['label' => 'En Camino', 'class' => 'bg-shipping', 'icon' => 'fa-truck-fast'],
        'entregado' => ['label' => 'Entregado', 'class' => 'bg-completed', 'icon' => 'fa-house-circle-check'],
        'cancelado' => ['label' => 'Cancelado', 'class' => 'bg-cancelled', 'icon' => 'fa-circle-xmark'],
    ];

    $cfg = $statusConfig[$sale->estado] ?? ['label' => $sale->estado, 'class' => 'bg-secondary', 'icon' => 'fa-question-circle'];

    $timelineSteps = [
        ['key' => 'pendiente_pago', 'label' => 'Pedido'],
        ['key' => 'pagado', 'label' => 'Confirmado'],
        ['key' => 'preparando', 'label' => 'Empacando'],
        ['key' => 'enviado', 'label' => 'En Camino'],
        ['key' => 'entregado', 'label' => 'Entregado']
    ];

    function getDStepStatus($orderStatus, $stepKey)
    {
        $statusOrder = ['pendiente_pago' => 0, 'pendiente' => 0, 'pagado' => 1, 'preparando' => 2, 'enviado' => 3, 'entregado' => 4];
        $currentOrder = $statusOrder[$orderStatus] ?? -1;
        $stepOrder = $statusOrder[$stepKey] ?? 0;

        if ($currentOrder > $stepOrder)
            return 'done';
        if ($currentOrder === $stepOrder)
            return 'active';
        return 'future';
    }
@endphp

@section('content')
    <div class="detail-page">
        <div class="container detail-container">

            {{-- TOP NAVIGATION --}}
            <div class="top-nav-pro">
                <a href="{{ route('historial.index') }}" class="btn-back-pro">
                    <i class="fas fa-arrow-left"></i> Volver a mis compras
                </a>
                @if($sale->numero_boleta)
                    <a href="{{ route('checkout.boleta', $sale) }}" target="_blank" class="btn-back-pro text-dark">
                        <i class="fas fa-file-invoice"></i> Ver Boleta
                    </a>
                @endif
            </div>

            {{-- MAIN CONTENT CARD --}}
            <div class="order-detail-card">

                {{-- Header --}}
                <div class="detail-header-pro">
                    <div class="order-label">Resumen del Pedido</div>
                    <h2>{{ $sale->numero_boleta ?: 'Pedido #' . str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</h2>
                    <div class="order-date">Realizado el {{ $sale->date->format('d \d\e F, Y \a \l\a\s H:i') }}</div>

                    <div class="status-badge-detail {{ $cfg['class'] }}">
                        <i class="fas {{ $cfg['icon'] }} me-2"></i> {{ $cfg['label'] }}
                    </div>
                </div>

                {{-- Tracking --}}
                @if($sale->estado !== 'cancelado')
                    <div class="section-pro">
                        <div class="section-title-pro"><i class="fas fa-truck-fast"></i> Seguimiento del Pedido</div>
                        <div class="tracking-detail-pro">
                            @foreach($timelineSteps as $step)
                                @php $status = getDStepStatus($sale->estado, $step['key']); @endphp
                                <div class="td-step {{ $status }}">
                                    <div class="td-dot">
                                        @if($status === 'done') <i class="fas fa-check"></i> @endif
                                    </div>
                                    <div class="td-label">{{ $step['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Products --}}
                <div class="section-pro">
                    <div class="section-title-pro"><i class="fas fa-basket-shopping"></i> Productos en tu Pedido</div>
                    <div class="order-products-list-pro">
                        @foreach($sale->details as $detail)
                            <div class="product-row-pro">
                                <div class="product-img-detail">
                                    @if($detail->product?->image)
                                        <img src="{{ asset('storage/' . $detail->product->image) }}"
                                            alt="{{ $detail->product->name }}">
                                    @else
                                        <div
                                            class="bg-secondary-subtle h-100 d-flex align-items-center justify-content-center text-secondary">
                                            <i class="fas fa-image fs-3"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="product-info-pro">
                                    <h4>{{ $detail->product?->name }}</h4>
                                    <div>
                                        @if($detail->talla)
                                            <span class="product-variant-badge">Talla: {{ $detail->talla->nombre }}</span>
                                        @endif
                                        @if($detail->color)
                                            <span class="product-variant-badge">Color: {{ $detail->color->name }}</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted mt-2">Cantidad: {{ (int) $detail->quantity }}</div>
                                </div>
                                <div class="product-price-pro">
                                    <div class="total">S/ {{ number_format($detail->subtotal, 2) }}</div>
                                    <div class="sub">PU: S/ {{ number_format($detail->unit_price, 2) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Info Blocks --}}
                <div class="info-grid-pro">
                    <div class="info-block-pro">
                        <h5>Información de Pago</h5>
                        <div class="info-content-pro">
                            @if($sale->payments->isNotEmpty())
                                @foreach($sale->payments as $payment)
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="fas fa-credit-card text-muted"></i>
                                        <strong>{{ $payment->paymentMethod?->name }}</strong>
                                    </div>
                                @endforeach
                                <div class="small text-muted">Transacción verificada de forma segura.</div>
                            @else
                                <div class="text-warning small"><i class="fas fa-clock me-1"></i> Pendiente de verificación de
                                    pago.</div>
                            @endif
                        </div>
                    </div>
                    <div class="info-block-pro">
                        <h5>Facturación</h5>
                        <div class="info-content-pro">
                            <div class="mb-1">Comprobante:
                                <strong>{{ $sale->canal_venta === 'ONLINE' ? 'Boleta Electrónica' : 'Boleta de Tienda' }}</strong>
                            </div>
                            <div class="mb-1">Cliente: <strong>{{ Auth::user()->name }}</strong></div>
                            <div>Correo: <span class="text-muted">{{ Auth::user()->email }}</span></div>
                        </div>
                    </div>
                </div>

                {{-- Totals --}}
                <div class="totals-section-pro">
                    <div class="total-row-pro">
                        <span class="text-muted">Subtotal</span>
                        <span>S/ {{ number_format($sale->total / 1.18, 2) }}</span>
                    </div>
                    <div class="total-row-pro">
                        <span class="text-muted">IGV (18%)</span>
                        <span>S/ {{ number_format($sale->total - ($sale->total / 1.18), 2) }}</span>
                    </div>
                    <div class="total-row-pro grand-total">
                        <span>Total Pagado</span>
                        <span>S/ {{ number_format($sale->total, 2) }}</span>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-5 d-flex flex-column flex-md-row gap-3">
                        <form action="{{ route('historial.repeat', $sale) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit"
                                class="btn-premium-action btn-view-detail w-100 py-3 justify-content-center">
                                <i class="fas fa-redo"></i> Volver a pedir estos productos
                            </button>
                        </form>
                        <a href="{{ route('shop.index') }}"
                            class="btn-premium-action btn-repeat-pro py-3 px-5 justify-content-center">
                            Seguir navegando
                        </a>
                    </div>
                </div>

            </div>

            {{-- Help Footer --}}
            <div class="text-center mt-5">
                <div class="text-muted small">¿Necesitas ayuda con este pedido? <a href="#"
                        class="text-dark fw-bold">Contactar Soporte Premium</a></div>
            </div>

        </div>
    </div>
@endsection