<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta #{{ $sale->numero_boleta }} - StyleBox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #fff;
            color: #000;
        }

        .boleta-container {
            max-width: 800px;
            margin: 30px auto;
            border: 1px solid #eee;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 2rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: -1px;
        }

        .logo span {
            color: #d4a017;
        }

        .boleta-info {
            text-align: right;
        }

        .boleta-number {
            font-size: 1.25rem;
            font-weight: 700;
            color: #d4a017;
        }

        .table thead th {
            background: #f8f9fa;
            border-top: none;
        }

        .total-section {
            border-top: 2px solid #000;
            padding-top: 15px;
            margin-top: 15px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .boleta-container {
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 0;
                width: 100%;
                max-width: 100%;
            }

            body {
                font-size: 12pt;
            }
        }
    </style>
</head>

<body>
    <div class="no-print text-center my-4">
        <button onclick="window.print()" class="btn btn-dark btn-lg shadow-sm">
            <i class="fas fa-print me-2"></i>Imprimir Comprobante
        </button>
        <a href="{{ route('checkout.confirmation', $sale) }}" class="btn btn-outline-dark btn-lg ms-2">
            Volver
        </a>
    </div>

    <div class="boleta-container">
        <div class="header d-flex justify-content-between align-items-center">
            <div class="logo">Style<span>Box</span></div>
            <div class="boleta-info">
                <div class="h5 mb-0 text-uppercase fw-bold">Boleta de Venta Electrónica</div>
                <div class="boleta-number">{{ $sale->numero_boleta }}</div>
                <div class="small text-muted">Fecha: {{ $sale->date->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-6">
                <h6 class="text-uppercase fw-bold small text-muted mb-3">Emisor</h6>
                <div class="fw-bold">STYLEBOX S.A.C.</div>
                <div>RUC: 20123456789</div>
                <div>Av. Principal 123, Lima</div>
                <div>Tel: (01) 456-7890</div>
            </div>
            <div class="col-6 text-end">
                <h6 class="text-uppercase fw-bold small text-muted mb-3">Cliente</h6>
                <div class="fw-bold">{{ $sale->client->name ?? $sale->buyer->name }}</div>
                <div>DNI/RUC: {{ $sale->client->dni_ruc ?? 'N/A' }}</div>
                <div>Correo: {{ $sale->client->email ?? $sale->buyer->email }}</div>
            </div>
        </div>

        <table class="table align-middle mb-4">
            <thead>
                <tr>
                    <th>Cant.</th>
                    <th>Descripción</th>
                    <th class="text-end">Precio Unit.</th>
                    <th class="text-end">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->details as $detail)
                    <tr>
                        <td>{{ (int) $detail->quantity }}</td>
                        <td>{{ $detail->product->name }}</td>
                        <td class="text-end">S/ {{ number_format($detail->unit_price, 2) }}</td>
                        <td class="text-end">S/ {{ number_format($detail->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row justify-content-end">
            <div class="col-5">
                <div class="d-flex justify-content-between mb-2">
                    <span>Op. Gravadas:</span>
                    <span>S/ {{ number_format($sale->total / 1.18, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>I.G.V. (18%):</span>
                    <span>S/ {{ number_format($sale->total - ($sale->total / 1.18), 2) }}</span>
                </div>
                <div class="total-section d-flex justify-content-between">
                    <span class="h5 mb-0 fw-bold">TOTAL:</span>
                    <span class="h5 mb-0 fw-bold">S/ {{ number_format($sale->total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="mt-5 pt-5 border-top small text-muted text-center">
            <p>Representación impresa de una Boleta de Venta Electrónica.</p>
            <p>¡Gracias por su compra en StyleBox! Su estilo es nuestra prioridad.</p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>

</html>