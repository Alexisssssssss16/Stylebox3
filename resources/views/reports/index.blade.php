@extends('layouts.admin')

@section('title', 'Reportes Avanzados')

@section('content')
    <div class="row g-4">
        <!-- Header -->
        <div class="col-12">
            <h2 class="fw-bold mb-0">Reportes y Analíticas</h2>
            <p class="text-muted">Análisis financiero y de inventario</p>
        </div>

        <!-- KPI Cards -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-uppercase mb-2 text-white-50">Valor del Inventario (Costo)</h6>
                    <h3 class="fw-bold mb-0">S/ {{ number_format($inventoryStats->total_cost, 2) }}</h3>
                    <small>Inversión actual en mercadería</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-success text-white">
                <div class="card-body">
                    <h6 class="text-uppercase mb-2 text-white-50">Valor del Inventario (Venta)</h6>
                    <h3 class="fw-bold mb-0">S/ {{ number_format($inventoryStats->total_price, 2) }}</h3>
                    <small>Proyección de ingresos</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-info text-white">
                <div class="card-body">
                    <h6 class="text-uppercase mb-2 text-white-50">Utilidad Estimada</h6>
                    <h3 class="fw-bold mb-0">S/ {{ number_format($estimatedUtility, 2) }}</h3>
                    <small>Margen bruto potencial</small>
                </div>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">Evolución de Ventas (30 Días)</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Sales by Seller -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">Rendimiento por Vendedor</h6>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($salesBySeller as $seller)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">{{ $seller->name }}</div>
                                <small class="text-muted">{{ $seller->count }} ventas</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">S/ {{ number_format($seller->total, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Low Rotation Products -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white text-danger">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-exclamation-circle me-2"></i>Productos Sin Rotación (30 Días)
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Valor Inmovilizado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowRotationProducts as $product)
                                <tr>
                                    <td class="fw-medium">{{ $product->name }}</td>
                                    <td>{{ $product->category }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>S/ {{ number_format($product->stock * $product->cost, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Todo el inventario tiene movimiento.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('salesChart').getContext('2d');
                const salesData = @json($salesByDay);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: salesData.map(d => d.date),
                        datasets: [{
                            label: 'Ventas Diarias (S/)',
                            data: salesData.map(d => d.total),
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            });
        </script>
    @endpush
@endsection