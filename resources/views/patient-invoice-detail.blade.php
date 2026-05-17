<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Detail - Khayra Physio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at 12% 0%, rgba(47,124,122,.12), transparent 28%),
                linear-gradient(180deg, #eef5f4 0%, #f7faf9 42%, #ffffff 100%);
            color: #17232b;
        }

        .page { min-height: 100vh; padding: 34px; }
        .container { max-width: 1180px; margin: 0 auto; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .brand img {
            width: 46px;
            height: 46px;
            object-fit: contain;
            border-radius: 14px;
            background: rgba(255,255,255,.75);
            border: 1px solid rgba(47,124,122,.14);
            padding: 4px;
        }

        .brand-name { font-size: 17px; font-weight: 900; color: #22343a; }
        .muted { color: #94a3b8; font-size: 12px; line-height: 1.6; }

        .badge {
            display: inline-flex;
            padding: 8px 13px;
            border-radius: 999px;
            background: #eef7f5;
            color: #2f7c7a;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 10px;
        }

        .title {
            margin: 0;
            font-size: 44px;
            line-height: 1.04;
            letter-spacing: -.8px;
            color: #22343a;
            font-weight: 900;
        }

        .subtitle {
            margin: 12px 0 0;
            max-width: 760px;
            font-size: 14px;
            line-height: 1.9;
            color: #64748b;
        }

        .actions { display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }

        .btn {
            min-height: 44px;
            padding: 0 18px;
            border-radius: 15px;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            font-size: 13px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            white-space: nowrap;
        }

        .btn-primary {
            color: #ffffff;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            box-shadow: 0 12px 24px rgba(47,124,122,.18);
        }

        .btn-soft {
            color: #2f7c7a;
            background: rgba(255,255,255,.9);
            border: 1px solid #dfeae8;
        }

        .hero {
            background: #ffffff;
            border: 1px solid #e6eeee;
            border-radius: 34px;
            box-shadow: 0 24px 54px rgba(15,23,42,.08);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.08fr .92fr;
            min-height: 320px;
        }

        .hero-main {
            padding: 30px;
            background: linear-gradient(135deg, #ffffff 0%, #f9fcfb 52%, #eef7f5 100%);
        }

        .hero-side {
            padding: 30px;
            color: #ffffff;
            background:
                radial-gradient(circle at 80% 18%, rgba(255,255,255,.15), transparent 28%),
                linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-side::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px);
            background-size: 56px 56px;
            pointer-events: none;
        }

        .hero-side > * { position: relative; z-index: 1; }

        .invoice-title {
            margin: 0 0 10px;
            font-size: 36px;
            line-height: 1.08;
            color: #22343a;
            font-weight: 900;
        }

        .profile-card {
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.20);
            border-radius: 24px;
            padding: 20px;
            margin-bottom: 14px;
        }

        .profile-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.78);
            font-weight: 900;
            margin-bottom: 7px;
        }

        .profile-value {
            font-size: 24px;
            line-height: 1.25;
            font-weight: 900;
            color: #ffffff;
        }

        .profile-muted {
            margin-top: 7px;
            color: rgba(255,255,255,.82);
            font-size: 13px;
            line-height: 1.7;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-top: 22px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e8eeee;
            border-radius: 22px;
            padding: 18px;
            box-shadow: 0 12px 28px rgba(15,23,42,.045);
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 900;
            color: #22343a;
            white-space: nowrap;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #e8eeee;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 14px 34px rgba(15,23,42,.055);
            margin-bottom: 20px;
        }

        .section-title {
            margin: 0;
            color: #22343a;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -.4px;
        }

        .section-subtitle {
            margin: 8px 0 20px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid #edf1f0;
            border-radius: 20px;
            background: #ffffff;
        }

        table { width: 100%; min-width: 860px; border-collapse: collapse; }
        th, td { padding: 15px 14px; border-bottom: 1px solid #edf1f0; text-align: left; vertical-align: top; font-size: 13px; }
        th { background: #f7faf9; color: #486168; text-transform: uppercase; letter-spacing: .05em; font-size: 11px; font-weight: 900; }
        tbody tr:last-child td { border-bottom: 0; }

        .primary-text { font-weight: 900; color: #22343a; line-height: 1.45; }
        .secondary-text { margin-top: 4px; color: #94a3b8; font-size: 11px; line-height: 1.55; }
        .money { font-weight: 900; color: #22343a; white-space: nowrap; }

        .pill {
            display: inline-flex;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .pill-green { background: #dcfce7; color: #166534; }
        .pill-orange { background: #fef3c7; color: #92400e; }
        .pill-red { background: #fee2e2; color: #b91c1c; }
        .pill-gray { background: #e5e7eb; color: #374151; }
        .pill-blue { background: #dbeafe; color: #1d4ed8; }

        .summary-box {
            margin-top: 18px;
            background:
                radial-gradient(circle at 80% 0%, rgba(255,255,255,.14), transparent 32%),
                linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            color: #ffffff;
            border-radius: 24px;
            padding: 24px;
            max-width: 520px;
            margin-left: auto;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,.14);
            font-size: 14px;
        }

        .summary-row:last-child {
            border-bottom: 0;
            padding-top: 16px;
            margin-top: 6px;
        }

        .summary-row strong { font-size: 15px; }
        .summary-total strong { font-size: 24px; }

        .note-card {
            background: #fbfcfc;
            border: 1px solid #edf1f0;
            border-radius: 20px;
            padding: 18px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }

        @media (max-width: 980px) {
            .hero-grid, .stats-grid { grid-template-columns: 1fr; }
            .summary-box { max-width: 100%; }
        }

        @media (max-width: 760px) {
            .page { padding: 18px; }
            .title { font-size: 34px; }
            .actions, .btn { width: 100%; }
        }

        @media print {
            body { background: #ffffff; }
            .actions, .btn-soft { display: none; }
            .page { padding: 0; }
            .hero, .section-card { box-shadow: none; border-color: #ddd; }
        }
    </style>
</head>
<body>
@php
    $paidDisplay = (float) ($billing->paid_amount ?? 0);
    if ($billing->payment_status === 'paid' && $paidDisplay <= 0) {
        $paidDisplay = (float) ($billing->amount ?? 0);
    }

    $remainingDisplay = (float) ($billing->remaining_amount ?? 0);
    if ($billing->payment_status === 'unpaid' && $remainingDisplay <= 0) {
        $remainingDisplay = (float) ($billing->amount ?? 0);
    }

    $statusClass = match($billing->payment_status) {
        'paid' => 'pill-green',
        'partial' => 'pill-orange',
        'unpaid' => 'pill-red',
        'void' => 'pill-gray',
        default => 'pill-blue',
    };
@endphp

<div class="page">
    <div class="container">
        <div class="topbar">
            <div>
                <div class="brand">
                    <img src="/images/khayra-logo.png" alt="Khayra Logo">
                    <div>
                        <div class="brand-name">Khayra Physio</div>
                        <div class="muted">Patient Portal</div>
                    </div>
                </div>

                <span class="badge">Invoice Detail</span>
                <h1 class="title">{{ $billing->invoice_number ?: 'Invoice #' . $billing->id }}</h1>
                <p class="subtitle">Detail invoice pasien, breakdown jasa/produk, status pembayaran, promo, dan total tagihan.</p>
            </div>

            <div class="actions">
                <a href="/patient/dashboard" class="btn btn-soft">← Dashboard</a>
                <button onclick="window.print()" class="btn btn-primary">Print / Save PDF</button>
            </div>
        </div>

        <section class="hero">
            <div class="hero-grid">
                <div class="hero-main">
                    <span class="badge">Payment Snapshot</span>
                    <h2 class="invoice-title">{{ optional($billing->patient)->full_name ?: 'Patient' }}</h2>
                    <p class="subtitle">
                        {{ optional($billing->patient)->medical_record_number ? 'MR: ' . optional($billing->patient)->medical_record_number : 'Medical record number belum tersedia' }}
                    </p>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-label">Invoice Date</div>
                            <div class="stat-value">{{ $billing->invoice_date ? $billing->invoice_date->format('Y-m-d') : '-' }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Status</div>
                            <div class="stat-value"><span class="pill {{ $statusClass }}">{{ $billing->payment_status }}</span></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Paid</div>
                            <div class="stat-value">Rp {{ number_format($paidDisplay, 0, ',', '.') }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Remaining</div>
                            <div class="stat-value">Rp {{ number_format($remainingDisplay, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <aside class="hero-side">
                    <div class="profile-card">
                        <div class="profile-label">Grand Total</div>
                        <div class="profile-value">Rp {{ number_format($billing->amount, 0, ',', '.') }}</div>
                        <div class="profile-muted">
                            Payment method: {{ $billing->payment_method_label ?? ($billing->payment_method ?: '-') }}<br>
                            Promo: {{ $billing->promo_code ?: '-' }}
                        </div>
                    </div>

                    <div class="profile-card">
                        <div class="profile-label">Visit</div>
                        <div class="profile-value">{{ $billing->visit ? 'Visit #' . $billing->visit->id : '-' }}</div>
                        <div class="profile-muted">
                            {{ $billing->visit && $billing->visit->visit_date ? $billing->visit->visit_date : 'Tidak terhubung ke visit tertentu' }}
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="section-card">
            <h2 class="section-title">Detail Jasa & Produk</h2>
            <p class="section-subtitle">Breakdown item invoice. Produk inventory ditampilkan sesuai transaksi kasir.</p>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billing->items as $item)
                            <tr>
                                <td>
                                    <div class="primary-text">{{ $item->description }}</div>
                                    @if($item->inventoryItem)
                                        <div class="secondary-text">SKU: {{ $item->inventoryItem->sku ?: '-' }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="pill {{ $item->item_type === 'inventory' ? 'pill-orange' : 'pill-blue' }}">
                                        {{ $item->item_type }}
                                    </span>
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td class="money">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="money">Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                                <td>{{ $item->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td>
                                    <div class="primary-text">Tagihan Manual</div>
                                    <div class="secondary-text">Invoice lama / billing dibuat tanpa item checkout.</div>
                                </td>
                                <td><span class="pill pill-blue">manual</span></td>
                                <td>1</td>
                                <td class="money">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
                                <td class="money">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
                                <td>{{ $billing->notes ?: '-' }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="summary-box">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong>Rp {{ number_format($billing->subtotal_amount ?: $billing->amount, 0, ',', '.') }}</strong>
                </div>
                <div class="summary-row">
                    <span>Discount{{ $billing->promo_code ? ' (' . $billing->promo_code . ')' : '' }}</span>
                    <strong>Rp {{ number_format($billing->discount_amount ?: 0, 0, ',', '.') }}</strong>
                </div>
                <div class="summary-row">
                    <span>Paid</span>
                    <strong>Rp {{ number_format($paidDisplay, 0, ',', '.') }}</strong>
                </div>
                <div class="summary-row">
                    <span>Remaining</span>
                    <strong>Rp {{ number_format($remainingDisplay, 0, ',', '.') }}</strong>
                </div>
                <div class="summary-row summary-total">
                    <span>Grand Total</span>
                    <strong>Rp {{ number_format($billing->amount, 0, ',', '.') }}</strong>
                </div>
            </div>
        </section>

        <section class="section-card">
            <h2 class="section-title">Catatan Pembayaran</h2>
            <p class="section-subtitle">Informasi tambahan dari kasir/admin.</p>

            <div class="note-card">
                <strong>Payment Detail:</strong><br>
                {{ $billing->payment_detail_notes ?: 'Belum ada catatan detail pembayaran.' }}
                <br><br>
                <strong>General Notes:</strong><br>
                {{ $billing->notes ?: 'Belum ada catatan tambahan.' }}
            </div>
        </section>
    </div>
</div>
</body>
</html>
