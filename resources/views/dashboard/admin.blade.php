@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="row g-4 mb-4">
        <!-- Stat Cards -->
        <div class="col-md-3">
            <div class="card card-custom h-100 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-2 opacity-75">Ventas Hoy</h6>
                            <h3 class="fw-bold mb-0" id="val-sales-today">S/ 0.00</h3>
                        </div>
                        <i class="fas fa-cash-register fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom h-100 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-2 opacity-75">Transacciones</h6>
                            <h3 class="fw-bold mb-0" id="val-transactions">0</h3>
                        </div>
                        <i class="fas fa-receipt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom h-100 bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-2 opacity-75">Bajo Stock</h6>
                            <h3 class="fw-bold mb-0" id="val-low-stock">0</h3>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom h-100 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-2 opacity-75">Usuarios</h6>
                            <h3 class="fw-bold mb-0" id="val-users">-</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sales Chart -->
        <div class="col-md-8">
            <div class="card card-custom h-100">
                <div class="card-header bg-white pt-4 pb-0 border-0 d-flex justify-content-between">
                    <h5 class="fw-bold">Ventas Últimos 7 Días</h5>
                    <span class="badge bg-success bg-opacity-10 text-success" id="live-indicator"><i
                            class="fas fa-circle me-1 small"></i> En vivo</span>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-md-4">
            <div class="card card-custom h-100">
                <div class="card-header bg-white pt-4 pb-0 border-0">
                    <h5 class="fw-bold">Producto Estrella</h5>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <div class="mb-3">
                        <i class="fas fa-crown fa-3x text-warning mb-3"></i>
                        <h4 class="fw-bold" id="val-best-seller">Cargando...</h4>
                        <p class="text-muted">Más vendido en volumen histórico</p>
                    </div>
                    <hr>
                    <div class="text-start">
                        <p class="mb-1 small text-muted">Total Ventas Históricas</p>
                        <h5 class="fw-bold" id="val-total-sales">S/ 0.00</h5>
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
            // Initialize Chart
            const ctx = document.getElementById('salesChart').getContext('2d');
            let salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Ventas (S/)',
                        data: [],
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            function fetchStats() {
                fetch('{{ route("admin.stats") }}', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Update Text Stats
                        document.getElementById('val-sales-today').textContent = 'S/ ' + data.totalSalesToday;
                        document.getElementById('val-transactions').textContent = data.transactionCount;
                        document.getElementById('val-low-stock').textContent = data.productsLowStock;
                        document.getElementById('val-users').textContent = data.totalUsers;
                        document.getElementById('val-best-seller').textContent = data.bestSeller;
                        document.getElementById('val-total-sales').textContent = 'S/ ' + data.totalSales;

                        // Update Chart
                        salesChart.data.labels = data.chartLabels;
                        salesChart.data.datasets[0].data = data.chartData;
                        salesChart.update('none'); // 'none' mode prevents full re-animation glitch

                        // Blink indicator
                        let badge = document.getElementById('live-indicator');
                        badge.classList.remove('bg-opacity-10');
                        badge.classList.add('bg-opacity-50');
                        setTimeout(() => {
                            badge.classList.remove('bg-opacity-50');
                            badge.classList.add('bg-opacity-10');
                        }, 500);
                    })
                    .catch(err => console.error('Error polling stats:', err));
            }

            // Initial fetch
            fetchStats();

            // Poll every 10 seconds
            setInterval(fetchStats, 10000);
        });
    </script>
@endsection