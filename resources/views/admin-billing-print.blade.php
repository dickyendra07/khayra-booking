<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $billing->invoice_number ?? 'Khayra Physio' }}</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        :root {
            --teal: #2f7c7a;
            --teal-dark: #245f67;
            --text: #17232b;
            --muted: #64748b;
            --border: #e5ecea;
            --soft: #f7faf9;
            --orange-soft: #fff7ed;
            --orange: #c2410c;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #eef2f1;
            color: var(--text);
        }

        body {
            padding: 22px;
        }

        .page {
            width: 100%;
            max-width: 880px;
            margin: 0 auto;
        }

        .top-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 14px;
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
            font-weight: 900;
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
        }

        .invoice-sheet {
            background: #ffffff;
            border: 1px solid #e7eceb;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.07);
        }

        .header {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 18px;
            padding: 24px 26px;
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.13), transparent 28%),
                linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px);
            background-size: auto, 44px 44px, 44px 44px;
            pointer-events: none;
        }

        .header > * {
            position: relative;
            z-index: 1;
        }

        .brand {
            display: flex;
            align-items: flex-start;
            gap: 13px;
        }

        .logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            border-radius: 14px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.18);
            padding: 6px;
            flex: 0 0 auto;
        }

        .brand-name {
            margin: 0;
            font-size: 31px;
            line-height: 1.05;
            color: #ffffff;
            font-weight: 900;
        }

        .brand-sub {
            margin-top: 5px;
            font-size: 11px;
            line-height: 1.45;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: rgba(255,255,255,.84);
            font-weight: 800;
        }

        .brand-desc {
            margin-top: 13px;
            display: grid;
            gap: 4px;
            font-size: 12px;
            line-height: 1.55;
            color: rgba(255,255,255,.92);
        }

        .invoice-meta {
            text-align: right;
            min-width: 230px;
        }

        .meta-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,.76);
            font-weight: 900;
            margin-bottom: 5px;
        }

        .invoice-number {
            font-size: 27px;
            line-height: 1.1;
            font-weight: 900;
            color: #ffffff;
            word-break: break-word;
        }

        .status-badge {
            display: inline-flex;
            margin-top: 10px;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.18);
            color: #ffffff;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .invoice-date {
            margin-top: 18px;
        }

        .invoice-date-value {
            font-size: 15px;
            font-weight: 900;
            color: #ffffff;
        }

        .body {
            padding: 20px 24px 22px;
        }

        .cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .card {
            border: 1px solid var(--border);
            background: #ffffff;
            border-radius: 16px;
            padding: 15px 16px;
            break-inside: avoid;
        }

        .card-title {
            margin: 0 0 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--muted);
            font-weight: 900;
        }

        .main-text {
            font-size: 17px;
            font-weight: 900;
            color: #22343a;
            line-height: 1.35;
        }

        .detail-text {
            margin-top: 5px;
            font-size: 12px;
            line-height: 1.6;
            color: var(--muted);
        }

        .section {
            border: 1px solid var(--border);
            background: #ffffff;
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 14px;
            break-inside: avoid;
        }

        .section-title {
            margin: 0 0 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: #334155;
            font-weight: 900;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th {
            text-align: left;
            background: var(--soft);
            color: #486168;
            padding: 10px 10px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .45px;
            font-weight: 900;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #eef3f2;
            font-size: 12px;
            line-height: 1.45;
            vertical-align: top;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .col-desc { width: 42%; }
        .col-type { width: 16%; }
        .col-qty { width: 10%; text-align: center; }
        .col-price { width: 16%; text-align: right; }
        .col-subtotal { width: 16%; text-align: right; }

        th.col-qty,
        td.col-qty {
            text-align: center;
        }

        th.col-price,
        th.col-subtotal,
        td.col-price,
        td.col-subtotal {
            text-align: right;
        }

        .item-name {
            font-weight: 900;
            color: #22343a;
        }

        .item-meta {
            margin-top: 4px;
            color: #8a97a3;
            font-size: 10.5px;
            line-height: 1.45;
        }

        .pill {
            display: inline-flex;
            padding: 5px 8px;
            border-radius: 999px;
            font-size: 9.5px;
            font-weight: 900;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .service {
            background: #eef7f5;
            color: #2f7c7a;
        }

        .inventory {
            background: var(--orange-soft);
            color: var(--orange);
        }

        .money {
            font-weight: 900;
            color: #22343a;
            white-space: nowrap;
        }

        .summary-wrap {
            display: flex;
            justify-content: flex-end;
            margin-top: 14px;
        }

        .summary {
            width: 360px;
            max-width: 100%;
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
            break-inside: avoid;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            border-bottom: 1px solid #eef3f2;
            font-size: 12px;
            color: #334155;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-row strong {
            font-weight: 900;
            color: #22343a;
        }

        .summary-row.total {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            color: #ffffff;
            font-size: 16px;
            font-weight: 900;
        }

        .summary-row.total strong {
            color: #ffffff;
            font-size: 18px;
        }

        .notes {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .note-box {
            border: 1px solid var(--border);
            background: #fbfcfc;
            border-radius: 15px;
            padding: 13px 14px;
            break-inside: avoid;
        }

        .note-label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--muted);
            font-weight: 900;
            margin-bottom: 7px;
        }

        .note-text {
            font-size: 12px;
            line-height: 1.6;
            color: #334155;
        }

        .footer {
            padding-top: 12px;
            margin-top: 10px;
            border-top: 1px solid #edf1f0;
            color: #6b7280;
            font-size: 11px;
            line-height: 1.55;
            display: flex;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        @media print {
            html,
            body {
                background: #ffffff;
                width: 210mm;
                min-height: 297mm;
            }

            body {
                padding: 0;
            }

            .top-actions {
                display: none !important;
            }

            .page {
                width: 100%;
                max-width: none;
                margin: 0;
            }

            .invoice-sheet {
                border-radius: 0;
                border: none;
                box-shadow: none;
                overflow: visible;
            }

            .header {
                padding: 16mm 14mm 12mm;
            }

            .body {
                padding: 8mm 14mm 10mm;
            }

            .section {
                margin-bottom: 9px;
                padding: 12px;
            }

            .cards {
                gap: 10px;
                margin-bottom: 10px;
            }

            .card {
                padding: 12px;
                border-radius: 12px;
            }

            .brand-name {
                font-size: 26px;
            }

            .invoice-number {
                font-size: 22px;
            }

            .logo {
                width: 48px;
                height: 48px;
            }

            th {
                padding: 8px 8px;
                font-size: 9px;
            }

            td {
                padding: 9px 8px;
                font-size: 10.5px;
            }

            .item-meta {
                font-size: 9.5px;
            }

            .summary-row {
                padding: 9px 12px;
            }

            .summary-row.total {
                font-size: 14px;
            }

            .summary-row.total strong {
                font-size: 15px;
            }

            .notes {
                gap: 8px;
                margin-top: 10px;
            }

            .note-box {
                padding: 10px 12px;
            }

            .footer {
                font-size: 10px;
            }
        }

        @media screen and (max-width: 760px) {
            body {
                padding: 12px;
            }

            .header,
            .cards {
                grid-template-columns: 1fr;
            }

            .invoice-meta {
                text-align: left;
            }

            .col-type,
            .col-qty,
            .col-price,
            .col-subtotal {
                width: auto;
            }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="top-actions">
        <a href="/admin/billings/{{ $billing->id }}" class="action-link">← Kembali</a>
        <button onclick="window.print()" class="print-btn">Print / Save PDF</button>
    </div>

    <div class="invoice-sheet">
        <header class="header">
            <div>
                <div class="brand">
                    <img src="/images/khayra-logo.png" alt="Khayra Physio" class="logo">
                    <div>
                        <h1 class="brand-name">Khayra Physio</h1>
                        <div class="brand-sub">Physiotherapy & Rehabilitation Clinic</div>
                    </div>
                </div>

                <div class="brand-desc">
                    <div>Invoice layanan fisioterapi, produk pendukung, dan administrasi pasien.</div>
                    <div>Website: app.khayraphysio.com</div>
                </div>
            </div>

            <div class="invoice-meta">
                <div class="meta-label">Invoice Number</div>
                <div class="invoice-number">{{ $billing->invoice_number ?: 'INV-' . $billing->id }}</div>
                <div class="status-badge">{{ $billing->payment_status }}</div>

                <div class="invoice-date">
                    <div class="meta-label">Invoice Date</div>
                    <div class="invoice-date-value">{{ $billing->invoice_date ? $billing->invoice_date->format('Y-m-d') : '-' }}</div>
                </div>
            </div>
        </header>

        <main class="body">
            <section class="cards">
                <div class="card">
                    <h2 class="card-title">Bill To</h2>
                    <div class="main-text">{{ optional($billing->patient)->full_name ?: '-' }}</div>
                    <div class="detail-text">
                        MR: {{ optional($billing->patient)->medical_record_number ?: '-' }}<br>
                        WhatsApp: {{ optional($billing->patient)->whatsapp ?: '-' }}<br>
                        Birth Date: {{ optional($billing->patient)->birth_date ?: '-' }}
                    </div>
                </div>

                <div class="card">
                    <h2 class="card-title">Payment Info</h2>
                    <div class="main-text">{{ $billing->payment_method_label }}</div>
                    <div class="detail-text">
                        Status: {{ ucfirst($billing->payment_status) }}<br>
                        Visit: {{ $billing->visit ? 'Visit #' . $billing->visit->id : '-' }}<br>
                        Visit Date: {{ $billing->visit && $billing->visit->visit_date ? $billing->visit->visit_date : '-' }}
                    </div>
                </div>
            </section>

            <section class="section">
                <h2 class="section-title">Detail Jasa & Produk</h2>

                <table>
                    <thead>
                        <tr>
                            <th class="col-desc">Description</th>
                            <th class="col-type">Type</th>
                            <th class="col-qty">Qty</th>
                            <th class="col-price">Unit Price</th>
                            <th class="col-subtotal">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billing->items as $item)
                            <tr>
                                <td class="col-desc">
                                    <div class="item-name">{{ $item->description }}</div>
                                    @if($item->inventoryItem)
                                        <div class="item-meta">
                                            SKU: {{ $item->inventoryItem->sku ?: '-' }} · Unit: {{ $item->inventoryItem->unit ?: '-' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="col-type">
                                    <span class="pill {{ $item->item_type }}">{{ $item->item_type }}</span>
                                </td>
                                <td class="col-qty">{{ $item->quantity }}</td>
                                <td class="col-price money">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="col-subtotal money">Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="col-desc">
                                    <div class="item-name">Tagihan Manual</div>
                                    <div class="item-meta">Invoice lama / billing dibuat tanpa item checkout.</div>
                                </td>
                                <td class="col-type"><span class="pill service">manual</span></td>
                                <td class="col-qty">1</td>
                                <td class="col-price money">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
                                <td class="col-subtotal money">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="summary-wrap">
                    <div class="summary">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <strong>Rp {{ number_format($billing->subtotal_amount ?: $billing->amount, 0, ',', '.') }}</strong>
                        </div>
                        <div class="summary-row">
                            <span>Promo / Discount{{ $billing->promo_code ? ' (' . $billing->promo_code . ')' : '' }}</span>
                            <strong>Rp {{ number_format($billing->discount_amount ?: 0, 0, ',', '.') }}</strong>
                        </div>
                        <div class="summary-row">
                            <span>Paid Amount</span>
                            <strong>Rp {{ number_format($billing->paid_amount ?: 0, 0, ',', '.') }}</strong>
                        </div>
                        <div class="summary-row">
                            <span>Change</span>
                            <strong>Rp {{ number_format($billing->change_amount ?: 0, 0, ',', '.') }}</strong>
                        </div>
                        <div class="summary-row">
                            <span>Remaining</span>
                            <strong>Rp {{ number_format($billing->remaining_amount ?: 0, 0, ',', '.') }}</strong>
                        </div>
                        <div class="summary-row total">
                            <span>Grand Total</span>
                            <strong>Rp {{ number_format($billing->amount, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section class="notes">
                <div class="note-box">
                    <div class="note-label">Payment Detail Notes</div>
                    <div class="note-text">{{ $billing->payment_detail_notes ?: '-' }}</div>
                </div>

                <div class="note-box">
                    <div class="note-label">General Notes</div>
                    <div class="note-text">{{ $billing->notes ?: '-' }}</div>
                </div>

                @if($billing->payment_status === 'void' || $billing->voided_at)
                <div class="note-box">
                    <div class="note-label">Void Reason</div>
                    <div class="note-text">
                        VOID at {{ $billing->voided_at ? $billing->voided_at->format('Y-m-d H:i') : '-' }}<br>
                        {{ $billing->void_reason ?: '-' }}
                    </div>
                </div>
            </section>

                @endif

            <footer class="footer">
                <div>Terima kasih telah menggunakan layanan Khayra Physio.</div>
                <div>Generated: {{ now()->format('Y-m-d H:i') }}</div>
            </footer>
        </main>
    </div>
</div>
</body>
</html>
