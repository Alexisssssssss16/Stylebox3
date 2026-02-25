@extends('layouts.admin')

@section('title', 'Punto de Venta')

@push('styles')
    <style>
        .product-card {
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--accent-color);
        }

        .product-img {
            height: 140px;
            object-fit: cover;
            background-color: #f8f9fa;
        }

        .cart-panel {
            height: calc(100vh - 100px);
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .cart-footer {
            padding: 1.5rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            border-radius: 0 0 12px 12px;
        }
    </style>
@endpush

@section('content')
    <div class="row h-100">
        <!-- Product Catalog -->
        <div class="col-md-8 h-100 d-flex flex-column">
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-th me-2 text-warning"></i>Catálogo</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fas fa-search text-muted"></i></span>
                                <input type="text" id="searchInput" class="form-control border-start-0"
                                    placeholder="Buscar por nombre o código...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 overflow-auto" id="productList"
                style="max-height: calc(100vh - 180px);">
                @foreach($products as $product)
                            <div class="col product-item" data-name="{{ strtolower($product->name) }}"
                                data-code="{{ strtolower($product->code) }}">
                                <div class="card product-card" onclick="selectTalla({{ json_encode([
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'stock' => $product->stock,
                        'image' => $product->image,
                        'productoTallas' => $product->productoTallas->map(fn($pt) => [
                            'id' => $pt->id,
                            'talla_id' => $pt->talla_id,
                            'talla' => $pt->talla->nombre,
                            'color_id' => $pt->color_id,
                            'color' => $pt->color?->name,
                            'hex' => $pt->color?->hex_code,
                            'stock' => $pt->stock,
                        ])
                    ]) }})">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top product-img"
                                            alt="{{ $product->name }}">
                                    @else
                                        <div class="card-img-top product-img d-flex align-items-center justify-content-center text-muted">
                                            <i class="fas fa-tshirt fa-3x"></i>
                                        </div>
                                    @endif
                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title text-truncate mb-1" title="{{ $product->name }}">{{ $product->name }}</h6>
                                        <div class="fw-bold text-primary">S/ {{ number_format($product->price, 2) }}</div>
                                        @if($product->productoTallas->count() > 0)
                                            <small class="text-success"><i class="fas fa-ruler-horizontal me-1"></i>Ver tallas</small>
                                        @else
                                            <small class="text-muted">Stock: {{ $product->stock }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                @endforeach
            </div>
        </div>

        <!-- Cart Panel -->
        <div class="col-md-4">
            <div class="cart-panel">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-shopping-cart me-2"></i>Carrito</h5>
                    <button class="btn btn-sm btn-outline-danger" onclick="clearCart()">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>

                <!-- Client Selector -->
                <div class="p-3 bg-light border-bottom">
                    <label class="small fw-bold text-muted mb-1">CLIENTE (Opcional)</label>
                    <select class="form-select form-select-sm" id="clientSelect">
                        <option value="">-- Venta Rápida --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}
                                ({{ $client->document_number ?? $client->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="cart-items" id="cartContainer">
                    <!-- Cart items injected here -->
                    <div class="text-center py-5 text-muted empty-cart-message">
                        <i class="fas fa-shopping-basket fa-3x mb-3 opacity-50"></i>
                        <p>El carrito está vacío</p>
                    </div>
                </div>

                <div class="cart-footer">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold" id="cartSubtotal">S/ 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">IGV (18%)</span>
                        <span class="fw-bold" id="cartTax">S/ 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 fs-4 border-top pt-2">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold text-primary" id="cartTotal">S/ 0.00</span>
                    </div>
                    <button class="btn btn-primary-custom w-100 py-2 fs-5" onclick="openPaymentModal()" id="btnCheckout"
                        disabled>
                        <i class="fas fa-check-circle me-2"></i> Procesar Venta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Date/Time for receipt -->
    <input type="hidden" id="currentDate" value="{{ now()->format('Y-m-d') }}">

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Confirmar Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4 text-center">
                        <h2 class="fw-bold text-primary" id="modalTotalAmount">S/ 0.00</h2>
                        <p class="text-muted">Total a Pagar</p>
                    </div>

                    <div id="paymentMethodsContainer">
                        <div class="payment-row mb-3 row g-2">
                            <div class="col-6">
                                <select class="form-select payment-method-select" onchange="checkYape(this)">
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <input type="number" class="form-control payment-amount-input" step="0.01"
                                    placeholder="Monto">
                            </div>
                            <div class="col-2">
                                <!-- First row can't be deleted, logic handled in JS -->
                            </div>
                        </div>
                    </div>

                    <!-- YAPE QR CONTAINER -->
                    <div id="yape-qr-container" class="text-center p-3 mb-3 bg-light rounded border d-none">
                        <h6 class="fw-bold text-purple mb-2" style="color: #742774;">¡Escanea para Yapear!</h6>
                        <img src="{{ asset('images/yape_qr.png') }}" alt="QR Yape"
                            style="max-width: 200px; border-radius: 10px;">
                        <p class="small text-muted mt-2">Titular: Raul Alexis Campos Sanchez</p>
                    </div>

                    <div class="text-end mb-3">
                        <button class="btn btn-sm btn-outline-secondary" onclick="addPaymentRow()">
                            <i class="fas fa-plus"></i> Otro método
                        </button>
                    </div>

                    <div class="alert alert-warning py-2 small d-none" id="paymentAlert">
                        <i class="fas fa-exclamation-triangle me-1"></i> El monto no cubre el total.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="submitSale()">
                        <i class="fas fa-print me-2"></i> Finalizar Venta
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal selección de talla --}}
    <div class="modal fade" id="tallaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="tallaModalNombre"></h5>
                        <small class="text-muted" id="tallaModalPrecio"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-muted small mb-2"><i class="fas fa-palette me-1"></i>1. Selecciona el Color:</p>
                    <div class="d-flex flex-wrap gap-2 mb-3" id="coloresBotones"></div>

                    <p class="text-muted small mb-2 d-none" id="labelTalla"><i class="fas fa-hand-pointer me-1"></i>2.
                        Selecciona la Talla:</p>
                    <div class="d-flex flex-wrap gap-2" id="tallasBotones"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let cart = [];
        let products = @json($products);
        let tallaModalBS = null;

        // ─ Selector de variante: se llama al hacer clic en un producto ─
        function selectTalla(product) {
            if (!product.productoTallas || product.productoTallas.length === 0) {
                addToCart(product, null, null, null, null);
                return;
            }
            document.getElementById('tallaModalNombre').textContent = product.name;
            document.getElementById('tallaModalPrecio').textContent = 'S/ ' + parseFloat(product.price).toFixed(2) + ' por unidad';

            const colorContainer = document.getElementById('coloresBotones');
            const tallaContainer = document.getElementById('tallasBotones');
            const labelTalla = document.getElementById('labelTalla');

            colorContainer.innerHTML = '';
            tallaContainer.innerHTML = '';
            labelTalla.classList.add('d-none');

            // Agrupar variantes por color para mostrar selectores
            const colorsMap = new Map();
            product.productoTallas.forEach(pt => {
                if (!colorsMap.has(pt.color_id)) {
                    colorsMap.set(pt.color_id, {
                        id: pt.color_id,
                        name: pt.color || 'Sin color',
                        hex: pt.hex,
                        variants: []
                    });
                }
                colorsMap.get(pt.color_id).variants.push(pt);
            });

            colorsMap.forEach(c => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `btn btn-outline-dark d-flex align-items-center gap-2 px-3 py-2 fw-bold`;
                btn.innerHTML = `
                        ${c.hex ? `<span style="width:14px; height:14px; border-radius:50%; background:${c.hex}; border:1px solid #ccc;"></span>` : ''}
                        ${c.name}
                    `;
                btn.onclick = () => {
                    // Highlight selected color
                    colorContainer.querySelectorAll('button').forEach(b => b.classList.replace('btn-dark', 'btn-outline-dark'));
                    btn.classList.replace('btn-outline-dark', 'btn-dark');

                    // Show talla buttons for this color
                    labelTalla.classList.remove('d-none');
                    tallaContainer.innerHTML = '';
                    c.variants.forEach(v => {
                        const agotado = v.stock <= 0;
                        const tBtn = document.createElement('button');
                        tBtn.type = 'button';
                        tBtn.className = `btn ${agotado ? 'btn-outline-danger' : 'btn-outline-primary'} px-4 py-2 fw-bold`;
                        tBtn.disabled = agotado;
                        tBtn.innerHTML = `${v.talla}<br><small style="font-size:.6rem">${agotado ? 'Agotado' : v.stock + ' uds.'}</small>`;
                        tBtn.onclick = () => {
                            addToCart(product, v.talla_id, v.talla, v.color_id, v.color);
                            tallaModalBS.hide();
                        };
                        tallaContainer.appendChild(tBtn);
                    });
                };
                colorContainer.appendChild(btn);
            });

            tallaModalBS = new bootstrap.Modal(document.getElementById('tallaModal'));
            tallaModalBS.show();
        }

        // ─ Añadir al carrito (producto + talla + color opcional) ─
        function addToCart(product, tallaId, tallaNombre, colorId, colorNombre) {
            const key = `${product.id}-${tallaId ?? 0}-${colorId ?? 0}`;

            let maxStock = product.stock;
            if (tallaId || colorId) {
                const variant = product.productoTallas?.find(v => v.talla_id == tallaId && v.color_id == colorId);
                maxStock = variant ? variant.stock : 0;
            }

            let existing = cart.find(item => item.key === key);
            if (existing) {
                if (existing.quantity + 1 > maxStock) {
                    Swal.fire('Stock Insuficiente', 'No hay más unidades disponibles de esta variante', 'warning');
                    return;
                }
                existing.quantity++;
            } else {
                if (maxStock < 1) {
                    Swal.fire('Stock Insuficiente', 'Variante agotada', 'warning');
                    return;
                }
                cart.push({
                    key: key,
                    id: product.id,
                    talla_id: tallaId,
                    talla: tallaNombre,
                    color_id: colorId,
                    color: colorNombre,
                    name: product.name,
                    price: parseFloat(product.price),
                    quantity: 1,
                    maxStock: maxStock
                });
            }
            renderCart();
        }

        function checkYape(selectElement) {
            let text = selectElement.options[selectElement.selectedIndex].text.toLowerCase();
            let qrContainer = document.getElementById('yape-qr-container');

            // Logic: If ANY row selects Yape, show QR. If NONE select Yape, hide.
            // Simplified: If THIS is Yape, show.
            // Better: Check all selects.
            let allSelects = document.querySelectorAll('.payment-method-select');
            let hasYape = false;
            allSelects.forEach(sel => {
                if (sel.options[sel.selectedIndex].text.toLowerCase().includes('yape')) {
                    hasYape = true;
                }
            });

            if (hasYape) {
                qrContainer.classList.remove('d-none');
            } else {
                qrContainer.classList.add('d-none');
            }
        }

        // Search Filter
        document.getElementById('searchInput').addEventListener('keyup', function (e) {
            let term = e.target.value.toLowerCase();
            let items = document.querySelectorAll('.product-item');
            items.forEach(item => {
                let name = item.dataset.name;
                let code = item.dataset.code;
                if (name.includes(term) || code.includes(term)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        function renderCart() {
            let container = document.getElementById('cartContainer');
            let btn = document.getElementById('btnCheckout');

            if (cart.length === 0) {
                container.innerHTML = `
                                                <div class="text-center py-5 text-muted empty-cart-message">
                                                    <i class="fas fa-shopping-basket fa-3x mb-3 opacity-50"></i>
                                                    <p>El carrito está vacío</p>
                                                </div>`;
                btn.disabled = true;
                updateTotals(0);
                return;
            }

            let html = '<ul class="list-group list-group-flush">';
            let total = 0;

            cart.forEach((item, index) => {
                let subtotal = item.price * item.quantity;
                total += subtotal;

                let variantLabels = [];
                if (item.talla) variantLabels.push(`<span class="badge bg-secondary ms-1">${item.talla}</span>`);
                if (item.color) variantLabels.push(`<span class="badge bg-info text-dark ms-1">${item.color}</span>`);

                html += `
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <div class="ms-2 me-auto">
                                                        <div class="fw-bold">${item.name} ${variantLabels.join('')}</div>
                                                        <div class="text-muted small">S/ ${item.price.toFixed(2)} x ${item.quantity}</div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="fw-bold">S/ ${subtotal.toFixed(2)}</span>
                                                        <button class="btn btn-sm text-danger" onclick="removeFromCart(${index})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </li>
                                            `;
            });
            html += '</ul>';
            container.innerHTML = html;
            btn.disabled = false;
            updateTotals(total);
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function clearCart() {
            cart = [];
            renderCart();
        }

        function updateTotals(total) {
            // Simple logic: Total is mostly what matters in PERU POS. 
            // Tax is usually included in price.
            let subtotal = total / 1.18;
            let tax = total - subtotal;

            document.getElementById('cartSubtotal').textContent = 'S/ ' + subtotal.toFixed(2);
            document.getElementById('cartTax').textContent = 'S/ ' + tax.toFixed(2);
            document.getElementById('cartTotal').textContent = 'S/ ' + total.toFixed(2);
            document.getElementById('modalTotalAmount').textContent = 'S/ ' + total.toFixed(2);
        }

        function openPaymentModal() {
            let total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            // Reset payment inputs
            document.querySelector('.payment-amount-input').value = total.toFixed(2);
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        }

        function addPaymentRow() {
            let container = document.getElementById('paymentMethodsContainer');
            let div = document.createElement('div');
            div.className = 'payment-row mb-3 row g-2';
            div.innerHTML = `
                                            <div class="col-6">
                                                <select class="form-select payment-method-select">
                                                    @foreach($paymentMethods as $method)
                                                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <input type="number" class="form-control payment-amount-input" step="0.01" placeholder="Monto">
                                            </div>
                                            <div class="col-2">
                                                <button class="btn btn-outline-danger w-100" onclick="this.closest('.row').remove()">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        `;
            container.appendChild(div);
        }

        // Search Filter
        document.getElementById('searchInput').addEventListener('keyup', function (e) {
            let term = e.target.value.toLowerCase();
            let items = document.querySelectorAll('.product-item');
            items.forEach(item => {
                let name = item.dataset.name;
                let code = item.dataset.code;
                if (name.includes(term) || code.includes(term)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        function submitSale() {
            let clientId = document.getElementById('clientSelect').value;
            let payments = [];
            let totalPayment = 0;
            let rows = document.querySelectorAll('.payment-row');

            rows.forEach(row => {
                let methodId = row.querySelector('.payment-method-select').value;
                let amount = parseFloat(row.querySelector('.payment-amount-input').value) || 0;
                if (amount > 0) {
                    payments.push({ method_id: methodId, amount: amount });
                    totalPayment += amount;
                }
            });

            let cartTotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            if (Math.abs(totalPayment - cartTotal) > 0.1) {
                Swal.fire('Error', `El monto pagado (S/ ${totalPayment.toFixed(2)}) no coincide con el total (S/ ${cartTotal.toFixed(2)})`, 'error');
                return;
            }

            // Mapear carrito incluyendo talla_id y color_id
            const cartPayload = cart.map(item => ({
                id: item.id,
                talla_id: item.talla_id,
                color_id: item.color_id,
                quantity: item.quantity,
                price: item.price,
            }));

            fetch('{{ route("pos.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    cart: cartPayload,
                    client_id: clientId,
                    payments: payments
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Venta Exitosa', 'La venta se ha registrado correctamente.', 'success')
                            .then(() => {
                                location.reload();
                            });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Hubo un problema al procesar la venta.', 'error');
                    console.error(error);
                });
        }
    </script>
@endpush