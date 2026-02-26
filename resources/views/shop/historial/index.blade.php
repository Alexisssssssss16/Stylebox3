@extends('layouts.shop')

@section('title', 'Mis Compras — StyleBox Premium')

@push('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --premium-bg: #f8fafc;
            --premium-surface: #ffffff;
            --premium-accent: #c9a84c;
            /* Gold accent */
            --premium-text: #0f172a;
            --premium-text-muted: #64748b;
            --premium-border: #e2e8f0;
            --premium-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --premium-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        .historial-page {
            font-family: 'Inter', sans-serif;
            background: var(--premium-bg);
            min-height: 100vh;
            padding-bottom: 5rem;
        }

        /* ── PREMIUM HEADER ── */
        .premium-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 3.5rem 0;
            margin-bottom: -2.5rem;
            position: relative;
            overflow: hidden;
        }

        .premium-hero::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 86c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm66 3c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm-46-45c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm26 18c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm16-34c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM21 45c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm54 2c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM25 15c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM71 19c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM3 31c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM7 63c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM10 40c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM89 50c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM5 80c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm60-63c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm15 11c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm5 50c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm21-4c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-4-15c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-32-15c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-6-24c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm42 5c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm-18 1c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zM19 75c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1zm41 1c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1z' fill='%236366f1' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.5;
        }

        .header-container {
            position: relative;
            z-index: 10;
        }

        .premium-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .premium-hero .stats-row {
            display: flex;
            gap: 2.5rem;
            margin-top: 2rem;
        }

        .stat-group {
            border-left: 2.5px solid var(--premium-accent);
            padding-left: 1rem;
        }

        .stat-group .label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            margin-bottom: 0.25rem;
        }

        .stat-group .value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
        }

        /* ── FILTER CHIPS ── */
        .filter-section {
            background: var(--premium-surface);
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: var(--premium-shadow);
            margin-bottom: 2rem;
            position: relative;
            z-index: 20;
        }

        .chip-container {
            display: flex;
            gap: 0.75rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .chip-container::-webkit-scrollbar {
            display: none;
        }

        .status-chip {
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1.5px solid transparent;
        }

        .status-chip:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .status-chip.active {
            background: #1e293b;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(30, 41, 59, 0.2);
        }

        /* ── ORDER CARDS ── */
        .order-card-premium {
            background: var(--premium-surface);
            border-radius: 1.5rem;
            border: 1px solid var(--premium-border);
            margin-bottom: 1.75rem;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: var(--premium-shadow);
        }

        .order-card-premium:hover {
            transform: translateY(-4px);
            box-shadow: var(--premium-shadow-lg);
            border-color: #cbd5e1;
        }

        /* Card Header */
        .order-header-pro {
            background: #f8fafc;
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid var(--premium-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-meta {
            display: flex;
            gap: 2rem;
        }

        .meta-item .label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .meta-item .value {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--premium-text);
        }

        /* ID and Total prominent */
        .order-id-badge {
            background: #ffffff;
            border: 1.5px solid var(--premium-border);
            padding: 4px 12px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.85rem;
            color: #1e293b;
        }

        /* Status Badge */
        .badge-status-pro {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .bg-pending {
            background: #fef9c3;
            color: #854d0e;
        }

        .bg-completed {
            background: #dcfce7;
            color: #166534;
        }

        .bg-shipping {
            background: #dbeafe;
            color: #1e40af;
        }

        .bg-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Card Body */
        .order-body-pro {
            padding: 1.75rem;
        }

        /* Tracking Timeline */
        .tracking-bar-pro {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2.5rem;
            position: relative;
            padding: 0 1rem;
        }

        .tracking-bar-pro::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 2rem;
            right: 2rem;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }

        .t-step {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 80px;
        }

        .t-dot {
            width: 24px;
            height: 24px;
            background: #ffffff;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .t-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: #94a3b8;
            margin-top: 8px;
            text-transform: uppercase;
            text-align: center;
        }

        /* Active/Done steps */
        .t-step.done .t-dot {
            background: #10b981;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
        }

        .t-step.done .t-dot i {
            color: white;
            display: block;
            font-size: 0.6rem;
        }

        .t-step.done .t-label {
            color: #1e293b;
        }

        .t-step.active .t-dot {
            background: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            animation: pulse-pro 2s infinite;
        }

        .t-step.active .t-label {
            color: #6366f1;
        }

        @keyframes pulse-pro {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Products Row */
        .order-items-scroll {
            display: flex;
            gap: 1.25rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .product-item-pro {
            display: flex;
            flex-direction: column;
            min-width: 100px;
            max-width: 120px;
            text-decoration: none;
        }

        .product-img-wrap {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--premium-border);
            margin-bottom: 0.5rem;
            background: #f8fafc;
        }

        .product-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-item-pro:hover img {
            transform: scale(1.1);
        }

        .p-name {
            font-size: 0.75rem;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .p-meta {
            font-size: 0.65rem;
            color: #64748b;
        }

        /* Card Footer Actions */
        .order-footer-pro {
            padding: 1.25rem 1.75rem;
            border-top: 1px solid var(--premium-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-premium-action {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-view-detail {
            background: #1e293b;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(30, 41, 59, 0.2);
        }

        .btn-view-detail:hover {
            background: #0f172a;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(30, 41, 59, 0.25);
            color: #fff;
        }

        .btn-repeat-pro {
            background: #ffffff;
            border: 2px solid var(--premium-border);
            color: #475569;
        }

        .btn-repeat-pro:hover {
            border-color: #1e293b;
            color: #1e293b;
            transform: translateY(-2px);
        }

        /* Pagination Style */
        .custom-pagination-pro .pagination {
            gap: 8px;
        }

        .custom-pagination-pro .page-link {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50% !important;
            border: none;
            background: #fff;
            color: #475569;
            font-weight: 700;
            box-shadow: var(--premium-shadow);
        }

        .custom-pagination-pro .page-item.active .page-link {
            background: #1e293b;
            color: #fff;
        }

        /* Mobile specific */
        @media (max-width: 768px) {
            .premium-hero h1 {
                font-size: 1.75rem;
            }

            .premium-hero .stats-row {
                gap: 1rem;
                flex-wrap: wrap;
            }

            .stat-group {
                padding-left: 0.75rem;
            }

            .stat-group .value {
                font-size: 1.2rem;
            }

            .order-meta {
                gap: 1rem;
            }

            .order-header-pro {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .tracking-bar-pro {
                display: none;
            }

            .order-footer-pro {
                flex-direction: column;
                gap: 1rem;
            }

            .order-footer-pro .btn-premium-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

@php
    $totalOrders = $purchases->total();
    $totalSpent = Auth::user()->purchases()->where('estado', '!=', 'cancelado')->sum('total');
    $deliveredCount = Auth::user()->purchases()->where('estado', 'entregado')->count();

    $statusConfig = [
        'pendiente_pago' => ['label' => 'Por Pagar', 'class' => 'bg-pending', 'icon' => 'fa-wallet'],
        'pendiente' => ['label' => 'Pendiente', 'class' => 'bg-pending', 'icon' => 'fa-clock'],
        'pagado' => ['label' => 'Confirmado', 'class' => 'bg-completed', 'icon' => 'fa-check-circle'],
        'preparando' => ['label' => 'Empacando', 'class' => 'bg-shipping', 'icon' => 'fa-box-open'],
        'enviado' => ['label' => 'En Camino', 'class' => 'bg-shipping', 'icon' => 'fa-truck-fast'],
        'entregado' => ['label' => 'Entregado', 'class' => 'bg-completed', 'icon' => 'fa-house-circle-check'],
        'cancelado' => ['label' => 'Cancelado', 'class' => 'bg-cancelled', 'icon' => 'fa-circle-xmark'],
    ];

    $timelineSteps = [
        ['key' => 'pendiente_pago', 'label' => 'Pedido'],
        ['key' => 'pagado', 'label' => 'Confirmado'],
        ['key' => 'preparando', 'label' => 'Empacando'],
        ['key' => 'enviado', 'label' => 'En Camino'],
        ['key' => 'entregado', 'label' => 'Entregado']
    ];

    function getStepStatus($orderStatus, $stepKey, $steps)
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
    <div class="historial-page">
        {{-- 1. PREMIUM HERO --}}
        <div class="premium-hero">
            <div class="container header-container text-center text-md-start">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div>
                        <h1>Mis Compras</h1>
                        <p class="text-white opacity-75 mb-0">Gestiona tus pedidos y sigue tus envíos en tiempo real.</p>
                    </div>
                    <div class="mt-4 mt-md-0">
                        <a href="{{ route('shop.index') }}"
                            class="btn-premium-action btn-view-detail bg-light text-dark py-2 px-4 shadow-sm"
                            style="background: rgba(255,255,255,0.1) !important; color:#fff !important; border: 1px solid rgba(255,255,255,0.2);">
                            <i class="fas fa-shopping-bag me-2"></i> Seguir Comprando
                        </a>
                    </div>
                </div>

                <div class="stats-row justify-content-center justify-content-md-start">
                    <div class="stat-group">
                        <div class="label">Total Pedidos</div>
                        <div class="value">{{ $totalOrders }}</div>
                    </div>
                    <div class="stat-group">
                        <div class="label">Total Gastado</div>
                        <div class="value">S/ {{ number_format($totalSpent, 2) }}</div>
                    </div>
                    <div class="stat-group">
                        <div class="label">Entregados</div>
                        <div class="value">{{ $deliveredCount }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-n4">
            {{-- 2. FILTERS --}}
            <div class="filter-section">
                <div class="chip-container">
                    <a href="{{ route('historial.index') }}"
                        class="status-chip {{ !request('estado') ? 'active' : '' }}">Todos</a>
                    <a href="{{ route('historial.index', ['estado' => 'pendiente_pago']) }}"
                        class="status-chip {{ request('estado') == 'pendiente_pago' ? 'active' : '' }}">Pendientes</a>
                    <a href="{{ route('historial.index', ['estado' => 'pagado']) }}"
                        class="status-chip {{ request('estado') == 'pagado' ? 'active' : '' }}">Confirmados</a>
                    <a href="{{ route('historial.index', ['estado' => 'enviado']) }}"
                        class="status-chip {{ request('estado') == 'enviado' ? 'active' : '' }}">En Camino</a>
                    <a href="{{ route('historial.index', ['estado' => 'entregado']) }}"
                        class="status-chip {{ request('estado') == 'entregado' ? 'active' : '' }}">Completados</a>
                    <a href="{{ route('historial.index', ['estado' => 'cancelado']) }}"
                        class="status-chip {{ request('estado') == 'cancelado' ? 'active' : '' }}">Cancelados</a>
                </div>
            </div>

            {{-- 3. ORDERS LIST --}}
            <div class="orders-wrapper">
                @forelse($purchases as $sale)
                    @php
                        $cfg = $statusConfig[$sale->estado] ?? $statusConfig['pendiente'];
                        $isCancelled = $sale->estado === 'cancelado';
                    @endphp
                    <div class="order-card-premium">
                        <div class="order-header-pro">
                            <div class="order-meta">
                                <div class="meta-item">
                                    <div class="label">Pedido</div>
                                    <div class="order-id-badge">
                                        {{ $sale->numero_boleta ?: '#' . str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</div>
                                </div>
                                <div class="meta-item d-none d-sm-block">
                                    <div class="label">Fecha</div>
                                    <div class="value">{{ $sale->date->format('d M, Y') }}</div>
                                </div>
                                <div class="meta-item">
                                    <div class="label">Total</div>
                                    <div class="value">S/ {{ number_format($sale->total, 2) }}</div>
                                </div>
                            </div>
                            <div class="badge-status-pro {{ $cfg['class'] }}">
                                <i class="fas {{ $cfg['icon'] }}"></i>
                                {{ $cfg['label'] }}
                            </div>
                        </div>

                        <div class="order-body-pro">
                            {{-- Tracking Bar --}}
                            @if(!$isCancelled)
                                <div class="tracking-bar-pro">
                                    @foreach($timelineSteps as $step)
                                        @php $status = getStepStatus($sale->estado, $step['key'], $timelineSteps); @endphp
                                        <div class="t-step {{ $status }}">
                                            <div class="t-dot">
                                                @if($status === 'done')<i class="fas fa-check"></i>@endif
                                            </div>
                                            <div class="t-label">{{ $step['label'] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-light border-0 py-2 small mb-4 text-center">
                                    <i class="fas fa-exclamation-triangle me-2 text-danger"></i> Este pedido fue cancelado.
                                </div>
                            @endif

                            {{-- Order Content Preview --}}
                            <div class="order-items-scroll">
                                @foreach($sale->details as $detail)
                                    <a href="{{ route('historial.show', $sale) }}" class="product-item-pro">
                                        <div class="product-img-wrap">
                                            @if($detail->product?->image)
                                                <img src="{{ asset('storage/' . $detail->product->image) }}"
                                                    alt="{{ $detail->product->name }}">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center h-100 bg-secondary-subtle">
                                                    <i class="fas fa-tshirt text-secondary"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-name">{{ $detail->product?->name }}</div>
                                        <div class="p-meta">Cant: {{ (int) $detail->quantity }}</div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="order-footer-pro">
                            <div class="text-muted small">
                                Método de Pago:
                                <strong>{{ $sale->payments->first()?->paymentMethod?->name ?? 'Por definir' }}</strong>
                            </div>
                            <div class="d-flex gap-2">
                                <form action="{{ route('historial.repeat', $sale) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-premium-action btn-repeat-pro">
                                        <i class="fas fa-redo"></i> Volver a Comprar
                                    </button>
                                </form>
                                <a href="{{ route('historial.show', $sale) }}" class="btn-premium-action btn-view-detail">
                                    Ver Detalle <i class="fas fa-chevron-right ms-2 fs-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-cart-2130356-1800917.png"
                            alt="No orders" style="max-width: 200px;" class="mb-4 opacity-75">
                        <h3 class="fw-bold">No tienes pedidos aún</h3>
                        <p class="text-muted">Parece que aún no has realizado ninguna compra en nuestra tienda.</p>
                        <a href="{{ route('shop.index') }}" class="btn-premium-action btn-view-detail mt-3">
                            Comenzar a Comprar
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
            <div class="d-flex justify-content-center custom-pagination-pro mt-4">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>
@endsection