<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Detail - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f8f8;
            color: #17232b;
        }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1220px; margin: 0 auto; }
        .top-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .ghost-link, .primary-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
        }
        .ghost-link {
            background: #ffffff;
            border: 1px solid #e6ebea;
            color: #2c5b5a;
        }
        .primary-link {
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
        }
        .hero {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.05);
            margin-bottom: 18px;
        }
        .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 20px;
        }
        .hero-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #eef5f4;
            color: #35565d;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 16px;
        }
        .hero-title {
            margin: 0;
            font-size: 40px;
            line-height: 1.06;
            color: #22343a;
            font-weight: 800;
        }
        .hero-text {
            margin: 14px 0 0;
            font-size: 14px;
            line-height: 1.9;
            color: #6b7280;
        }
        .hero-tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }
        .hero-tag {
            display: inline-block;
            padding: 9px 13px;
            border-radius: 999px;
            background: #f7faf9;
            border: 1px solid #e7eceb;
            color: #486168;
            font-size: 12px;
            font-weight: 700;
        }
        .hero-side {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            border-radius: 24px;
            padding: 24px;
            color: #ffffff;
        }
        .hero-side h3 {
            margin: 0 0 8px;
            font-size: 26px;
        }
        .hero-side p {
            margin: 0 0 16px;
            font-size: 13px;
            line-height: 1.8;
            color: rgba(255,255,255,.94);
        }
        .snapshot-grid {
            display: grid;
            gap: 12px;
        }
        .snapshot-card {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.18);
        }
        .snapshot-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .45px;
            color: rgba(255,255,255,.88);
            margin-bottom: 6px;
            font-weight: 700;
        }
        .snapshot-value {
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.5;
            word-break: break-word;
        }
        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
            margin-bottom: 18px;
        }
        .section-title {
            margin: 0;
            font-size: 24px;
            color: #22343a;
            font-weight: 800;
        }
        .section-subtitle {
            margin: 8px 0 18px;
            font-size: 13px;
            line-height: 1.8;
            color: #6b7280;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .info-item {
            border: 1px solid #edf1f0;
            border-radius: 18px;
            padding: 16px;
            background: #fbfcfc;
        }
        .info-item.full {
            grid-column: 1 / -1;
        }
        .info-key {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .45px;
            color: #7b8794;
            font-weight: 800;
            margin-bottom: 8px;
        }
        .info-value {
            font-size: 16px;
            line-height: 1.7;
            color: #22343a;
            font-weight: 700;
            word-break: break-word;
        }
        .status-pill {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
        }
        .paid { background: #dcfce7; color: #166534; }
        .unpaid { background: #fee2e2; color: #b91c1c; }
        .partial { background: #fef3c7; color: #92400e; }
        @media (max-width: 960px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-grid, .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'billings'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/billings" class="ghost-link">← Kembali ke Billing List</a>
                <a href="/admin/billings/{{ $billing->id }}/edit" class="ghost-link">Edit Billing</a>
                <a href="/admin/billings/{{ $billing->id }}/print" class="primary-link" target="_blank">Print / Save PDF</a>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <div class="hero-badge">Billing Detail</div>
                        <h1 class="hero-title">{{ $billing->invoice_number }}</h1>
                        <p class="hero-text">
                            Detail invoice ini menampilkan patient, nominal tagihan, status pembayaran, metode pembayaran, dan catatan pembayaran agar admin lebih mudah melakukan follow-up.
                        </p>

                        <div class="hero-tags">
                            <span class="hero-tag">{{ $billing->patient->full_name ?? '-' }}</span>
                            <span class="hero-tag">{{ $billing->payment_method_label }}</span>
                            <span class="hero-tag">{{ $billing->invoice_date ? $billing->invoice_date->format('Y-m-d') : '-' }}</span>
                        </div>
                    </div>

                    <div class="hero-side">
                        <h3>Payment Snapshot</h3>
                        <p>Ringkasan cepat untuk membaca kondisi pembayaran invoice saat ini.</p>

                        <div class="snapshot-grid">
                            <div class="snapshot-card">
                                <div class="snapshot-label">Payment Status</div>
                                <div class="snapshot-value">{{ ucfirst($billing->payment_status) }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Payment Method</div>
                                <div class="snapshot-value">{{ $billing->payment_method_label }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Amount</div>
                                <div class="snapshot-value">Rp {{ number_format($billing->amount, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Billing Information</h2>
                <p class="section-subtitle">Informasi dasar invoice dan relasi patient / visit yang terkait.</p>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-key">Invoice Number</div>
                        <div class="info-value">{{ $billing->invoice_number }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-key">Invoice Date</div>
                        <div class="info-value">{{ $billing->invoice_date ? $billing->invoice_date->format('Y-m-d') : '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-key">Patient</div>
                        <div class="info-value">{{ $billing->patient->full_name ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-key">Medical Record Number</div>
                        <div class="info-value">{{ $billing->patient->medical_record_number ?? '-' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-key">Visit Terkait</div>
                        <div class="info-value">
                            @if($billing->visit)
                                #{{ $billing->visit->id }} - {{ $billing->visit->visit_date ?: '-' }}
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-key">Amount</div>
                        <div class="info-value">Rp {{ number_format($billing->amount, 0, ',', '.') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-key">Payment Status</div>
                        <div class="info-value">
                            <span class="status-pill {{ $billing->payment_status }}">{{ ucfirst($billing->payment_status) }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-key">Payment Method</div>
                        <div class="info-value">{{ $billing->payment_method_label }}</div>
                    </div>

                    <div class="info-item full">
                        <div class="info-key">Payment Detail Notes</div>
                        <div class="info-value">{{ $billing->payment_detail_notes ?: '-' }}</div>
                    </div>

                    <div class="info-item full">
                        <div class="info-key">General Notes</div>
                        <div class="info-value">{{ $billing->notes ?: '-' }}</div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>