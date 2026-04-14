<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $billing->invoice_number ?? 'Khayra Physio' }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --teal: #2f7c7a;
            --teal-dark: #245f67;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #e5ecea;
            --soft: #f7faf9;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f5;
            color: var(--text);
            padding: 24px;
        }

        .page {
            max-width: 980px;
            margin: 0 auto;
        }

        .top-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .action-link,
        .print-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 11px 15px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 800;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: Arial, sans-serif;
        }

        .action-link {
            background: #ffffff;
            color: var(--teal);
            border-color: #dfe8e6;
        }

        .print-btn {
            background: linear-gradient(135deg, #3d8a89 0%, var(--teal) 100%);
            color: #ffffff;
            border: none;
            box-shadow: 0 10px 20px rgba(47, 124, 122, 0.16);
        }

        .invoice-shell {
            background: #ffffff;
            border: 1px solid #e7eceb;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.06);
        }

        .invoice-header {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 18px;
            padding: 24px 24px 18px;
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .invoice-header::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.12), transparent 30%),
                linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
            background-size: auto, 48px 48px, 48px 48px;
            pointer-events: none;
        }

        .invoice-header > * {
            position: relative;
            z-index: 1;
        }

        .company-block {
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .company-logo {
            width: 58px;
            height: 58px;
            object-fit: contain;
            border-radius: 14px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.16);
            padding: 6px;
            flex: 0 0 auto;
        }

        .company-name {
            margin: 0;
            font-size: 32px;
            line-height: 1.05;
            font-weight: 800;
            color: #ffffff;
        }

        .company-tagline {
            margin: 6px 0 0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: rgba(255,255,255,.82);
            font-weight: 700;
        }

        .company-info {
            margin-top: 14px;
            display: grid;
            gap: 5px;
            max-width: 620px;
        }

        .company-info div {
            font-size: 12px;
            line-height: 1.6;
            color: rgba(255,255,255,.92);
        }

        .invoice-meta {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
            text-align: right;
        }

        .invoice-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,.78);
            font-weight: 800;
            margin-bottom: 6px;
        }

        .invoice-number {
            font-size: 30px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.1;
        }

        .invoice-badge {
            margin-top: 10px;
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.18);
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
        }

        .invoice-body {
            padding: 20px 24px 22px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 16px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e9efee;
            border-radius: 18px;
            padding: 16px 18px;
            break-inside: avoid;
        }

        .card-title {
            margin: 0 0 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--muted);
            font-weight: 800;
        }

        .bill-name {
            font-size: 18px;
            font-weight: 800;
            color: #22343a;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .bill-info {
            display: grid;
            gap: 5px;
        }

        .bill-info div {
            font-size: 13px;
            line-height: 1.6;
            color: #475569;
        }

        .info-grid {
            display: grid;
            gap: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            padding-bottom: 8px;
            border-bottom: 1px solid #eef3f2;
        }

        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-key {
            font-size: 12px;
            color: var(--muted);
            font-weight: 700;
        }

        .info-value {
            font-size: 13px;
            color: #22343a;
            font-weight: 800;
            text-align: right;
        }

        .status-pill {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: capitalize;
        }

        .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .status-unpaid {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-partial {
            background: #fef3c7;
            color: #92400e;
        }

        .section {
            margin-top: 16px;
            break-inside: avoid;
        }

        .section-title {
            margin: 0 0 8px;
            font-size: 20px;
            color: #22343a;
            line-height: 1.2;
        }

        .section-subtitle {
            margin: 0 0 12px;
            font-size: 12px;
            line-height: 1.7;
            color: var(--muted);
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid #e9efee;
            border-radius: 18px;
            background: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 680px;
        }

        th {
            text-align: left;
            padding: 12px 14px;
            background: var(--soft);
            color: #486168;
            font-size: 11px;
            font-weight: 800;
            border-bottom: 1px solid #edf1f0;
        }

        td {
            padding: 14px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f2f5f5;
            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .service-title {
            font-size: 16px;
            font-weight: 800;
            color: #22343a;
            margin-bottom: 6px;
        }

        .service-meta {
            display: grid;
            gap: 3px;
        }

        .service-meta div {
            font-size: 11px;
            line-height: 1.55;
            color: #94a3b8;
        }

        .amount-text {
            font-size: 18px;
            font-weight: 800;
            color: var(--teal);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 14px;
            align-items: start;
            margin-top: 16px;
        }

        .notes-card,
        .summary-card {
            background: #ffffff;
            border: 1px solid #e9efee;
            border-radius: 18px;
            padding: 16px 18px;
            break-inside: avoid;
        }

        .notes-card p {
            margin: 0;
            font-size: 12px;
            line-height: 1.75;
            color: var(--muted);
        }

        .summary-table {
            display: grid;
            gap: 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eef3f2;
            font-size: 13px;
            color: #475569;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-row.total {
            margin-top: 4px;
            padding-top: 12px;
            border-top: 1px solid #dfe8e6;
            font-size: 16px;
            font-weight: 800;
            color: #22343a;
        }

        .summary-row.total span:last-child {
            color: var(--teal);
        }

        .invoice-footer {
            margin-top: 16px;
            border-top: 1px solid #eef3f2;
            padding-top: 10px;
            display: grid;
            gap: 4px;
        }

        .invoice-footer div {
            font-size: 11px;
            line-height: 1.6;
            color: #94a3b8;
        }

        .footer-strong {
            color: #486168;
            font-weight: 800;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        @media print {
            html, body {
                background: #ffffff !important;
                padding: 0;
                margin: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .top-actions {
                display: none !important;
            }

            .page {
                max-width: 100%;
                margin: 0;
            }

            .invoice-shell {
                border: none;
                border-radius: 0;
                box-shadow: none;
                overflow: visible;
            }

            .invoice-header {
                background: #ffffff !important;
                color: var(--text) !important;
                border-bottom: 2px solid var(--border);
                padding: 0 0 10px 0;
                grid-template-columns: 1.2fr .8fr;
                gap: 10px;
            }

            .invoice-header::before {
                display: none !important;
            }

            .company-logo {
                background: #ffffff !important;
                border: 1px solid var(--border);
                padding: 3px;
                width: 46px;
                height: 46px;
                border-radius: 10px;
            }

            .company-name {
                font-size: 24px;
                color: var(--text) !important;
            }

            .invoice-number {
                font-size: 22px;
                color: var(--text) !important;
            }

            .company-tagline,
            .invoice-label,
            .company-info div {
                color: var(--muted) !important;
                font-size: 10px;
                line-height: 1.45;
            }

            .invoice-badge {
                background: #ffffff !important;
                border: 1px solid var(--border);
                color: var(--text) !important;
                padding: 5px 8px;
                font-size: 10px;
                margin-top: 6px;
            }

            .invoice-body {
                padding: 10px 0 0 0;
            }

            .meta-grid {
                gap: 8px;
                margin-bottom: 10px;
            }

            .card,
            .notes-card,
            .summary-card {
                border-radius: 10px;
                padding: 10px 12px;
            }

            .card-title {
                font-size: 10px;
                margin-bottom: 8px;
            }

            .bill-name {
                font-size: 14px;
                margin-bottom: 6px;
            }

            .bill-info div,
            .info-key,
            .info-value,
            .notes-card p,
            .invoice-footer div,
            .service-meta div {
                font-size: 10px;
                line-height: 1.45;
            }

            .section {
                margin-top: 10px;
            }

            .section-title {
                font-size: 15px;
                margin-bottom: 4px;
            }

            .section-subtitle {
                font-size: 10px;
                margin-bottom: 8px;
                line-height: 1.45;
            }

            th, td {
                padding: 8px 10px;
                font-size: 10px;
            }

            .service-title {
                font-size: 12px;
                margin-bottom: 4px;
            }

            .amount-text {
                font-size: 13px;
            }

            .summary-grid {
                grid-template-columns: 1fr 220px;
                gap: 8px;
                margin-top: 10px;
            }

            .summary-row {
                padding: 6px 0;
                font-size: 10px;
            }

            .summary-row.total {
                font-size: 12px;
                padding-top: 8px;
            }

            .invoice-footer {
                margin-top: 10px;
                padding-top: 8px;
            }

            .table-wrap,
            table {
                min-width: 0 !important;
                overflow: visible !important;
            }

            .page-break-avoid,
            .card,
            .section,
            .table-wrap,
            .notes-card,
            .summary-card,
            .meta-grid,
            .summary-grid {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }

        @media (max-width: 900px) {
            body {
                padding: 16px;
            }

            .invoice-header,
            .meta-grid,
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .invoice-meta {
                align-items: flex-start;
                text-align: left;
            }

            .invoice-number {
                font-size: 26px;
            }

            .company-name {
                font-size: 28px;
            }
        }

        @media (max-width: 640px) {
            .invoice-header,
            .invoice-body {
                padding: 18px;
            }

            .company-block {
                flex-direction: column;
            }

            .section-title {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
@php
    $companyName = 'Khayra Physio';
    $companyTagline = 'Leading Spinal & Pain Care';
    $companyAddress = 'Jl. A. Yani No. 835C, Padasuka, Kec. Cibeunying Kidul, Kota Bandung, Jawa Barat 40125';
    $companyWhatsapp = '+62 881-0823-00889';
    $companyInstagram = '@khayra.physio';

    $subtotal = (int) ($billing->amount ?? 0);
    $adminFee = 0;
    $total = $subtotal + $adminFee;
@endphp

<div class="page">
    <div class="top-actions">
        <a href="/admin/billings/{{ $billing->id }}" class="action-link">← Kembali ke Detail</a>
        <button onclick="window.print()" class="print-btn">Print / Save PDF</button>
    </div>

    <div class="invoice-shell">
        <div class="invoice-header">
            <div>
                <div class="company-block">
                    <img src="/images/khayra-logo.png" alt="Khayra Logo" class="company-logo">
                    <div>
                        <h1 class="company-name">{{ $companyName }}</h1>
                        <div class="company-tagline">{{ $companyTagline }}</div>
                    </div>
                </div>

                <div class="company-info">
                    <div><strong>Alamat:</strong> {{ $companyAddress }}</div>
                    <div><strong>WhatsApp:</strong> {{ $companyWhatsapp }}</div>
                    <div><strong>Instagram:</strong> {{ $companyInstagram }}</div>
                </div>
            </div>

            <div class="invoice-meta">
                <div>
                    <div class="invoice-label">Invoice Number</div>
                    <div class="invoice-number">{{ $billing->invoice_number ?: 'INV-' . str_pad($billing->id, 4, '0', STR_PAD_LEFT) }}</div>
                </div>

                <div class="invoice-badge">Official Payment Invoice</div>
            </div>
        </div>

        <div class="invoice-body">
            <div class="meta-grid">
                <div class="card">
                    <h2 class="card-title">Bill To</h2>
                    <div class="bill-name">{{ optional($billing->patient)->full_name ?: 'Patient' }}</div>
                    <div class="bill-info">
                        <div><strong>WhatsApp:</strong> {{ optional($billing->patient)->whatsapp ?: '-' }}</div>
                        <div><strong>Alamat:</strong> {{ optional($billing->patient)->address ?: '-' }}</div>
                    </div>
                </div>

                <div class="card">
                    <h2 class="card-title">Invoice Info</h2>
                    <div class="info-grid">
                        <div class="info-row">
                            <span class="info-key">Invoice Date</span>
                            <span class="info-value">{{ $billing->invoice_date ?: '-' }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-key">Payment Status</span>
                            <span class="info-value">
                                <span class="status-pill status-{{ $billing->payment_status }}">
                                    {{ $billing->payment_status ?: '-' }}
                                </span>
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-key">Payment Method</span>
                            <span class="info-value">{{ $billing->payment_method ?: '-' }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-key">Visit Reference</span>
                            <span class="info-value">{{ $billing->visit ? '#' . $billing->visit->id : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section page-break-avoid">
                <h2 class="section-title">Invoice Breakdown</h2>
                <p class="section-subtitle">Ringkasan layanan yang ditagihkan kepada patient.</p>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Visit Date</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="service-title">Physiotherapy Service Billing</div>
                                    <div class="service-meta">
                                        <div>Patient: {{ optional($billing->patient)->full_name ?: '-' }}</div>
                                        <div>Visit ID: {{ $billing->visit ? '#' . $billing->visit->id : '-' }}</div>
                                        <div>Notes: {{ $billing->notes ?: 'No additional notes' }}</div>
                                    </div>
                                </td>
                                <td>{{ $billing->visit ? ($billing->visit->visit_date ?: '-') : '-' }}</td>
                                <td>
                                    <div class="amount-text">Rp {{ number_format($billing->amount, 0, ',', '.') }}</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="summary-grid">
                <div class="notes-card">
                    <h2 class="section-title" style="font-size:18px; margin-bottom:8px;">Notes</h2>
                    <p>
                        Invoice ini merupakan tagihan resmi layanan fisioterapi di {{ $companyName }}.
                        Untuk pertanyaan atau konfirmasi pembayaran, silakan hubungi admin melalui WhatsApp.
                    </p>
                </div>

                <div class="summary-card">
                    <div class="summary-table">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <div class="summary-row">
                            <span>Administrative Fee</span>
                            <span>Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                        </div>

                        <div class="summary-row total">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="invoice-footer">
                <div><span class="footer-strong">{{ $companyName }}</span> • {{ $companyTagline }}</div>
                <div>{{ $companyAddress }}</div>
                <div>WhatsApp: {{ $companyWhatsapp }} • Instagram: {{ $companyInstagram }}</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>