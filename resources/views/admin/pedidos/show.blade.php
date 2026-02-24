@extends('layouts.admin')

@section('title', 'Detalle del Pedido Virtual - StyleBox')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.pedidos.index') }}">Pedidos</a></li>
                        <li class="breadcrumb-item active">{{ $pedido->numero_boleta ?? '#' . $pedido->id }}</li>
                    </ol>
                </nav>
                <h2 class="mb-0 fw-bold">Pedido: {{ $pedido->numero_boleta ?? '#' . $pedido->id }}</h2>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <!-- Client Card -->
                <div class="card card-custom mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-user me-2 text-primary"></i>Información del Cliente</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                    style="width: 48px; height: 48px; font-size: 20px;">
                                    {{ strtoupper(substr($pedido->buyer->name ?? 'N', 0, 1)) }}
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold">{{ $pedido->buyer->name ?? ($pedido->client->name ?? 'N/A') }}</h6>
                                <p class="text-muted small mb-0">
                                    {{ $pedido->buyer->email ?? ($pedido->client->email ?? '') }}
                                </p>
                            </div>
                        </div>
                        <hr class="text-muted my-3">
                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold">Documento</label>
                            <p class="mb-0">
                                {{ ($pedido->client->document_type ?? 'DNI') . ': ' . ($pedido->client->document_number ?? 'N/A') }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold">Teléfono</label>
                            <p class="mb-0">
                                {{ $pedido->client->phone ?? ($pedido->buyer->phone ?? 'Sin teléfono registrado') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Order Status Card -->
                <div class="card card-custom">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-cog me-2 text-primary"></i>Gestión del Pedido</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="mb-4">
                            <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Estado Actual</label>
                            @php
                                $badgeClass = match ($pedido->estado) {
                                    'pendiente_pago' => 'warning',
                                    'pagado' => 'success',
                                    'preparando' => 'info',
                                    'enviado' => 'primary',
                                    'entregado' => 'dark',
                                    'cancelado' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span
                                class="badge bg-{{ $badgeClass }} bg-opacity-10 text-{{ $badgeClass }} px-3 py-2 rounded-pill fs-6 w-100">
                                {{ $pedido->estadoLabel() }}
                            </span>
                        </div>

                        @if($pedido->estado == 'pendiente_pago')
                            <form action="{{ route('admin.pedidos.confirmar', $pedido->id) }}" method="POST"
                                class="confirm-payment-form mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 py-2">
                                    <i class="fas fa-check-circle me-2"></i>Confirmar Pago
                                </button>
                            </form>
                        @endif

                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Cambiar Estado
                                (Flujo)</label>
                            <form action="{{ route('admin.pedidos.estado.update', $pedido->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="nuevo_estado" class="form-select mb-2">
                                    @foreach($estados as $key => $label)
                                        <option value="{{ $key }}" {{ $pedido->estado == $key ? 'selected' : '' }}>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-dark w-100">Actualizar Estado</button>
                            </form>
                        </div>

                        @if($pedido->fecha_confirmacion_pago)
                            <div class="mt-4 p-3 bg-light rounded small">
                                <p class="mb-1 fw-bold"><i class="fas fa-shield-alt me-1 text-success"></i> Pago Confirmado</p>
                                <p class="mb-1 text-muted"><strong>Fecha:</strong>
                                    {{ $pedido->fecha_confirmacion_pago->format('d/m/Y H:i') }}</p>
                                <p class="mb-0 text-muted"><strong>Por:</strong> {{ $pedido->confirmador->name ?? 'Admin' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Order Details -->
            <div class="col-lg-8">
                <div class="card card-custom mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="mb-0 fw-bold">Productos del Pedido</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Producto</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-end">Precio Unit.</th>
                                        <th class="text-end pe-4">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedido->details as $detail)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        @if($detail->product && $detail->product->image)
                                                            <img src="{{ asset('storage/' . $detail->product->image) }}"
                                                                class="rounded shadow-sm"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                                                                style="width: 50px; height: 50px;">
                                                                <i class="fas fa-image"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="mb-0 fw-bold">
                                                            {{ $detail->product->name ?? 'Producto no encontrado' }}
                                                        </h6>
                                                        <small
                                                            class="text-muted">{{ $detail->product->category->name ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center fw-bold text-muted">{{ $detail->quantity }}</td>
                                            <td class="text-end">S/ {{ number_format($detail->unit_price, 2) }}</td>
                                            <td class="text-end pe-4 fw-bold">S/ {{ number_format($detail->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                        <td class="text-end pe-4 fw-bold text-muted">S/
                                            {{ number_format($pedido->total, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold fs-5">TOTAL:</td>
                                        <td class="text-end pe-4 fw-bold fs-5 text-dark">S/
                                            {{ number_format($pedido->total, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="card card-custom">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="mb-0 fw-bold">Detalle de la Transacción</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold mb-1 d-block">Método de Pago</label>
                                @php $payment = $pedido->payments->first(); @endphp
                                <div class="p-3 border rounded">
                                    <i class="fas fa-credit-card me-2 text-muted"></i>
                                    <span
                                        class="fw-bold">{{ $payment->paymentMethod->name ?? 'Venta sin método registrado' }}</span>
                                    @if($payment && $payment->reference)
                                        <div class="mt-1 small text-muted">Ref: {{ $payment->reference }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold mb-1 d-block">Fecha de Compra</label>
                                <div class="p-3 border rounded">
                                    <i class="fas fa-calendar-alt me-2 text-muted"></i>
                                    <span class="fw-bold">{{ $pedido->date->format('l d \d\e F, Y - H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.confirm-payment-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Confirmar Pago?',
                        text: "Esta acción cambiará el estado a PAGADO y registrará tu usuario como confirmador.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#1a1a1a',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, confirmar pago',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection