@extends('layouts.admin')

@section('title', 'Panel de Vendedor')

@section('content')
    <div class="row g-4 mb-4">
        <!-- Stat Cards -->
        <div class="col-md-6">
            <div class="card card-custom h-100 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-2 opacity-75">Mis Ventas Hoy</h6>
                            <h3 class="fw-bold mb-0" id="val-my-sales">S/ 0.00</h3>
                        </div>
                        <i class="fas fa-wallet fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-custom h-100 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-2 opacity-75">Mis Transacciones</h6>
                            <h3 class="fw-bold mb-0" id="val-my-transactions">0</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Sales Table -->
        <div class="col-md-8">
            <div class="card card-custom h-100">
                <div class="card-header bg-white pt-4 pb-0 border-0 d-flex justify-content-between">
                    <h5 class="fw-bold">Últimas Ventas</h5>
                    <a href="{{ route('pos.index') }}" class="btn btn-sm btn-primary-custom">Nueva Venta</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Hora</th>
                                </tr>
                            </thead>
                            <tbody id="recent-sales-body">
                                <!-- Injected via JS -->
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Stats -->
        <div class="col-md-4">
            <div class="card card-custom h-100">
                <div class="card-header bg-white pt-4 pb-0 border-0">
                    <h5 class="fw-bold">Métodos de Pago</h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('paymentChart').getContext('2d');
            let paymentChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Efectivo', 'Tarjeta', 'Yape'],
                    datasets: [{
                        data: [0, 0, 0],
                        backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            function fetchStats() {
                fetch('{{ route("vendedor.stats") }}', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('val-my-sales').textContent = 'S/ ' + data.mySalesToday;
                        document.getElementById('val-my-transactions').textContent = data.myTransactionCount;

                        // Update Table
                        let tbody = document.getElementById('recent-sales-body');
                        if (data.recentSales.length > 0) {
                            tbody.innerHTML = data.recentSales.map(sale => `
                                    <tr>
                                        <td>#${sale.id}</td>
                                        <td>${sale.client}</td>
                                        <td class="fw-bold">S/ ${sale.total}</td>
                                        <td class="text-muted small">${sale.time}</td>
                                    </tr>
                                `).join('');
                        } else {
                            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Sin ventas recientes</td></tr>';
                        }

                        // Update Chart
                        if (data.paymentStats) {
                            paymentChart.data.labels = data.paymentStats.labels;
                            paymentChart.data.datasets[0].data = data.paymentStats.data;
                            paymentChart.update();
                        }
                    })
                    .catch(err => console.error('Error polling stats:', err));
            }

            fetchStats();
            setInterval(fetchStats, 15000);
        });
    </script>
@endsection