<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.shipment.label_title') }} - {{ $shipment->tracking_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page {
            size: 100mm 150mm;
            margin: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f1f5f9;
            color: #000;
            margin: 0;
            padding: 20px 0;
        }
        .label-container {
            width: 100mm;
            min-height: 148mm;
            max-height: 150mm;
            margin: 0 auto;
            background: #fff;
            border: 2px solid #000;
            box-sizing: border-box;
            padding: 4mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 10pt;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .border-b-2 { border-bottom: 2px solid #000; }
        .border-t-2 { border-top: 2px solid #000; }
        .border-r-2 { border-right: 2px solid #000; }
        .border-l-2 { border-left: 2px solid #000; }
        
        .barcode-stripes {
            display: flex;
            align-items: stretch;
            justify-content: center;
            height: 38px;
            gap: 2px;
            margin: 4px 0;
        }
        .b-bar {
            background-color: #000;
            width: 2px;
        }
        .b-bar-wide {
            background-color: #000;
            width: 5px;
        }
        .b-space {
            background-color: transparent;
            width: 2px;
        }
        .b-space-wide {
            background-color: transparent;
            width: 4px;
        }

        .routing-box {
            font-size: 18pt;
            font-weight: 900;
            letter-spacing: 2px;
            line-height: 1;
        }

        .service-badge {
            background: #000;
            color: #fff;
            padding: 2px 6px;
            font-size: 8pt;
            font-weight: bold;
            display: inline-block;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .label-container {
                box-shadow: none;
                border: 2px solid #000;
                margin: 0;
                width: 100%;
                height: 100%;
            }
        }
    </style>
</head>
<body>

<div class="no-print text-center mb-3">
    <div class="container d-flex justify-content-center gap-2">
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4 fw-bold">
            <i class="bi bi-printer-fill me-1"></i> {{ __('messages.shipment.label_print') }}
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-x-lg me-1"></i> Cerrar
        </button>
    </div>
    <div class="text-muted small mt-1">Formato oficial térmico A6 (100mm x 150mm) para impresoras Zebra / Dymo</div>
</div>

<div class="label-container">
    <!-- Header: Transportista & Routing Code -->
    <div class="border-b-2 pb-2">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="fw-bold fs-6 tracking-wide text-uppercase">CORREOS EXPRESS</div>
                <div class="small fw-semibold text-muted">Reposa+ Sleep Logistics S.L.</div>
            </div>
            <div class="text-end">
                <span class="service-badge">{{ strtoupper($shipment->service_name) }}</span>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="routing-box text-start">
                {{ $labelData['routing_code'] ?? 'MAD-' . ($shipment->zip_code ?: '28001') . '-Z01' }}
            </div>
            <div class="text-end small fw-bold">
                REF: PED-#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
            </div>
        </div>
    </div>

    <!-- Barcode 128 simulado -->
    <div class="text-center py-2 border-b-2">
        <div class="barcode-stripes" aria-hidden="true">
            <div class="b-bar-wide"></div><div class="b-space"></div><div class="b-bar"></div><div class="b-space-wide"></div>
            <div class="b-bar-wide"></div><div class="b-space"></div><div class="b-bar"></div><div class="b-space"></div>
            <div class="b-bar"></div><div class="b-space-wide"></div><div class="b-bar-wide"></div><div class="b-space"></div>
            <div class="b-bar"></div><div class="b-space"></div><div class="b-bar-wide"></div><div class="b-space-wide"></div>
            <div class="b-bar-wide"></div><div class="b-space"></div><div class="b-bar"></div><div class="b-space"></div>
            <div class="b-bar"></div><div class="b-space-wide"></div><div class="b-bar-wide"></div><div class="b-space"></div>
            <div class="b-bar-wide"></div><div class="b-space"></div><div class="b-bar"></div><div class="b-space"></div>
            <div class="b-bar"></div><div class="b-space-wide"></div><div class="b-bar-wide"></div><div class="b-space"></div>
            <div class="b-bar-wide"></div><div class="b-space"></div><div class="b-bar"></div><div class="b-space"></div>
            <div class="b-bar"></div><div class="b-space-wide"></div><div class="b-bar-wide"></div><div class="b-space"></div>
            <div class="b-bar-wide"></div><div class="b-space"></div><div class="b-bar"></div><div class="b-space-wide"></div>
            <div class="b-bar-wide"></div><div class="b-space"></div><div class="b-bar"></div><div class="b-space"></div>
            <div class="b-bar"></div><div class="b-space-wide"></div><div class="b-bar-wide"></div><div class="b-space"></div>
        </div>
        <div class="fw-bold tracking-widest font-monospace" style="font-size: 11pt; letter-spacing: 3px;">
            {{ $shipment->tracking_number }}
        </div>
    </div>

    <!-- Destinatario & Remitente -->
    <div class="row g-0 flex-grow-1 border-b-2 py-2">
        <div class="col-7 pe-2 border-r-2">
            <div class="text-uppercase small fw-bold text-muted mb-1" style="font-size: 7.5pt;">DESTINATARIO:</div>
            <div class="fw-bold fs-6 text-uppercase">{{ $shipment->recipient_name }}</div>
            <div class="small fw-semibold mt-1">{{ $shipment->street }}</div>
            <div class="fs-6 fw-bold mt-1">{{ $shipment->zip_code }} {{ strtoupper($shipment->city) }}</div>
            <div class="small text-uppercase fw-semibold">{{ $shipment->province }} ({{ $shipment->country }})</div>
            <div class="small mt-2"><strong>TEL:</strong> {{ $shipment->recipient_phone ?: 'No especificado' }}</div>
        </div>
        <div class="col-5 ps-2">
            <div class="text-uppercase small fw-bold text-muted mb-1" style="font-size: 7.5pt;">REMITENTE:</div>
            <div class="fw-bold small">REPOSA+ SLEEP WELLNESS</div>
            <div style="font-size: 7.5pt;" class="mt-1">
                Hub Logístico Coslada<br>
                Av. de la Cañada 42, Nave 7<br>
                28823 Coslada (Madrid)<br>
                TEL: +34 910 000 737
            </div>
            <div class="mt-3 pt-2 border-t-2" style="font-size: 7pt;">
                <div><strong>FECHA:</strong> {{ $shipment->created_at->format('d/m/Y') }}</div>
                <div><strong>BULTO:</strong> 1/1</div>
            </div>
        </div>
    </div>

    <!-- Datos del Bulto y Logística -->
    <div class="pt-2">
        <div class="row g-1 text-center" style="font-size: 8pt;">
            <div class="col-4 border-r-2">
                <div class="text-muted fw-bold">PESO BASCULA</div>
                <div class="fw-bold fs-6">1.80 kg</div>
            </div>
            <div class="col-4 border-r-2">
                <div class="text-muted fw-bold">VOLUMÉTRICO</div>
                <div class="fw-bold fs-6">3.60 kg</div>
            </div>
            <div class="col-4">
                <div class="text-muted fw-bold">DIMENSIONES</div>
                <div class="fw-bold fs-6">60x40x15</div>
            </div>
        </div>
        <div class="mt-2 pt-1 border-t-2 text-center" style="font-size: 6.5pt; line-height: 1.1;">
            TRATAR CON CUIDADO. PRODUCTO TEXTIL ERGONÓMICO EN CAJA SELLADA AL VACÍO. NO PERFORAR NI DOBLAR.
        </div>
    </div>
</div>

</body>
</html>
