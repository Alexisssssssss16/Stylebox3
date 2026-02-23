@extends('layouts.shop')

@section('title', 'Checkout — StyleBox')

@push('styles')
<style>
    /* ── Checkout page styles ── */
    .checkout-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem 6rem;
    }

    .section-card {
        background: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,.04);
        position: relative;       /* own stacking context */
        isolation: isolate;       /* prevent children from bleeding outside */
        overflow: hidden;         /* clip any absolute/fixed children */
        z-index: 1;
    }

    .section-title {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: #888;
        margin-bottom: 1.1rem;
    }

    /* Delivery type radio cards */
    .delivery-option {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.1rem;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        margin-bottom: 0.75rem;
    }

    .delivery-option:hover {
        border-color: #adb5bd;
        background: #fafafa;
    }

    .delivery-option.selected {
        border-color: #1a1a1a;
        background: #f8f8f8;
    }

    .delivery-option .icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        background: #f0f0f0;
        flex-shrink: 0;
    }

    .delivery-option.selected .icon-wrap {
        background: #1a1a1a;
        color: #fff;
    }

    .delivery-option input[type=radio] {
        display: none;
    }

    /* Payment method cards */
    .payment-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        cursor: pointer;
        transition: border-color 0.2s;
        margin-bottom: 0.6rem;
    }

    .payment-option:hover {
        border-color: #adb5bd;
    }

    .payment-option.selected {
        border-color: #1a1a1a;
        background: #f8f8f8;
    }

    .payment-option input[type=radio] {
        display: none;
    }

    /* Order summary */
    .order-line {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .order-line:last-child {
        border-bottom: none;
    }

    .order-thumb {
        width: 52px;
        height: 52px;
        min-width: 52px;          /* prevent flex squish */
        border-radius: 8px;
        object-fit: cover;
        display: block;           /* remove inline baseline gap */
        opacity: 1 !important;    /* override any inherited opacity from shop layout */
        position: static !important; /* prevent absolute escape from hero styles */
        flex-shrink: 0;
        background: #f0f0f0;
    }

    .order-thumb-placeholder {
        width: 52px;
        height: 52px;
        border-radius: 8px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ddd;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    /* Totals */
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.4rem 0;
    }

    .total-row.grand {
        border-top: 2px solid #1a1a1a;
        margin-top: 0.5rem;
        padding-top: 0.75rem;
        font-weight: 700;
        font-size: 1.1rem;
    }

    /* Confirm button */
    .btn-confirm {
        background: #1a1a1a;
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 15px 32px;
        font-weight: 700;
        font-size: 1rem;
        width: 100%;
        transition: background 0.2s, transform 0.15s;
        letter-spacing: 0.2px;
    }

    .btn-confirm:hover {
        background: #000;
        transform: translateY(-1px);
        color: #fff;
    }

    .btn-confirm:disabled {
        background: #adb5bd;
        transform: none;
    }

    /* Repartidor section animation */
    #repartidor-section {
        overflow: hidden;
        transition: max-height 0.35s ease, opacity 0.3s ease;
        max-height: 0;
        opacity: 0;
    }

    #repartidor-section.visible {
        max-height: 800px;
        opacity: 1;
    }

    .form-control:focus {
        border-color: #1a1a1a;
        box-shadow: 0 0 0 0.2rem rgba(26,26,26,.1);
    }

    .info-delivery-alert {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #bae6fd;
        border-radius: 10px;
        padding: 0.85rem 1rem;
        font-size: 0.875rem;
        color: #0369a1;
    }
</style>
@endpush

