@extends('layouts.admin')

@section('title', 'Dashboard | StyleBox Pro')

@push('styles')
        <style>
            :root {
                --saas-primary: #6366f1;
                --saas-success: #10b981;
                --saas-danger: #f43f5e;
                --saas-warning: #f59e0b;
                --saas-info: #3b82f6;
                --card-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
                --card-shadow-hover: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            }

            .dashboard-wrapper {
                font-family: 'Inter', sans-serif;
                background: #f8fafc;
                min-height: 100vh;
                padding-bottom: 3rem;
            }

            /* SaaS Cards */
            .saas-card {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                box-shadow: var(--card-shadow);
                transition: all 0.2s ease;
                overflow: hidden;
            }

            .saas-card:hover {
                box-shadow: var(--card-shadow-hover);
                transform: translateY(-2px);
            }

            .stat-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.75rem;
            }

            .stat-icon {
                width: 42px;
                height: 42px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
            }

            .stat-label {
                font-size: 0.75rem;
                font-weight: 600;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .stat-value {
                font-size: 1.5rem;
                font-weight: 800;
                color: #0f172a;
                margin: 0.5rem 0;
            }

            @media (min-width: 1200px) {
                .stat-value {
                    font-size: 1.75rem;
                }
            }

            /* Sparklines */
            .sparkline-container {
                height: 40px;
                width: 100%;
                margin-top: 1rem;
            }

            /* Live Badge */
            .live-status {
                padding: 0.4rem 0.6rem;
                border-radius: 8px;
                font-size: 0.7rem;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 0.4rem;
                background: #ecfdf5;
                color: #065f46;
                border: 1px solid #d1fae5;
                white-space: nowrap;
            }

            .live-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #10b981;
                position: relative;
            }

            .live-dot::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                border-radius: 50%;
                background: inherit;
                animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
            }

            @keyframes ping {

                75%,
                100% {
                    transform: scale(3.5);
                    opacity: 0;
                }
            }

            /* Activity & Alerts */
            .activity-item {
                padding: 0.85rem;
                border-left: 2px solid #e2e8f0;
                position: relative;
                margin-left: 10px;
                transition: background 0.2s ease;
            }

            .activity-item:hover {
                background: #f1f5f9;
                border-radius: 0 8px 8px 0;
            }

            .activity-item::before {
                content: '';
                position: absolute;
                left: -6px;
                top: 1.1rem;
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: #fff;
                border: 2px solid var(--saas-primary);
            }

            .alert-pill {
                padding: 0.75rem 1rem;
                border-radius: 10px;
                margin-bottom: 0.75rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                text-decoration: none;
                transition: transform 0.2s ease;
            }

            .alert-pill:hover {
                transform: translateX(5px);
            }

            /* Period Filter */
            .filter-btn {
                background: #fff;
                border: 1px solid #e2e8f0;
                padding: 0.35rem 0.7rem;
                font-size: 0.8rem;
                font-weight: 600;
                color: #64748b;
                border-radius: 6px;
                transition: all 0.2s ease;
            }

            .filter-btn.active {
                background: var(--saas-primary);
                color: #fff;
                border-color: var(--saas-primary);
            }

            .sync-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.6);
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 50;
            }

            /* Responsive Adjustments */
            @media (max-width: 768px) {
                .dashboard-wrapper .container-fluid {
                    padding: 1rem !important;
                }

                .saas-card {
                    padding: 1.25rem !important;
                }

                .saas-header-stack {
                    flex-direction: column !important;
                    align-items: flex-start !important;
                }

                .saas-header-controls {
                    width: 100%;
                    justify-content: space-between;
                }

                .chart-main-container {
                    height: 280px !important;
                }
            }

            @media (max-width: 576px) {
                .saas-header-controls {
                    flex-wrap: wrap;
                    gap: 0.5rem !important;
                }

                .btn-group {
                    width: 100%;
                }

                .filter-btn {
                    flex: 1;
                    text-align: center;
                }

                .stat-value {
                    font-size: 1.35rem;
                }
            }
        </style>
    @endpush

