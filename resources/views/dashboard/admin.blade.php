@extends('layouts.admin')

@section('title', 'StyleBox | Premium Dashboard')

@section('content')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.3);
        --indigo-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        --emerald-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --amber-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --rose-gradient: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
    }

    .dashboard-container {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 1.25rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease;
        overflow: hidden;
    }

    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.12);
    }

    .stat-badge {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.025em;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .gradient-text {
        background: var(--indigo-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .sync-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #10b981;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }

    .sync-active .sync-indicator {
        animation: pulse-green 1.5s infinite;
    }

    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .bg-indigo-grad { background: var(--indigo-gradient); }
    .bg-emerald-grad { background: var(--emerald-gradient); }
    .bg-amber-grad { background: var(--amber-gradient); }
    .bg-rose-grad { background: var(--rose-gradient); }

    .stat-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
    }
</style>

<div class="dashboard-container p-2">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold mb-1">Panel de Control</h2>
            <p class="text-muted small mb-0">Gestión inteligente de StyleBox SaaS</p>
        </div>
        <div class="stat-badge bg-emerald-light sync-active" id="live-status-container">
            <span class="sync-indicator pulse"></span>
            <span class="text-emerald-dark" id="live-text">Actualizando en vivo</span>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-4 h-100">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="icon-box bg-indigo-grad shadow-sm">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <span class="text-success small fw-bold">Hoy</span>
                </div>
                <div class="stat-label">Ingresos del Día</div>
                <div class="stat-value" id="val-sales-today">S/ 0.00</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-4 h-100">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="icon-box bg-emerald-grad shadow-sm">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <span class="text-success small fw-bold">Total</span>
                </div>
                <div class="stat-label">Transacciones</div>
                <div class="stat-value" id="val-transactions">0</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-4 h-100">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="icon-box bg-rose-grad shadow-sm">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <span class="text-danger small fw-bold">Alerta</span>
                </div>
                <div class="stat-label">Stock Crítico</div>
                <div class="stat-value" id="val-low-stock">0</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-4 h-100">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="icon-box bg-amber-grad shadow-sm">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="text-primary small fw-bold">Clientes</span>
                </div>
                <div class="stat-label">Usuarios Registrados</div>
                <div class="stat-value" id="val-users">-</div>
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0 text-dark">Rendimiento de Ventas</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border-0 px-3" disabled>Últimos 7 Días</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <h5 class="fw-bold mb-4 text-dark">Producto Estrella</h5>
                <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="position-relative mb-4">
                        <div class="bg-amber-grad rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-crown fa-3x text-white"></i>
                        </div>
                        <div class="position-absolute top-0 end-0 bg-white border rounded-circle p-2 shadow-sm">
                            <i class="fas fa-star text-warning"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-2 text-center" id="val-best-seller">Cargando...</h4>
                    <span class="badge bg-light text-dark px-3 py-2 border mb-4">Líder en Volumen</span>
                </div>
                <div class="mt-auto border-top pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-medium">Histórico Generado</span>
                        <span class="fw-bold fs-5 text-dark" id="val-total-sales">S/ 0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Font setup (Google Fonts - Inter)
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap';
        document.head.appendChild(link);

        // UI Helpers
        const container = document.getElementById('live-status-container');
        const liveText = document.getElementById('live-text');

        function setStatusSyncing(isSyncing) {
            if (isSyncing) {
                container.classList.add('sync-active');
                liveText.textContent = 'En vivo';
            } else {
                container.classList.remove('sync-active');
                liveText.textContent = 'Actualizado';
            }
        }

        // Initialize Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        let salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels ?? []) !!},
                datasets: [{
                    data: {!! json_encode($chartData ?? []) !!},
                    borderColor: '#6366f1',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    backgroundColor: gradient,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 }, color: '#9ca3af' } },
                    y: { grid: { borderDash: [5, 5], color: '#e5e7eb' }, ticks: { font: { family: 'Inter', size: 11 }, color: '#9ca3af' } }
                }
            }
        });

        function updateDashboard(data) {
            if (!data) return;
            
            // Text values with subtle animation
            const updateVal = (id, newVal) => {
                const el = document.getElementById(id);
                if (el.textContent !== newVal) {
                    el.style.transition = 'none';
                    el.style.opacity = '0.5';
                    setTimeout(() => {
                        el.textContent = newVal;
                        el.style.transition = 'opacity 0.5s ease';
                        el.style.opacity = '1';
                    }, 100);
                }
            };

            updateVal('val-sales-today', 'S/ ' + data.totalSalesToday);
            updateVal('val-transactions', data.transactionCount);
            updateVal('val-low-stock', data.productsLowStock);
            updateVal('val-users', data.totalUsers);
            updateVal('val-best-seller', data.bestSeller);
            updateVal('val-total-sales', 'S/ ' + data.totalSales);

            // Chart update
            salesChart.data.labels = data.chartLabels;
            salesChart.data.datasets[0].data = data.chartData;
            salesChart.update('active');

            setStatusSyncing(true);
            setTimeout(() => setStatusSyncing(false), 2000);
        }

        function fetchStats() {
            fetch('{{ route("admin.stats") }}', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => updateDashboard(data))
            .catch(err => console.error('Sync Error:', err));
        }

        // Faster Polling (5 seconds) to simulate real-time perfection
        fetchStats();
        setInterval(fetchStats, 5000);

        // Real-time Event Setup (CDN Fallback)
        window.addEventListener('load', () => {
            if (typeof Echo !== 'undefined' && !window.EchoInstance) {
                try {
                    window.EchoInstance = new Echo({
                        broadcaster: 'pusher',
                        key: '{{ env("VITE_PUSHER_APP_KEY") }}',
                        cluster: '{{ env("VITE_PUSHER_APP_CLUSTER", "mt1") }}',
                        forceTLS: true
                    });
                    
                    window.EchoInstance.channel('stats-channel')
                        .listen('.venta.realizada', e => updateDashboard(e.stats))
                        .listen('.stock.actualizado', e => updateDashboard(e.stats))
                        .listen('.usuario.registrado', e => updateDashboard(e.stats));
                } catch (e) {
                    console.warn('WebSocket unavailable, using high-speed polling.');
                }
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
@endsection