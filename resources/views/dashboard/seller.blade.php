@extends('layouts.admin')

@section('title', 'Panel de Vendedor')

@push('styles')
    <style>
        :root {
            --saas-primary: #3b82f6;
            --saas-secondary: #6366f1;
            --saas-success: #10b981;
            --saas-warning: #f59e0b;
            --saas-danger: #ef4444;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        }

        .dashboard-wrapper {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            padding-bottom: 3rem;
        }

        .saas-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: all 0.2s ease;
            overflow: hidden;
        }

        .saas-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--card-shadow-hover);
        }

        .hero-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 16px;
            padding: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-gradient::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.15);
            filter: blur(40px);
            pointer-events: none;
            z-index: 0;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .btn-pos-quick {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
        }

        .btn-pos-quick:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 12px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .live-badge {
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #3b82f6;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        .status-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .method-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .sync-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            border-radius: 12px;
        }

        .live-sale-toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #0f172a;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            transform: translateY(100px);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0;
            pointer-events: none;
            border-left: 4px solid #10b981;
        }

        .live-sale-toast.show {
            transform: translateY(0);
            opacity: 1;
            pointer-events: all;
        }
    </style>
@endpush
@section('content')
    <div class="dashboard-wrapper">
        <div class="container-fluid py-4 px-lg-5">

            <!-- Welcome & Fast Action -->
            <div
                class="hero-gradient mb-5 shadow-lg d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                <div>
                    <h1 class="fw-bold mb-2">¡Hola, {{ Auth::user()->name }}! 👋</h1>
                    <p class="opacity-75 mb-0 fs-5">Tienes un gran día para vender hoy. ¡A por ello!</p>
                </div>
                <div class="d-flex flex-wrap gap-2" style="position: relative; z-index: 5;">
                    <a href="{{ route('pos.index') }}" class="btn-pos-quick">
                        <i class="fas fa-plus-circle"></i> Nueva Venta
                    </a>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#corteDiarioModal"
                        class="btn btn-light border-0 fw-bold rounded-3 px-3">
                        <i class="fas fa-file-download"></i> Corte Diario
                    </button>
                </div>
            </div>

            <!-- Metric Cards -->
            <div class="row g-4 mb-5">
                <div class="col-xl-3 col-md-6">
                    <div class="saas-card p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted fw-bold small text-uppercase letter-spacing-1">Ventas Hoy</span>
                            <div class="stat-icon bg-primary-subtle text-primary"><i class="fas fa-wallet"></i></div>
                        </div>
                        <h2 class="fw-800 mb-1" id="val-my-sales">S/ 0.00</h2>
                        <div class="d-flex align-items-center gap-2">
                            <span
                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small">S/
                                <span id="val-my-month">0.00</span> este mes</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="saas-card p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted fw-bold small text-uppercase letter-spacing-1">Transacciones</span>
                            <div class="stat-icon bg-success-subtle text-success"><i class="fas fa-receipt"></i></div>
                        </div>
                        <h2 class="fw-800 mb-1" id="val-my-transactions">0</h2>
                        <span class="text-muted small fw-medium">Operaciones realizadas hoy</span>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="saas-card p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted fw-bold small text-uppercase letter-spacing-1">Ticket Promedio</span>
                            <div class="stat-icon bg-warning-subtle text-warning"><i class="fas fa-chart-line"></i></div>
                        </div>
                        <h2 class="fw-800 mb-1" id="val-ticket-average">S/ 0.00</h2>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-warning" style="width: 65%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="saas-card p-4" data-bs-toggle="modal" data-bs-target="#clientesNuevosModal"
                        style="cursor: pointer;">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted fw-bold small text-uppercase letter-spacing-1">Clientes Nuevos</span>
                            <div class="stat-icon bg-info-subtle text-info"><i class="fas fa-user-plus"></i></div>
                        </div>
                        <h2 class="fw-800 mb-1" id="val-new-clients">0</h2>
                        <span class="text-muted small fw-medium">Captados hoy por ti <i
                                class="fas fa-external-link-alt ms-1 opacity-50"></i></span>
                    </div>
                </div>
            </div>

            <!-- Analytics Row -->
            <div class="row g-4 mb-5">
                <div class="col-lg-8">
                    <div class="saas-card p-4 position-relative" style="min-height: 450px;">
                        <div class="sync-overlay" id="daily-trend-sync">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold m-0"><i class="fas fa-chart-area text-primary me-2"></i>Tendencia de Ventas
                                (7d)</h5>
                            <div class="live-badge"><span class="live-dot"></span> REALTIME</div>
                        </div>
                        <div style="height: 350px;">
                            <canvas id="dailyTrendChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="saas-card p-4 h-100">
                        <h5 class="fw-bold mb-4">Métodos de Pago</h5>
                        <div style="height: 300px;">
                            <canvas id="paymentMethodChart"></canvas>
                        </div>
                        <div id="payment-labels-container" class="mt-4"></div>
                    </div>
                </div>
            </div>

            <!-- Latest Sales & Top Products -->
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="saas-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold m-0 text-dark">Últimas Ventas</h5>
                            <button class="btn btn-sm btn-light border fw-bold text-muted px-3 rounded-pill"
                                onclick="fetchSellerStats()">
                                <i class="fas fa-sync-alt me-1"></i> Refresh
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light-subtle">
                                    <tr class="text-muted small fw-bold">
                                        <th># ORDEN</th>
                                        <th>CLIENTE</th>
                                        <th>PAGO</th>
                                        <th>TOTAL</th>
                                        <th>ESTADO</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-sales-body">
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Cargando datos...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="saas-card p-4 h-100">
                        <h5 class="fw-bold mb-4">Mis Productos Más Vendidos</h5>
                        <div id="top-products-container" class="pe-2">
                            <!-- Bars -->
                            <p class="text-center text-muted py-5">Sincronizando...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Corte Diario (Shopify Style) -->
    <div class="modal fade" id="corteDiarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-dark">Corte Diario</h5>
                        <p class="text-muted small mb-0" id="corte-header-info">Generando reporte...</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center py-5" id="corte-loading">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Consultando servidor...</p>
                    </div>

                    <div id="corte-content" style="display: none;">
                        <!-- Summary Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="p-3 border rounded-4 bg-light">
                                    <div class="small fw-bold text-muted mb-1">VENTAS</div>
                                    <h4 class="fw-800 m-0" id="corte-total-sales">S/ 0.00</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border rounded-4 bg-light">
                                    <div class="small fw-bold text-muted mb-1">ÓRDENES</div>
                                    <h4 class="fw-800 m-0" id="corte-total-vouchers">0</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border rounded-4 bg-light">
                                    <div class="small fw-bold text-muted mb-1">TICKET PROM.</div>
                                    <h4 class="fw-800 m-0" id="corte-avg-ticket">S/ 0.00</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border rounded-4 bg-danger-subtle text-danger">
                                    <div class="small fw-bold opacity-75 mb-1">ANULADAS</div>
                                    <h4 class="fw-800 m-0" id="corte-voided">0</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Times -->
                        <div class="d-flex gap-2 mb-4">
                            <div class="badge bg-dark-subtle text-dark px-3 py-2 rounded-pill">
                                <i class="far fa-clock me-1"></i> Primera venta: <span id="corte-start-time">--:--</span>
                            </div>
                            <div class="badge bg-dark-subtle text-dark px-3 py-2 rounded-pill">
                                <i class="fas fa-history me-1"></i> Última venta: <span id="corte-end-time">--:--</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <span class="bg-primary text-white p-1 rounded me-2"
                                    style="width:24px; height:24px; display:flex; align-items:center; justify-content:center; font-size:0.7rem;">
                                    <i class="fas fa-money-bill-wave"></i>
                                </span>
                                Desglose de Ingresos Reales
                            </h6>
                            <div class="row g-3" id="corte-payments-breakdown">
                                <!-- Breakdown via JS -->
                            </div>
                        </div>

                        <div class="mb-0">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <span class="bg-secondary text-white p-1 rounded me-2"
                                    style="width:24px; height:24px; display:flex; align-items:center; justify-content:center; font-size:0.7rem;">
                                    <i class="fas fa-list-ul"></i>
                                </span>
                                Movimientos del Día
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle border-top">
                                    <thead class="bg-light">
                                        <tr class="text-muted small fw-bold">
                                            <th class="ps-3"># ORDEN</th>
                                            <th>HORA</th>
                                            <th>CLIENTE</th>
                                            <th class="text-end pe-3">MONTO</th>
                                        </tr>
                                    </thead>
                                    <tbody id="corte-sales-list"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary fw-bold px-4 rounded-3" onclick="window.print()">
                        <i class="fas fa-print me-2"></i> Imprimir Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Clientes Nuevos (Stripe Style) -->
    <div class="modal fade" id="clientesNuevosModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-users text-info me-2"></i>Clientes Nuevos de
                        Hoy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="clients-loading" class="text-center py-5">
                        <div class="spinner-border text-info" role="status"></div>
                    </div>
                    <div id="clients-list" class="list-group list-group-flush">
                        <!-- List via JS -->
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light fw-bold w-100" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let trendChart, paymentChart;

        function initCharts() {
            try {
                const trendEl = document.getElementById('dailyTrendChart');
                if (!trendEl) return;
                const trendCtx = trendEl.getContext('2d');
                const gradient = trendCtx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
                gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

                trendChart = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: [], datasets: [{
                            label: 'Monto Total', data: [],
                            borderColor: '#3b82f6', backgroundColor: gradient,
                            fill: true, tension: 0.4, borderWeight: 3,
                            pointBackgroundColor: '#fff', pointBorderColor: '#3b82f6', pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { callback: v => 'S/ ' + v } },
                            x: { grid: { display: false } }
                        }
                    }
                });

                const payEl = document.getElementById('paymentMethodChart');
                if (!payEl) return;
                const payCtx = payEl.getContext('2d');
                paymentChart = new Chart(payCtx, {
                    type: 'doughnut',
                    data: {
                        labels: [], datasets: [{
                            data: [], backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                            borderWidth: 0, hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '75%',
                        plugins: { legend: { display: false } }
                    }
                });
            } catch (e) {
                console.error('Chart init error:', e);
            }
        }

        function fetchSellerStats() {
            const syncOverlay = document.getElementById('daily-trend-sync');
            if (syncOverlay) syncOverlay.style.display = 'flex';

            fetch('{{ route("vendedor.stats") }}')
                .then(res => res.json())
                .then(data => {
                    try {
                        const safeUpdate = (id, val) => {
                            const el = document.getElementById(id);
                            if (el) el.innerText = val;
                        };

                        safeUpdate('val-my-sales', 'S/ ' + (data.mySalesToday || '0.00'));
                        safeUpdate('val-my-month', data.mySalesMonth || '0.00');
                        safeUpdate('val-my-transactions', data.myTransactionCount || '0');
                        safeUpdate('val-ticket-average', 'S/ ' + (data.ticketAverage || '0.00'));
                        safeUpdate('val-new-clients', data.newClientsToday || '0');

                        if (trendChart && data.dailyTrend) {
                            trendChart.data.labels = data.dailyTrend.labels || [];
                            trendChart.data.datasets[0].data = data.dailyTrend.data || [];
                            trendChart.update();
                        }

                        if (paymentChart && data.paymentStats) {
                            paymentChart.data.labels = data.paymentStats.labels || [];
                            paymentChart.data.datasets[0].data = data.paymentStats.data || [];
                            paymentChart.update();

                            const pContainer = document.getElementById('payment-labels-container');
                            if (pContainer) {
                                const colors = paymentChart.data.datasets[0].backgroundColor;
                                pContainer.innerHTML = (data.paymentStats.labels || []).map((l, i) => `
                                            <div class="d-flex justify-content-between mb-2 small fw-medium">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span style="width:10px; height:10px; border-radius:3px; background:${colors[i % colors.length]}"></span>
                                                    ${l}
                                                </div>
                                                <span>S/ ${parseFloat(data.paymentStats.data[i] || 0).toFixed(2)}</span>
                                            </div>
                                        `).join('');
                            }
                        }

                        const tbody = document.getElementById('recent-sales-body');
                        if (tbody) {
                            tbody.innerHTML = (data.recentSales || []).map(s => {
                                const statusClass = s.estado === 'completado' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning';
                                const method = (s.method || '').toLowerCase();
                                const icon = method.includes('yape') ? 'Y' : (method.includes('tarjeta') ? '💳' : (method.includes('plin') ? 'P' : '💵'));
                                return `
                                            <tr>
                                                <td class="fw-bold text-slate-800">#${s.id}</td>
                                                <td>
                                                    <div class="fw-bold">${s.client || 'General'}</div>
                                                    <div class="text-muted" style="font-size:0.65rem">${s.time || ''}</div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="method-icon bg-light text-dark border">${icon}</div>
                                                        <span class="small fw-bold">${s.method || 'N/A'}</span>
                                                    </div>
                                                </td>
                                                <td class="fw-800 text-dark">S/ ${s.total || '0.00'}</td>
                                                <td><span class="status-badge ${statusClass}">${(s.estado || 'N/A').toUpperCase()}</span></td>
                                            </tr>
                                        `;
                            }).join('') || '<tr><td colspan="5" class="text-center py-4">Sin ventas hoy</td></tr>';
                        }

                        const topContainer = document.getElementById('top-products-container');
                        if (topContainer) {
                            const topProducts = data.topProducts || [];
                            if (topProducts.length === 0) {
                                topContainer.innerHTML = '<p class="text-center text-muted py-5">Sin productos vendidos aún</p>';
                            } else {
                                const maxQty = Math.max(...topProducts.map(p => parseFloat(p.total_qty)), 1);
                                topContainer.innerHTML = topProducts.map(p => `
                                            <div class="mb-4">
                                                <div class="d-flex justify-content-between mb-1 small fw-bold text-dark">
                                                    <span>${p.name}</span>
                                                    <span class="text-primary">${p.total_qty} u.</span>
                                                </div>
                                                <div class="progress" style="height: 8px; background: #e2e8f0; border-radius: 4px;">
                                                    <div class="progress-bar rounded-pill" style="width: ${(parseFloat(p.total_qty) / maxQty) * 100}%; background: linear-gradient(90deg, #3b82f6, #6366f1);"></div>
                                                </div>
                                            </div>
                                        `).join('');
                            }
                        }
                    } catch (err) { console.error(err); }
                    finally { if (syncOverlay) syncOverlay.style.display = 'none'; }
                });
        }

        function showCorteDiario() {
            const modalEl = document.getElementById('corteDiarioModal');
            if (!modalEl) return;
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            const content = document.getElementById('corte-content');
            const loading = document.getElementById('corte-loading');

            content.style.display = 'none';
            loading.style.display = 'block';

            fetch('{{ route("vendedor.corte-diario") }}')
                .then(res => res.json())
                .then(res => {
                    const data = res.data;
                    document.getElementById('corte-header-info').innerText = `Reporte de ${data.seller} • ${data.date}`;
                    document.getElementById('corte-total-sales').innerText = 'S/ ' + data.total_sales;
                    document.getElementById('corte-total-vouchers').innerText = data.total_transactions;
                    document.getElementById('corte-avg-ticket').innerText = 'S/ ' + data.ticket_average;
                    document.getElementById('corte-voided').innerText = data.voided_sales;
                    document.getElementById('corte-start-time').innerText = data.first_order_time;
                    document.getElementById('corte-end-time').innerText = data.last_order_time;

                    const pDiv = document.getElementById('corte-payments-breakdown');
                    pDiv.innerHTML = (data.payment_breakdown || []).map(p => `
                                <div class="col-6 col-md-4">
                                    <div class="p-3 border rounded-3 text-center bg-white shadow-sm">
                                        <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size:0.6rem;">${p.name}</div>
                                        <div class="fw-bold fs-5 text-primary">S/ ${parseFloat(p.total || 0).toFixed(2)}</div>
                                    </div>
                                </div>
                            `).join('') || '<div class="col-12 text-center text-muted">No hay cobros registrados</div>';

                    const sTbody = document.getElementById('corte-sales-list');
                    sTbody.innerHTML = (data.recent_sales || []).map(s => `
                                <tr>
                                    <td class="ps-3 fw-bold">#${s.id}</td>
                                    <td>${s.hour}</td>
                                    <td>${s.client}</td>
                                    <td class="text-end pe-3">
                                        <span class="fw-bold">S/ ${s.total}</span>
                                        <div class="small ${s.estado === 'cancelado' ? 'text-danger' : 'text-success'}" style="font-size:0.6rem">${s.estado.toUpperCase()}</div>
                                    </td>
                                </tr>
                            `).join('') || '<tr><td colspan="4" class="text-center py-3 text-muted">Sin movimientos</td></tr>';

                    loading.style.display = 'none';
                    content.style.display = 'block';
                }).catch(err => {
                    loading.innerHTML = '<p class="text-danger py-4">Error al cargar el corte diario.</p>';
                    console.error(err);
                });
        }

        function showClientesNuevos() {
            const modalEl = document.getElementById('clientesNuevosModal');
            if (!modalEl) return;
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            const list = document.getElementById('clients-list');
            const loading = document.getElementById('clients-loading');

            list.innerHTML = '';
            loading.style.display = 'block';

            fetch('{{ route("vendedor.clientes-nuevos") }}')
                .then(res => res.json())
                .then(data => {
                    loading.style.display = 'none';
                    if (data.list && data.list.length > 0) {
                        list.innerHTML = data.list.map(c => `
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                        <div>
                                            <div class="fw-bold text-dark">${c.name}</div>
                                            <div class="small text-muted"><i class="fas fa-phone-alt me-1"></i> ${c.phone}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="badge bg-info-subtle text-info rounded-pill px-3">${c.time}</div>
                                        </div>
                                    </div>
                                `).join('');
                    } else {
                        list.innerHTML = '<div class="text-center py-5 text-muted">No hay clientes captados hoy.</div>';
                    }
                }).catch(err => {
                    loading.innerHTML = '<p class="text-danger p-4">Error al cargar clientes.</p>';
                });
        }

        function initRealtime() {
            if (typeof Echo !== 'undefined') {
                const userId = {{ auth()->id() ?: 'null' }};
                if (!userId) return;

                const privateChannel = Echo.private(`vendedor.${userId}`);

                // Real-time Sales
                privateChannel.listen('.venta.realizada', (data) => {
                    console.log('Nueva Venta:', data);
                    showLiveToast(`Venta #${data.sale.id} - S/ ${data.sale.total}`);
                    fetchSellerStats();
                    // If Corte Diario modal is open, refresh it too
                    const corteModal = document.getElementById('corteDiarioModal');
                    if (corteModal && corteModal.classList.contains('show')) {
                        showCorteDiario();
                    }
                });

                // Real-time Clients
                privateChannel.listen('.cliente.registrado', (data) => {
                    console.log('Nuevo Cliente:', data);
                    showLiveToast(`Cliente: ${data.client.name}`);
                    fetchSellerStats();
                    // If Clientes modal is open, refresh it
                    const clientsModal = document.getElementById('clientesNuevosModal');
                    if (clientsModal && clientsModal.classList.contains('show')) {
                        showClientesNuevos();
                    }
                });
            } else {
                setInterval(fetchSellerStats, 30000);
            }
        }

        function showLiveToast(message) {
            const toast = document.createElement('div');
            toast.className = 'live-sale-toast';
            toast.innerHTML = `
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon bg-success text-white shadow-sm"><i class="fas fa-bolt"></i></div>
                            <div>
                                <div class="fw-bold">Actividad en Vivo</div>
                                <div class="small opacity-75">${message}</div>
                            </div>
                        </div>
                    `;
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            try {
                initCharts();
                fetchSellerStats();
                initRealtime();

                // Automated fetching when modals show
                const corteModal = document.getElementById('corteDiarioModal');
                if (corteModal) {
                    corteModal.addEventListener('shown.bs.modal', showCorteDiario);
                }

                const clientsModal = document.getElementById('clientesNuevosModal');
                if (clientsModal) {
                    clientsModal.addEventListener('shown.bs.modal', showClientesNuevos);
                }
            } catch (err) {
                console.error('Dashboard init error:', err);
            }
        });
    </script>
@endpush