@section('content')
    <div class="dashboard-wrapper">
        <div class="container-fluid py-4 px-lg-5">
            <!-- Dashboard Header -->
            <div class="d-flex justify-content-between align-items-center gap-3 mb-4 mb-md-5 saas-header-stack">
                <div>
                    <h2 class="fw-bold text-slate-900 mb-1" style="font-size: calc(1.3rem + 0.6vw);">Dashboard Overview</h2>
                    <p class="text-muted small fw-medium mb-0">Control total y métricas en tiempo real.</p>
                </div>
                <div class="d-flex align-items-center gap-2 gap-md-3 saas-header-controls">
                    <div class="live-status" id="status-badge">
                        <span class="live-dot"></span>
                        <span id="status-text">LIVE</span>
                    </div>
                    <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                        <button class="filter-btn active" data-range="24h">24h</button>
                        <button class="filter-btn" data-range="7d">7d</button>
                        <button class="filter-btn" data-range="30d">30d</button>
                        <button class="filter-btn" data-range="6m">6m</button>
                    </div>
                </div>
            </div>

            <!-- Metric Cards -->
            <div class="row g-3 g-lg-4 mb-4 mb-md-5">
                <!-- Income Card -->
                <div class="col-xl-3 col-sm-6">
                    <div class="saas-card p-4">
                        <div class="stat-card-header">
                            <span class="stat-label">Ingresos Diarios</span>
                            <div class="stat-icon bg-info-subtle text-info d-none d-sm-flex">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="val-income">S/ 0.00</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill"
                                style="font-size: 0.65rem;">Hoy</span>
                            <span class="text-muted fw-medium" style="font-size: 0.7rem;">Tendencia</span>
                        </div>
                        <div class="sparkline-container">
                            <canvas id="sparklineSales"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Transactions Card -->
                <div class="col-xl-3 col-sm-6">
                    <div class="saas-card p-4">
                        <div class="stat-card-header">
                            <span class="stat-label">Transacciones</span>
                            <div class="stat-icon bg-primary-subtle text-primary d-none d-sm-flex">
                                <i class="fas fa-receipt"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="val-transactions">0</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill"
                                style="font-size: 0.65rem;">Periodo</span>
                            <span class="text-muted fw-medium" style="font-size: 0.7rem;">Volumen</span>
                        </div>
                        <div class="sparkline-container">
                            <canvas id="sparklineTransactions"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Users Card -->
                <div class="col-xl-3 col-sm-6">
                    <div class="saas-card p-4">
                        <div class="stat-card-header">
                            <span class="stat-label">Usuarios</span>
                            <div class="stat-icon bg-success-subtle text-success d-none d-sm-flex">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="val-users">0</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill"
                                style="font-size: 0.65rem;">Total</span>
                            <span class="text-muted fw-medium" style="font-size: 0.7rem;">Registros</span>
                        </div>
                        <div class="mt-4 pt-2">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Alert Card -->
                <div class="col-xl-3 col-sm-6">
                    <div class="saas-card p-4">
                        <div class="stat-card-header">
                            <span class="stat-label">Stock Crítico</span>
                            <div class="stat-icon bg-danger-subtle text-danger d-none d-sm-flex">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="stat-value text-danger" id="val-low-stock">0</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill"
                                style="font-size: 0.65rem;">Alertas</span>
                            <span class="text-muted fw-medium" style="font-size: 0.7rem;">Acción</span>
                        </div>
                        <div class="mt-4 pt-2">
                            <a href="{{ route('products.index') }}"
                                class="btn btn-sm btn-outline-danger w-100 rounded-pill fw-bold"
                                style="font-size: 0.75rem;">Gestionar</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Analytics Section -->
            <div class="row g-3 g-lg-4 mb-4 mb-md-5">
                <div class="col-lg-8">
                    <div class="saas-card p-4 position-relative" style="min-height: 350px;">
                        <div class="sync-overlay" id="main-chart-sync">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold m-0 text-dark" style="font-size: 1rem;">Ventas del Periodo</h5>
                            <div class="live-status bg-light border-0 py-1 px-2" style="font-size: 0.6rem;">
                                <i class="fas fa-history me-1"></i> LIVE
                            </div>
                        </div>
                        <div class="chart-main-container" style="height: 320px;">
                            <canvas id="mainSalesChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="saas-card p-4 h-100">
                        <h5 class="fw-bold mb-4 text-dark" style="font-size: 1rem;">Top 5 Productos</h5>
                        <div id="top-products-container">
                            <p class="text-center text-muted py-5 small">Actualizando datos...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Feed Section -->
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="saas-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold m-0 text-dark">Actividad Reciente</h5>
                            <span class="text-muted small">Actualizado ahora</span>
                        </div>
                        <div id="activity-feed">
                            <!-- Activity items will be generated here -->
                            <div class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="saas-card p-4">
                        <h5 class="fw-bold mb-4 text-dark">Alertas de Sistema</h5>
                        <div id="alerts-panel">
                            <!-- Alerts will be generated here -->
                            <div class="alert alert-info border-0 rounded-4 py-3 d-flex align-items-center">
                                <i class="fas fa-info-circle me-3"></i>
                                <span class="small fw-semibold">No hay alertas críticas en este momento.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentRange = '7d';
            let mainChart = null;
            let sparklineSales = null;
            let sparklineTransactions = null;

            // --- Chart Helpers ---
            function createGradient(ctx, color) {
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, `${color}20`);
                gradient.addColorStop(1, `${color}00`);
                return gradient;
            }

            function initMainChart(labels, data) {
                const el = document.getElementById('mainSalesChart');
                if (!el) return;
                const ctx = el.getContext('2d');
                if (mainChart) mainChart.destroy();

                mainChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels || [],
                        datasets: [{
                            label: 'Ventas',
                            data: data || [],
                            borderColor: '#6366f1',
                            borderWidth: 3,
                            backgroundColor: createGradient(ctx, '#6366f1'),
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#6366f1',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: '#0f172a',
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: (ctx) => ` S/ ${ctx.parsed.y.toLocaleString()}`
                                }
                            }
                        },
                        scales: {
                            x: { display: true, grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } },
                            y: { display: true, grid: { borderDash: [5, 5], color: '#e2e8f0' }, ticks: { color: '#94a3b8', font: { size: 11 } } }
                        }
                    }
                });
            }

            function initSparkline(id, data, color) {
                const el = document.getElementById(id);
                if (!el) return null;
                const ctx = el.getContext('2d');
                return new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: (data || []).map((_, i) => i),
                        datasets: [{
                            data: data || [],
                            borderColor: color,
                            borderWidth: 2,
                            fill: false,
                            tension: 0.4,
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { enabled: false } },
                        scales: { x: { display: false }, y: { display: false } }
                    }
                });
            }

            // --- Core UI Updates ---
            function updateDashboard(data) {
                if (!data) return;

                // Helper for safe text updates
                const safeText = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = val;
                };

                // Stats
                safeText('val-income', 'S/ ' + (data.totalSalesToday || '0.00'));
                safeText('val-transactions', data.transactionCount || '0');
                safeText('val-users', data.totalUsers || '0');
                safeText('val-low-stock', data.productsLowStock || '0');

                // Main Chart
                initMainChart(data.chartLabels, data.chartData);

                // Sparklines
                if (sparklineSales) sparklineSales.destroy();
                sparklineSales = initSparkline('sparklineSales', data.sparklines?.sales, '#3b82f6');

                if (sparklineTransactions) sparklineTransactions.destroy();
                sparklineTransactions = initSparkline('sparklineTransactions', data.sparklines?.transactions, '#6366f1');

                // Top Products
                const topProductsCont = document.getElementById('top-products-container');
                if (topProductsCont) {
                    const topItems = data.topProducts || [];
                    if (topItems.length === 0) {
                        topProductsCont.innerHTML = '<p class="text-center text-muted py-5 small">No hay datos.</p>';
                    } else {
                        const maxQty = Math.max(...topItems.map(p => parseFloat(p.total_qty)), 1);
                        topProductsCont.innerHTML = topItems.map(p => `
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold text-dark text-truncate" style="max-width: 150px;">${p.name}</span>
                                        <span class="small text-muted fw-bold">${p.total_qty} units</span>
                                    </div>
                                    <div class="progress" style="height: 8px; border-radius: 4px; background: #f1f5f9;">
                                        <div class="progress-bar" style="width: ${(parseFloat(p.total_qty) / maxQty) * 100}%; background: var(--saas-primary); border-radius: 4px;"></div>
                                    </div>
                                    <div class="text-end mt-1">
                                        <span class="text-success x-small" style="font-size: 0.7rem;">S/ ${parseFloat(p.total_revenue || 0).toLocaleString()}</span>
                                    </div>
                                </div>
                            `).join('');
                    }
                }

                // Recent Activity
                const activityCont = document.getElementById('activity-feed');
                if (activityCont) {
                    const activity = data.recentActivity || [];
                    activityCont.innerHTML = activity.map(a => `
                            <div class="activity-item">
                                <div class="d-flex justify-content-between">
                                    <span class="small fw-bold text-dark">${a.title}</span>
                                    <span class="x-small text-muted" style="font-size: 0.7rem;">${a.time}</span>
                                </div>
                                <p class="small text-muted mb-0">${a.subtitle}</p>
                            </div>
                        `).join('') || '<p class="text-center text-muted py-4">Sin actividad.</p>';
                }

                // Alerts
                const alertsCont = document.getElementById('alerts-panel');
                if (alertsCont) {
                    const alerts = data.alerts || [];
                    alertsCont.innerHTML = alerts.map(al => `
                            <a href="${al.action}" class="alert-pill bg-${al.type}-subtle text-${al.type}">
                                <i class="fas fa-${al.type === 'danger' ? 'times-circle' : 'exclamation-circle'} fs-5"></i>
                                <span class="small fw-bold">${al.message}</span>
                            </a>
                        `).join('') || `
                            <div class="alert alert-info border-0 rounded-4 py-3 d-flex align-items-center mb-0">
                                <i class="fas fa-check-circle me-3 text-success"></i>
                                <span class="small fw-semibold">Todo bajo control. No hay alertas.</span>
                            </div>
                        `;
                }

                // Sync Indicator
                const badge = document.getElementById('status-badge');
                if (badge) {
                    badge.style.background = '#dcfce7';
                    setTimeout(() => badge.style.background = '#ecfdf5', 1000);
                }
            }

            function fetchStats() {
                const overlay = document.getElementById('main-chart-sync');
                if (overlay) overlay.style.display = 'flex';

                fetch(`{{ route("admin.stats") }}?range=${currentRange}`, {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(res => {
                        if (!res.ok) throw new Error('Error de red');
                        return res.json();
                    })
                    .then(data => {
                        updateDashboard(data);
                    })
                    .catch(err => console.error('Error fetching admin stats:', err))
                    .finally(() => {
                        if (overlay) overlay.style.display = 'none';
                    });
            }

            // --- Event Listeners ---
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentRange = this.dataset.range;
                    fetchStats();
                });
            });

            // Initial Load
            fetchStats();

            // Polling (Auto-Refresh)
            setInterval(fetchStats, 30000);
        });
    </script>
@endpush