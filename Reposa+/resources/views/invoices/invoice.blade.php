<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.invoice.title') }} #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} - Reposa+</title>
    <style>
        /* Dompdf strictly compatible styles: pure table layout, no flexbox or grid */
        @page {
            size: A4 portrait;
            margin: 14mm 16mm 14mm 16mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.45;
            background: #ffffff;
        }

        /* Layout Tables */
        table.layout-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin: 0;
            padding: 0;
        }

        table.layout-table td {
            vertical-align: top;
            padding: 0;
        }

        /* Brand & Header */
        .header-container {
            border-bottom: 2px solid #182447;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .brand-title {
            font-size: 24px;
            font-weight: 800;
            color: #182447;
            letter-spacing: -0.5px;
            margin-bottom: 2px;
        }

        .brand-title span {
            color: #4f46e5;
        }

        .brand-subtitle {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            margin-bottom: 4px;
        }

        .brand-details {
            font-size: 9.5px;
            color: #64748b;
            line-height: 1.4;
        }

        .invoice-badge-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            text-align: right;
            width: 240px;
            margin-left: auto;
        }

        .invoice-heading {
            font-size: 13px;
            font-weight: 700;
            color: #182447;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 6px;
        }

        .meta-line {
            font-size: 10px;
            color: #475569;
            margin-bottom: 3px;
        }

        .meta-line strong {
            color: #0f172a;
        }

        /* Party / Billed Blocks */
        .parties-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }

        .party-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
        }

        .party-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #4f46e5;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }

        .party-name {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .party-text {
            font-size: 10px;
            color: #475569;
            line-height: 1.35;
        }

        /* Items Table */
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        table.items-table thead th {
            background-color: #182447;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 7px 10px;
            text-align: left;
        }

        table.items-table thead th.text-end {
            text-align: right;
        }

        table.items-table thead th.text-center {
            text-align: center;
        }

        table.items-table tbody td {
            padding: 8px 10px;
            font-size: 10.5px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
            vertical-align: middle;
        }

        table.items-table tbody td.text-end {
            text-align: right;
        }

        table.items-table tbody td.text-center {
            text-align: center;
        }

        table.items-table tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .item-title {
            font-weight: 600;
            color: #0f172a;
        }

        .item-subtitle {
            font-size: 9px;
            color: #64748b;
        }

        /* Totals / Fiscal Breakdown */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }

        .fiscal-notes {
            font-size: 9.5px;
            color: #64748b;
            line-height: 1.4;
            padding-right: 20px;
        }

        .fiscal-notes strong {
            color: #0f172a;
        }

        .summary-box {
            width: 250px;
            margin-left: auto;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #ffffff;
        }

        table.summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.summary-table td {
            padding: 5px 10px;
            font-size: 10px;
            color: #475569;
        }

        table.summary-table td.val {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }

        table.summary-table tr.total-row td {
            background-color: #182447;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            padding: 8px 10px;
            border-top: 1px solid #182447;
        }

        table.summary-table tr.total-row td.val {
            color: #ffffff;
            font-size: 13px;
        }

        /* Footer */
        .footer-container {
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            margin-top: 20px;
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            line-height: 1.45;
        }

        .footer-legal {
            margin-bottom: 3px;
            color: #64748b;
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }
    </style>
