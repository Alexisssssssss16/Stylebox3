@extends('layouts.shop')

@section('title', 'Mi Carrito — StyleBox')

@push('styles')
    <style>
        .cart-wrapper {
            max-width: 780px;
            margin: 0 auto;
            padding: 2rem 1rem 6rem;
        }

        .cart-card {
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f5f5f5;
            transition: background .15s;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-thumb {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
            background: #f0f0f0;
        }

        .cart-thumb-placeholder {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .qty-ctrl {
            display: inline-flex;
            align-items: center;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }

        .qty-ctrl button {
            width: 32px;
            height: 32px;
            border: none;
            background: #f8f9fa;
            font-size: 1rem;
            cursor: pointer;
            transition: background .15s;
        }

        .qty-ctrl button:hover {
            background: #e9ecef;
        }

        .qty-ctrl span {
            width: 36px;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            background: #fff;
        }

        .btn-checkout {
            background: #1a1a1a;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            transition: background .2s, transform .15s;
        }

        .btn-checkout:hover {
            background: #000;
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-checkout:disabled {
            background: #adb5bd;
        }
    </style>
@endpush

@section('content')
    <div class="cart-wrapper">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="fw-bold mb-0" style="font-size:1.6rem;">
                <i class="fas fa-shopping-bag me-2"></i>Mi carrito
            </h1>
            <a href="{{ route('shop.index') }}" class="text-muted small text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i>Seguir comprando
            </a>
        </div>

        @if(session('info'))
            <div class="alert alert-info rounded-3">{{ session('info') }}</div>
        @endif

        @if($items->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-shopping-bag text-muted" style="font-size:4rem; opacity:.3;"></i>
                <p class="mt-3 text-muted">Tu carrito está vacío.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-4 mt-2">Ver productos</a>
            </div>
        @else
            <div class="cart-card mb-3">
                @foreach($items as $item)
                    <div class="cart-item" id="cart-item-{{ $item['id'] }}-{{ $item['talla_id'] ?? 0 }}">
                        @if($item['image'])
                            <img src="{{ asset('storage/' . $item['image']) }}" class="cart-thumb" alt="{{ $item['name'] }}">
                        @else
                            <div class="cart-thumb-placeholder"><i class="fas fa-tshirt"></i></div>
                        @endif

                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $item['name'] }}</div>
                            @if($item['talla'])
                                <div class="badge bg-light text-dark border fw-normal py-1 px-2 mt-1" style="font-size: 0.75rem;">
                                    Talla: <span class="fw-bold">{{ $item['talla'] }}</span>
                                </div>
                            @endif
                            <div class="text-muted small mt-1">S/ {{ number_format($item['price'], 2) }} c/u</div>
                        </div>

                        <div class="qty-ctrl">
                            <button type="button"
                                onclick="updateQty({{ $item['id'] }}, -1, {{ $item['talla_id'] ?? 'null' }})">−</button>
                            <span id="qty-{{ $item['id'] }}-{{ $item['talla_id'] ?? 0 }}">{{ $item['quantity'] }}</span>
                            <button type="button"
                                onclick="updateQty({{ $item['id'] }}, 1, {{ $item['talla_id'] ?? 'null' }})">+</button>
                        </div>

                        <div class="fw-bold ms-2" id="price-{{ $item['id'] }}-{{ $item['talla_id'] ?? 0 }}"
                            style="min-width:70px; text-align:right;">
                            S/ {{ number_format($item['price'] * $item['quantity'], 2) }}
                        </div>

                        <button class="btn btn-sm text-danger p-1 ms-1"
                            onclick="removeItem({{ $item['id'] }}, {{ $item['talla_id'] ?? 'null' }})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                @endforeach
            </div>

            {{-- Summary --}}
            <div class="bg-white border rounded-3 p-3 mb-3" style="border-color:#f0f0f0 !important;">
                <div class="d-flex justify-content-between mb-1 text-muted">
                    <span>Subtotal</span>
                    <span id="subtotal-display">S/ {{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-muted">
                    <span>Envío</span>
                    <span class="text-success fw-semibold">Gratis</span>
                </div>
                <div class="d-flex justify-content-between fw-bold mt-2 pt-2" style="border-top:2px solid #1a1a1a;">
                    <span>Total</span>
                    <span style="color:#d4a017;" id="total-display">S/ {{ number_format($subtotal, 2) }}</span>
                </div>
            </div>

            <a href="{{ route('checkout.show') }}" class="btn-checkout d-block text-center text-decoration-none">
                <i class="fas fa-lock me-2"></i>Ir al checkout
            </a>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        const prices = {
            @foreach($items as $item)
                {{ $item['id'] }}: {{ $item['price'] }},
            @endforeach
            };

        /**
         * Update quantity via Fetch API
         */
        async function updateQty(id, delta, tallaId = null) {
            const key = tallaId ? `${id}-${tallaId}` : `${id}-0`;
            const qtyEl = document.getElementById('qty-' + key);
            let currentQty = parseInt(qtyEl.textContent);
            let newQty = currentQty + delta;

            if (newQty < 1) {
                return removeItem(id, tallaId);
            }

            try {
                const res = await fetch(`/cart/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        quantity: newQty,
                        talla_id: tallaId
                    })
                });

                const data = await res.json();

                if (data.success) {
                    qtyEl.textContent = newQty;
                    updateSummary(data.subtotal, data.count);

                    // Update item subtotal in UI
                    const priceEl = document.getElementById('price-' + key);
                    if (priceEl && prices[id]) {
                        priceEl.textContent = 'S/ ' + (prices[id] * newQty).toFixed(2);
                    }
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: data.message || 'No se pudo actualizar la cantidad.',
                        confirmButtonColor: '#000'
                    });
                }
            } catch (err) {
                console.error(err);
            }
        }

        /**
         * Remove item from cart
         */
        async function removeItem(id, tallaId = null) {
            const key = tallaId ? `${id}-${tallaId}` : `${id}-0`;
            const result = await Swal.fire({
                title: '¿Eliminar producto?',
                text: "Se quitará de tu carrito de compras.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#000',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (!result.isConfirmed) return;

            try {
                const res = await fetch(`/cart/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        _method: 'DELETE',
                        talla_id: tallaId
                    })
                });

                const data = await res.json();

                if (data.success) {
                    const itemRow = document.getElementById('cart-item-' + key);
                    if (itemRow) {
                        itemRow.style.opacity = '0';
                        setTimeout(() => {
                            itemRow.remove();
                            updateSummary(data.subtotal, data.count);
                            if (data.count === 0) {
                                location.reload(); // Show "Empty Cart" view
                            }
                        }, 300);
                    }
                }
            } catch (err) {
                console.error(err);
            }
        }

        /**
         * Update totals in the UI
         */
        function updateSummary(total, count) {
            const totalDisplay = document.getElementById('total-display');
            if (totalDisplay) {
                totalDisplay.textContent = 'S/ ' + parseFloat(total).toFixed(2);
            }

            // Subtotal display is often separate but equal in this simple cart
            const subtotalDisplay = document.getElementById('subtotal-display');
            if (subtotalDisplay) {
                subtotalDisplay.textContent = 'S/ ' + parseFloat(total).toFixed(2);
            }
        }
    </script>
@endpush