@section('content')
<div class="checkout-wrapper">

    {{-- Page title --}}
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="font-size: 1.6rem;">Finalizar compra</h1>
        <p class="text-muted small mb-0">Revisa tu pedido y elige cómo recogerlo.</p>
    </div>

    {{-- Global error --}}
    @if(session('error'))
        <div class="alert alert-danger rounded-3 mb-3">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form">
        @csrf

        <div class="row g-3 align-items-start">

            {{-- LEFT COLUMN --}}
            <div class="col-lg-7">

                {{-- ── 1. Order Summary ── --}}
                <div class="section-card">
                    <p class="section-title"><i class="fas fa-shopping-bag me-1"></i>Resumen del pedido</p>

                    @foreach($items as $item)
                        <div class="order-line">
                            @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}"
                                     alt="{{ $item['name'] }}" class="order-thumb">
                            @else
                                <div class="order-thumb-placeholder">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size:0.93rem;">{{ $item['name'] }}</div>
                                <div class="text-muted small">Cant: {{ $item['quantity'] }} × S/ {{ number_format($item['price'], 2) }}</div>
                            </div>
                            <div class="fw-bold text-end" style="font-size:0.93rem;">
                                S/ {{ number_format($item['price'] * $item['quantity'], 2) }}
                            </div>
                        </div>
                    @endforeach

                    {{-- Totals --}}
                    <div class="mt-3">
                        <div class="total-row text-muted">
                            <span>Subtotal</span>
                            <span>S/ {{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="total-row text-muted">
                            <span>Costo de envío</span>
                            <span class="text-success fw-semibold">Gratis</span>
                        </div>
                        <div class="total-row grand">
                            <span>Total</span>
                            <span style="color:#d4a017;">S/ {{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- ── 2. Tipo de entrega ── --}}
                <div class="section-card">
                    <p class="section-title"><i class="fas fa-store me-1"></i>Tipo de entrega</p>

                    @error('tipo_entrega')
                        <div class="text-danger small mb-2"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror

                    {{-- Recojo en tienda --}}
                    <label class="delivery-option {{ old('tipo_entrega', 'recojo_tienda') == 'recojo_tienda' ? 'selected' : '' }}"
                           id="label-recojo" for="tipo-recojo">
                        <input type="radio" name="tipo_entrega" id="tipo-recojo"
                               value="recojo_tienda"
                               {{ old('tipo_entrega', 'recojo_tienda') == 'recojo_tienda' ? 'checked' : '' }}>
                        <div class="icon-wrap {{ old('tipo_entrega', 'recojo_tienda') == 'recojo_tienda' ? 'bg-dark text-white' : '' }}">
                            <i class="fas fa-walking"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Recojo en tienda</div>
                            <div class="text-muted small">Yo mismo recojo el pedido.</div>
                        </div>
                    </label>

                    {{-- Mi delivery --}}
                    <label class="delivery-option {{ old('tipo_entrega') == 'mi_delivery' ? 'selected' : '' }}"
                           id="label-delivery" for="tipo-delivery">
                        <input type="radio" name="tipo_entrega" id="tipo-delivery"
                               value="mi_delivery"
                               {{ old('tipo_entrega') == 'mi_delivery' ? 'checked' : '' }}>
                        <div class="icon-wrap {{ old('tipo_entrega') == 'mi_delivery' ? 'bg-dark text-white' : '' }}">
                            <i class="fas fa-motorcycle"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Mi delivery recoge en tienda</div>
                            <div class="text-muted small">Envía tu propio repartidor a recoger.</div>
                        </div>
                    </label>

                    {{-- ── Repartidor fields (shown only for mi_delivery) ── --}}
                    <div id="repartidor-section" class="{{ old('tipo_entrega') == 'mi_delivery' ? 'visible' : '' }}">
                        <div class="info-delivery-alert mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Tu delivery puede recoger el pedido cuando esté listo.</strong>
                            Te avisaremos cuando el pedido esté disponible en tienda.
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold small">Nombre del repartidor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre_repartidor') is-invalid @enderror"
                                       name="nombre_repartidor" value="{{ old('nombre_repartidor') }}"
                                       placeholder="Ej: Juan Pérez" maxlength="120">
                                @error('nombre_repartidor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold small">DNI o identificación <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('dni_repartidor') is-invalid @enderror"
                                       name="dni_repartidor" value="{{ old('dni_repartidor') }}"
                                       placeholder="Ej: 12345678" maxlength="20">
                                @error('dni_repartidor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold small">Teléfono del repartidor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('telefono_repartidor') is-invalid @enderror"
                                       name="telefono_repartidor" value="{{ old('telefono_repartidor') }}"
                                       placeholder="Ej: 987654321" maxlength="20">
                                @error('telefono_repartidor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold small">Empresa de delivery <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('empresa_delivery') is-invalid @enderror"
                                       name="empresa_delivery" value="{{ old('empresa_delivery') }}"
                                       placeholder="Ej: Rappi, motorizado propio…" maxlength="120">
                                @error('empresa_delivery')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold small">Placa del vehículo <span class="text-muted">(opcional)</span></label>
                                <input type="text" class="form-control @error('placa_vehiculo') is-invalid @enderror"
                                       name="placa_vehiculo" value="{{ old('placa_vehiculo') }}"
                                       placeholder="Ej: ABC-123" maxlength="20">
                                @error('placa_vehiculo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /LEFT --}}

            {{-- RIGHT COLUMN --}}
            <div class="col-lg-5">

                {{-- ── 3. Método de pago ── --}}
                <div class="section-card">
                    <p class="section-title"><i class="fas fa-credit-card me-1"></i>Método de pago</p>

                    @error('payment_method_id')
                        <div class="text-danger small mb-2"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                    @enderror

                    @forelse($paymentMethods as $method)
                        <label class="payment-option {{ old('payment_method_id') == $method->id ? 'selected' : '' }}"
                               for="pm-{{ $method->id }}">
                            <input type="radio" name="payment_method_id" id="pm-{{ $method->id }}"
                                   value="{{ $method->id }}"
                                   {{ old('payment_method_id') == $method->id ? 'checked' : '' }}>
                            <i class="fas fa-money-bill-wave text-success fs-5"></i>
                            <span class="fw-semibold">{{ $method->name }}</span>
                        </label>
                    @empty
                        <p class="text-muted small">No hay métodos de pago disponibles.</p>
                    @endforelse
                </div>

                {{-- ── 4. Confirm ── --}}
                <div class="section-card">
                    <p class="section-title"><i class="fas fa-lock me-1"></i>Confirmar pedido</p>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total a pagar</span>
                        <span class="fw-bold" style="font-size:1.2rem; color:#d4a017;">
                            S/ {{ number_format($subtotal, 2) }}
                        </span>
                    </div>

                    <button type="submit" class="btn-confirm" id="btn-confirm">
                        <i class="fas fa-check-circle me-2"></i>Confirmar pedido
                    </button>

                    <a href="{{ route('cart.index') }}"
                       class="btn btn-link text-muted text-decoration-none d-block text-center mt-2 small">
                        <i class="fas fa-arrow-left me-1"></i>Volver al carrito
                    </a>
                </div>

                {{-- Trust badges --}}
                <div class="d-flex gap-3 justify-content-center flex-wrap mt-1">
                    <span class="text-muted small"><i class="fas fa-shield-alt text-success me-1"></i>Compra segura</span>
                    <span class="text-muted small"><i class="fas fa-store text-primary me-1"></i>Recojo en tienda</span>
                    <span class="text-muted small"><i class="fas fa-tag text-warning me-1"></i>Sin cargo extra</span>
                </div>

            </div>{{-- /RIGHT --}}

        </div>{{-- /row --}}
    </form>

</div>
@endsection

@push('scripts')
<script>
    // ── Delivery option toggle ──────────────────────────────────────────
    const radioRecojo   = document.getElementById('tipo-recojo');
    const radioDelivery = document.getElementById('tipo-delivery');
    const labelRecojo   = document.getElementById('label-recojo');
    const labelDelivery = document.getElementById('label-delivery');
    const repartidorSection = document.getElementById('repartidor-section');

    function updateDeliveryUI() {
        const isMiDelivery = radioDelivery.checked;

        labelRecojo.classList.toggle('selected',   !isMiDelivery);
        labelDelivery.classList.toggle('selected',  isMiDelivery);

        labelRecojo.querySelector('.icon-wrap').classList.toggle('bg-dark',    !isMiDelivery);
        labelRecojo.querySelector('.icon-wrap').classList.toggle('text-white', !isMiDelivery);
        labelDelivery.querySelector('.icon-wrap').classList.toggle('bg-dark',    isMiDelivery);
        labelDelivery.querySelector('.icon-wrap').classList.toggle('text-white', isMiDelivery);

        repartidorSection.classList.toggle('visible', isMiDelivery);

        // Toggle required attr on repartidor inputs
        repartidorSection.querySelectorAll('input').forEach(input => {
            if (isMiDelivery && input.name !== 'placa_vehiculo') {
                input.required = true;
            } else {
                input.required = false;
            }
        });
    }

    radioRecojo.addEventListener('change',   updateDeliveryUI);
    radioDelivery.addEventListener('change', updateDeliveryUI);

    // Run on page load to handle old() values
    updateDeliveryUI();

    // ── Payment option selection ────────────────────────────────────────
    document.querySelectorAll('.payment-option').forEach(label => {
        label.addEventListener('click', function () {
            document.querySelectorAll('.payment-option').forEach(l => l.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // ── Submit guard: prevent double submit ─────────────────────────────
    document.getElementById('checkout-form').addEventListener('submit', function () {
        const btn = document.getElementById('btn-confirm');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando…';
    });
</script>
@endpush