</head>
<body>
    {{-- Header Section --}}
    <div class="header-container">
        <table class="layout-table">
            <tr>
                <td style="width: 58%;">
                    <div class="brand-title">Reposa<span>+</span></div>
                    <div class="brand-subtitle">{{ __('messages.invoice.company') }} &middot; Ergonomía Avanzada</div>
                    <div class="brand-details">
                        {{ __('messages.invoice.merchant_details') }}<br>
                        Email: soporte@reposaplus.com &middot; Web: www.reposaplus.es
                    </div>
                </td>
                <td style="width: 42%;">
                    <div class="invoice-badge-box">
                        <div class="invoice-heading">{{ __('messages.invoice.title') }}</div>
                        <div class="meta-line">
                            {{ __('messages.invoice.number') }} <strong>FAC-{{ $order->created_at->format('Y') }}-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
                        </div>
                        <div class="meta-line">
                            {{ __('messages.invoice.date') }} <strong class="tabular-nums">{{ $order->created_at->format('d/m/Y') }}</strong>
                        </div>
                        @if($order->stripe_session_id)
                            <div class="meta-line" style="font-size: 8.5px;">
                                {{ __('messages.invoice.session') }} <strong>{{ substr($order->stripe_session_id, 0, 20) }}…</strong>
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Parties Section (Billed to & Payment Method) --}}
    <table class="parties-table">
        <tr>
            <td style="width: 48.5%;">
                <div class="party-card">
                    <div class="party-title">{{ __('messages.invoice.customer_data') }}</div>
                    <div class="party-name">{{ $order->customer_name }}</div>
                    <div class="party-text">{{ $order->customer_email }}</div>
                    @if($order->shipping_street)
                        <div class="party-text">{{ $order->shipping_street }}</div>
                        <div class="party-text">{{ $order->shipping_zip_code }} {{ $order->shipping_city }} @if($order->shipping_province)({{ $order->shipping_province }})@endif</div>
                    @elseif($order->user && $order->user->addresses && $order->user->addresses->isNotEmpty())
                        @php $addr = $order->user->addresses->first(); @endphp
                        <div class="party-text">{{ $addr->street }}</div>
                        <div class="party-text">{{ $addr->zip_code }} {{ $addr->city }} (España)</div>
                    @else
                        <div class="party-text">Cliente Particular (Venta a Distancia)</div>
                    @endif
                </div>
            </td>
            <td style="width: 3%;"></td>
            <td style="width: 48.5%;">
                <div class="party-card">
                    <div class="party-title">{{ __('messages.invoice.payment_delivery') }}</div>
                    <div class="party-name">{{ __('messages.invoice.payment_method') }}: Stripe Checkout</div>
                    @if($order->user && $order->user->pm_last_four)
                        <div class="party-text">{{ __('messages.invoice.card_ending') }} {{ $order->user->pm_last_four }}</div>
                    @else
                        <div class="party-text">Tarjeta de Crédito / Débito Segura (SSL)</div>
                    @endif
                    <div class="party-text">
                        <strong style="color: #059669;">&check; {{ __('messages.invoice.status_paid') }}</strong> &middot; {{ __('messages.invoice.shipping_included') }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Line Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">{{ __('messages.invoice.product') }}</th>
                <th class="text-center" style="width: 12%;">{{ __('messages.invoice.quantity') }}</th>
                <th class="text-end" style="width: 18%;">{{ __('messages.invoice.unit_price') }}</th>
                <th class="text-end" style="width: 20%;">{{ __('messages.invoice.subtotal') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
                @php
                    $lineTotal = $item->price_at_purchase * $item->quantity;
                    $lineBase = $lineTotal / 1.21;
                @endphp
                <tr>
                    <td>
                        <div class="item-title">{{ $item->product->name }}</div>
                        @if($item->product->firmness || $item->product->dimensions)
                            <div class="item-subtitle">
                                @if($item->product->firmness) Firmeza: {{ $item->product->firmness }} @endif
                                @if($item->product->dimensions) &middot; {{ $item->product->dimensions }} @endif
                            </div>
                        @endif
                    </td>
                    <td class="text-center tabular-nums fw-bold">{{ $item->quantity }}</td>
                    <td class="text-end tabular-nums">{{ number_format($item->price_at_purchase, 2) }} €</td>
                    <td class="text-end tabular-nums fw-bold">{{ number_format($lineTotal, 2) }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals & Fiscal Breakdown --}}
    @php
        $total = (float) $order->total_amount;
        $baseImponible = $total / 1.21;
        $cuotaIva = $total - $baseImponible;
    @endphp
    <table class="totals-table">
        <tr>
            <td style="width: 50%; vertical-align: bottom;">
                <div class="fiscal-notes">
                    <strong>Desglose Tributario:</strong><br>
                    Operación sujeta al Régimen General del Impuesto sobre el Valor Añadido (IVA 21%).<br>
                    {{ __('messages.invoice.registry') }}
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="summary-box">
                    <table class="summary-table">
                        <tr>
                            <td>{{ __('messages.invoice.tax_base') }}</td>
                            <td class="val tabular-nums">{{ number_format($baseImponible, 2) }} €</td>
                        </tr>
                        <tr>
                            <td>{{ __('messages.invoice.tax_rate') }}</td>
                            <td class="val tabular-nums">{{ number_format($cuotaIva, 2) }} €</td>
                        </tr>
                        <tr>
                            <td>{{ __('messages.invoice.shipping') }}</td>
                            <td class="val" style="color: #059669;">0,00 € (Gratis)</td>
                        </tr>
                        <tr class="total-row">
                            <td>{{ __('messages.invoice.total') }}</td>
                            <td class="val tabular-nums">{{ number_format($total, 2) }} €</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- Corporate Legal Footer --}}
    <div class="footer-container">
        <div class="footer-legal">
            {{ __('messages.invoice.guarantee_notice') }}
        </div>
        <div>
            Reposa+ &middot; {{ __('messages.invoice.footer_title') }} &middot; {{ __('messages.invoice.footer_thanks') }}
        </div>
    </div>
</body>
</html>
