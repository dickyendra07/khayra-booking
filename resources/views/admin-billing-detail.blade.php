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
        .container { max-width: 1280px; margin: 0 auto; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .badge {
            display: inline-flex;
            padding: 8px 13px;
            border-radius: 999px;
            background: #eef5f4;
            color: #35565d;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 12px;
        }

        .title {
            margin: 0;
            font-size: 42px;
            line-height: 1.05;
            color: #22343a;
            font-weight: 900;
        }

        .subtitle {
            margin: 12px 0 0;
            max-width: 820px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.9;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            min-height: 42px;
            border: 0;
            cursor: pointer;
            padding: 0 16px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            white-space: nowrap;
        }

        .btn-soft {
            color: #2f7c7a;
            background: #ffffff;
            border: 1px solid #e6ebea;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(47,124,122,.16);
        }

        .btn-blue {
            background: #eef2ff;
            color: #3457d5;
            border: 1px solid #dde5ff;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 18px;
            font-size: 14px;
            line-height: 1.7;
            font-weight: 700;
        }

        .alert-success {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .hero {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(15,23,42,.05);
            margin-bottom: 18px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 18px;
            align-items: stretch;
        }

        .hero-side {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            border-radius: 24px;
            color: #ffffff;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        .hero-side::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.12), transparent 28%),
                linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px);
            background-size: auto, 56px 56px, 56px 56px;
            pointer-events: none;
        }

        .hero-side > * { position: relative; z-index: 1; }
        .hero-side h3 { margin: 0 0 10px; font-size: 26px; color: #ffffff; }
        .hero-side p { margin: 0; font-size: 13px; line-height: 1.85; color: rgba(255,255,255,.92); }

        .snapshot-grid {
            display: grid;
            gap: 12px;
            margin-top: 18px;
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
            color: rgba(255,255,255,.82);
            margin-bottom: 6px;
            font-weight: 900;
        }

        .snapshot-value {
            font-size: 18px;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.5;
            word-break: break-word;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 26px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15,23,42,.04);
            margin-bottom: 18px;
        }

        .section-title {
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            color: #22343a;
        }

        .section-subtitle {
            margin: 8px 0 18px;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.85;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }

        .info-card {
            border: 1px solid #edf1f0;
            background: #fbfcfc;
            border-radius: 18px;
            padding: 16px;
        }

        .info-card.full { grid-column: 1 / -1; }

        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #7b8794;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 15px;
            line-height: 1.65;
            color: #22343a;
            font-weight: 800;
            word-break: break-word;
        }

        .status-pill {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .paid { background: #dcfce7; color: #166534; }
        .unpaid { background: #fee2e2; color: #b91c1c; }
        .partial { background: #fef3c7; color: #92400e; }
        .void { background: #e5e7eb; color: #374151; }
        .void-box { background:#fff1f2; border:1px solid #ffe0e6; color:#991b1b; border-radius:20px; padding:18px; margin-bottom:18px; }
        .void-box h3 { margin:0 0 8px; font-size:18px; }
        .void-box p { margin:0 0 12px; font-size:13px; line-height:1.7; }
        .void-form { display:grid; grid-template-columns:1fr auto; gap:10px; align-items:end; }
        .void-form textarea { width:100%; border:1px solid #fecaca; border-radius:14px; padding:12px; min-height:72px; font-family:Arial,sans-serif; }
        .btn-void { background:#be123c; color:white; border:none; }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid #edf1f0;
            border-radius: 20px;
            background: #ffffff;
        }

        table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 15px 14px;
            border-bottom: 1px solid #edf1f0;
            vertical-align: top;
            font-size: 13px;
        }

        th {
            background: #f7faf9;
            color: #486168;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
            white-space: nowrap;
        }

        tbody tr:last-child td { border-bottom: 0; }

        .primary-text {
            font-weight: 900;
            color: #22343a;
            line-height: 1.45;
        }

        .secondary-text {
            margin-top: 4px;
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.55;
        }

        .item-pill {
            display: inline-flex;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .service { background: #eef7f5; color: #2f7c7a; }
        .inventory { background: #fff7ed; color: #c2410c; }

        .money {
            font-weight: 900;
            color: #22343a;
            white-space: nowrap;
        }

        .total-panel {
            display: flex;
            justify-content: flex-end;
            margin-top: 16px;
        }

        .total-box {
            width: min(100%, 420px);
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            color: #ffffff;
            border-radius: 22px;
            padding: 22px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: center;
        }

        .total-label {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.78);
        }

        .total-value {
            font-size: 32px;
            font-weight: 900;
            line-height: 1;
            white-space: nowrap;
        }

        @media (max-width: 980px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-grid, .info-grid { grid-template-columns: 1fr; }
            .title { font-size: 32px; }
            .total-panel { justify-content: stretch; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'billings'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Billing Invoice</span>
                    <h1 class="title">{{ $billing->invoice_number ?: 'Invoice #' . $billing->id }}</h1>
                    <p class="subtitle">
                        Detail invoice pasien dengan breakdown jasa fisioterapi, produk inventory, status pembayaran, metode bayar, dan total tagihan.
                    </p>
                </div>

                <div class="actions">
                    <a href="/admin/billings" class="btn btn-soft">← Billing List</a>
                    <a href="/admin/billings/{{ $billing->id }}/edit" class="btn btn-blue">Edit Billing</a>
                    
                    @php
                        $hasPackageItem = ($billing->items ?? collect())->contains(function ($item) {
                            return preg_match('/paket\s*(3x|6x|12x)/i', (string) $item->description);
                        });
                    @endphp

                    @if($hasPackageItem)
                        <a href="/admin/package-treatments/create?billing_id={{ $billing->id }}" class="btn btn-soft">
                            Buat Dokumen Paket
                        </a>
                    @endif
<a href="/admin/billings/{{ $billing->id }}/print" class="btn btn-primary" target="_blank">Print / Save PDF</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <span class="badge">Invoice Detail</span>
                        <h2 class="section-title">{{ optional($billing->patient)->full_name ?: 'Patient' }}</h2>
                        <p class="section-subtitle">
                            {{ optional($billing->patient)->medical_record_number ? 'MR: ' . optional($billing->patient)->medical_record_number : 'Medical record number belum tersedia' }}
                        </p>

                        <div class="info-grid">
                            <div class="info-card">
                                <div class="info-label">Invoice Date</div>
                                <div class="info-value">{{ $billing->invoice_date ? $billing->invoice_date->format('Y-m-d') : '-' }}</div>
                            </div>

                            <div class="info-card">
                                <div class="info-label">Visit</div>
                                <div class="info-value">
                                    {{ $billing->visit ? 'Visit #' . $billing->visit->id : '-' }}
                                    @if($billing->visit && $billing->visit->visit_date)
                                        <div class="secondary-text">{{ $billing->visit->visit_date }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="info-card">
                                <div class="info-label">Payment Status</div>
                                <div class="info-value">
                                    <span class="status-pill {{ $billing->payment_status }}">{{ $billing->payment_status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <aside class="hero-side">
                        <h3>Payment Snapshot</h3>
                        <p>Ringkasan pembayaran untuk front desk dan admin.</p>

                        <div class="snapshot-grid">
                            <div class="snapshot-card">
                                <div class="snapshot-label">Total Amount</div>
                                <div class="snapshot-value">Rp {{ number_format($billing->amount, 0, ',', '.') }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Payment Method</div>
                                <div class="snapshot-value">{{ $billing->payment_method_label }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Invoice Status</div>
                                <div class="snapshot-value">{{ ucfirst($billing->payment_status) }}</div>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            @if($billing->payment_status === 'void' || $billing->voided_at)
                <div class="void-box">
                    <h3>Billing ini sudah di-void</h3>
                    <p>
                        Transaksi dibatalkan pada {{ $billing->voided_at ? $billing->voided_at->format('Y-m-d H:i') : '-' }}.
                        Reason: {{ $billing->void_reason ?: '-' }}
                    </p>
                </div>
            @else
                <div class="void-box">
                    <h3>Void Billing</h3>
                    <p>
                        Gunakan tombol ini kalau invoice salah input atau transaksi dibatalkan. Inventory item pada invoice ini akan dikembalikan otomatis ke stok.
                    </p>
                    <form method="POST" action="/admin/billings/{{ $billing->id }}/void" class="void-form" onsubmit="return confirm('Yakin VOID billing ini? Inventory akan dikembalikan dan invoice tidak boleh dipakai lagi.')">
                        @csrf
                        <textarea name="void_reason" placeholder="Tulis alasan void, contoh: salah input item / pasien batal transaksi" required></textarea>
                        <button type="submit" class="btn btn-void">VOID Billing</button>
                    </form>
                </div>
            @endif

            <section class="section-card">
                <h2 class="section-title">Detail Jasa & Produk</h2>
                <p class="section-subtitle">
                    Breakdown item checkout. Service tidak mengurangi stok, sedangkan inventory otomatis tercatat sebagai stock movement saat checkout.
                </p>

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
                                            <div class="secondary-text">
                                                SKU: {{ $item->inventoryItem->sku ?: '-' }} · Stock item: {{ $item->inventoryItem->stock }} {{ $item->inventoryItem->unit }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="item-pill {{ $item->item_type }}">{{ $item->item_type }}</span>
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
                                    <td><span class="item-pill service">manual</span></td>
                                    <td>1</td>
                                    <td class="money">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
                                    <td class="money">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
                                    <td>{{ $billing->notes ?: '-' }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="total-panel">
                    <div class="total-box">
                        <div style="display:grid; gap:10px;">
                            <div style="display:flex; justify-content:space-between; gap:14px; font-size:13px; color:rgba(255,255,255,.84);">
                                <span>Subtotal</span>
                                <strong>Rp {{ number_format($billing->subtotal_amount ?: $billing->amount, 0, ',', '.') }}</strong>
                            </div>
                            <div style="display:flex; justify-content:space-between; gap:14px; font-size:13px; color:rgba(255,255,255,.84);">
                                <span>Promo / Discount{{ $billing->promo_code ? ' (' . $billing->promo_code . ')' : '' }}</span>
                                <strong>Rp {{ number_format($billing->discount_amount ?: 0, 0, ',', '.') }}</strong>
                            </div>
                            <div style="display:flex; justify-content:space-between; gap:14px; font-size:13px; color:rgba(255,255,255,.84);">
                                <span>Paid Amount</span>
                                <strong>Rp {{ number_format($billing->paid_amount ?: 0, 0, ',', '.') }}</strong>
                            </div>
                            <div style="display:flex; justify-content:space-between; gap:14px; font-size:13px; color:rgba(255,255,255,.84);">
                                <span>Change</span>
                                <strong>Rp {{ number_format($billing->change_amount ?: 0, 0, ',', '.') }}</strong>
                            </div>
                            <div style="display:flex; justify-content:space-between; gap:14px; font-size:13px; color:rgba(255,255,255,.84);">
                                <span>Remaining</span>
                                <strong>Rp {{ number_format($billing->remaining_amount ?: 0, 0, ',', '.') }}</strong>
                            </div>
                            <div class="total-row" style="border-top:1px solid rgba(255,255,255,.18); padding-top:14px;">
                                <div>
                                    <div class="total-label">Grand Total</div>
                                    <div style="margin-top:6px; font-size:12px; color:rgba(255,255,255,.78);">
                                        Total invoice pasien
                                    </div>
                                </div>
                                <div class="total-value">Rp {{ number_format($billing->amount, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Informasi Pembayaran & Catatan</h2>
                <p class="section-subtitle">Detail administratif yang tercatat pada invoice.</p>

                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Payment Method</div>
                        <div class="info-value">{{ $billing->payment_method_label }}</div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">Invoice Number</div>
                        <div class="info-value">{{ $billing->invoice_number ?: '-' }}</div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">Amount</div>
                        <div class="info-value">Rp {{ number_format($billing->amount, 0, ',', '.') }}</div>
                    </div>

                    <div class="info-card full">
                        <div class="info-label">Payment Detail Notes</div>
                        <div class="info-value">{{ $billing->payment_detail_notes ?: '-' }}</div>
                    </div>

                    <div class="info-card full">
                        <div class="info-label">General Notes</div>
                        <div class="info-value">{{ $billing->notes ?: '-' }}</div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
