<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1a1a2e; font-size: 13px; line-height: 1.5; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; padding: 30px 40px; border-bottom: 3px solid #4338ca; }
        .brand h1 { font-size: 26px; color: #4338ca; font-weight: 800; letter-spacing: -0.5px; }
        .brand p { color: #6b7280; font-size: 11px; margin-top: 2px; }
        .invoice-meta { text-align: right; }
        .invoice-meta h2 { font-size: 18px; color: #4338ca; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 6px; }
        .invoice-meta p { font-size: 11px; color: #6b7280; }

        .body { padding: 30px 40px; }

        .parties { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .party { width: 48%; }
        .party h3 { font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: #9ca3af; margin-bottom: 6px; }
        .party p { font-size: 12px; color: #374151; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead th { background: #4338ca; color: #fff; padding: 10px 14px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        thead th:last-child, thead th:nth-child(3), thead th:nth-child(4) { text-align: right; }
        tbody td { padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        tbody td:last-child, tbody td:nth-child(3), tbody td:nth-child(4) { text-align: right; }
        tbody tr:nth-child(even) { background: #f9fafb; }

        .totals { display: flex; justify-content: flex-end; margin-bottom: 30px; }
        .totals-box { width: 260px; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; color: #6b7280; }
        .totals-row.total { border-top: 2px solid #4338ca; padding-top: 10px; margin-top: 4px; font-size: 16px; font-weight: 700; color: #4338ca; }

        .footer { text-align: center; padding: 20px 40px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 10px; }
        .footer p { margin-bottom: 2px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">
            <h1>Reposa+</h1>
            <p>Almohadas Ergonómicas</p>
        </div>
        <div class="invoice-meta">
            <h2>Factura</h2>
            <p><strong>Nº:</strong> #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
            @if($order->stripe_session_id)
                <p><strong>Sesión:</strong> {{ substr($order->stripe_session_id, 0, 20) }}…</p>
            @endif
        </div>
    </div>

    <div class="body">
        <div class="parties">
            <div class="party">
                <h3>Facturado a</h3>
                <p><strong>{{ $order->user->name }}</strong></p>
                <p>{{ $order->user->email }}</p>
                @if($order->user->addresses && $order->user->addresses->isNotEmpty())
                    @php $addr = $order->user->addresses->first(); @endphp
                    <p>{{ $addr->street }}</p>
                    <p>{{ $addr->zip_code }}, {{ $addr->city }}</p>
                @endif
            </div>
            <div class="party">
                <h3>Método de pago</h3>
                <p><strong>Stripe Checkout</strong></p>
                @if($order->user->pm_last_four)
                    <p>Tarjeta **** {{ $order->user->pm_last_four }}</p>
                @endif
                <p>Estado: <strong style="color: #16a34a;">Pagado</strong></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Ud.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price_at_purchase, 2) }}€</td>
                        <td><strong>{{ number_format($item->price_at_purchase * $item->quantity, 2) }}€</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-box">
                <div class="totals-row">
                    <span>Subtotal</span>
                    <span>{{ number_format($order->total_amount, 2) }}€</span>
                </div>
                <div class="totals-row">
                    <span>IVA (21%)</span>
                    <span>Incluido</span>
                </div>
                <div class="totals-row total">
                    <span>Total</span>
                    <span>{{ number_format($order->total_amount, 2) }}€</span>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>Reposa+</strong> — Almohadas Ergonómicas de Alta Gama</p>
        <p>Gracias por tu compra. Para consultas, contacta con nosotros.</p>
    </div>
</body>
</html>
