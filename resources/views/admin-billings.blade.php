<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Ledger - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f8f8;
            color: #17232b;
        }

        .layout {
            min-height: 100vh;
            display: flex;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 28px;
        }

        .container {
            max-width: 1380px;
            margin: 0 auto;
        }

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
            max-width: 860px;
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
            padding: 0 15px;
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

        .btn-primary {
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(47,124,122,.16);
        }

        .btn-dark {
            background: linear-gradient(135deg, #0f172a 0%, #1f2d3d 100%);
            color: #ffffff;
        }

        .btn-soft {
            color: #2f7c7a;
            background: #ffffff;
            border: 1px solid #e6ebea;
        }

        .btn-blue {
            background: #eef2ff;
            color: #3457d5;
            border: 1px solid #dde5ff;
        }

        .btn-danger {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #ffe0e6;
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
            grid-template-columns: 1.08fr .92fr;
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
                radial-gradient(circle at top right, rgba(255,255,255,.13), transparent 28%),
                linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px);
            background-size: auto, 56px 56px, 56px 56px;
            pointer-events: none;
        }

        .hero-side > * {
            position: relative;
            z-index: 1;
        }

        .hero-side h3 {
            margin: 0 0 10px;
            color: #ffffff;
            font-size: 26px;
            line-height: 1.2;
        }

        .hero-side p {
            margin: 0;
            font-size: 13px;
            line-height: 1.85;
            color: rgba(255,255,255,.92);
        }

        .snapshot-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
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
            font-size: 16px;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.45;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 10px 26px rgba(15,23,42,.04);
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #7b8794;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 27px;
            line-height: 1.05;
            font-weight: 900;
            color: #22343a;
            word-break: break-word;
        }

        .stat-sub {
            margin-top: 8px;
            font-size: 12px;
            line-height: 1.6;
            color: #94a3b8;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 26px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15,23,42,.04);
            margin-bottom: 18px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .section-title {
            margin: 0;
            font-size: 26px;
            color: #22343a;
            line-height: 1.2;
            font-weight: 900;
        }

        .section-subtitle {
            margin: 8px 0 0;
            font-size: 13px;
            line-height: 1.8;
            color: #6b7280;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1.4fr .8fr .8fr .8fr .8fr auto auto;
            gap: 12px;
            align-items: end;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 900;
            color: #334155;
        }

        input,
        select {
            width: 100%;
            height: 48px;
            padding: 0 13px;
            border: 1px solid #dde5e3;
            border-radius: 14px;
            font-size: 13px;
            background: #ffffff;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #176f69;
            box-shadow: 0 0 0 4px rgba(23,111,105,.08);
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid #edf1f0;
            border-radius: 20px;
            background: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1320px;
        }

        th {
            text-align: left;
            padding: 14px 15px;
            background: #f7faf9;
            color: #486168;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-bottom: 1px solid #edf1f0;
            white-space: nowrap;
        }

        td {
            padding: 15px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f2f5f5;
            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        tr.void-row {
            background: #f8fafc;
            opacity: .78;
        }

        .primary-text {
            font-weight: 900;
            color: #22343a;
            line-height: 1.45;
        }

        .secondary-text {
            margin-top: 4px;
            font-size: 11px;
            line-height: 1.55;
            color: #94a3b8;
        }

        .money {
            font-weight: 900;
            color: #22343a;
            white-space: nowrap;
        }

        .status-pill,
        .method-pill,
        .promo-pill {
            display: inline-flex;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .paid { background: #dcfce7; color: #166534; }
        .unpaid { background: #fee2e2; color: #b91c1c; }
        .partial { background: #fef3c7; color: #92400e; }
        .void { background: #e5e7eb; color: #374151; }

        .method-pill {
            background: #eef7f5;
            color: #2f7c7a;
        }

        .promo-pill {
            background: #eef2ff;
            color: #3457d5;
        }

        .action-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        form {
            margin: 0;
        }

        .mini-btn {
            min-height: 34px;
            padding: 0 11px;
            border-radius: 11px;
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: Arial, sans-serif;
            white-space: nowrap;
        }

        .mini-detail { background: #eef7f5; color: #2f7c7a; border-color: #d8ebe7; }
        .mini-print { background: #eef2ff; color: #3457d5; border-color: #dde5ff; }
        .mini-edit { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
        .mini-void { background: #fff1f2; color: #be123c; border-color: #ffe0e6; }

        .empty-state {
            padding: 28px;
            text-align: center;
            color: #6b7280;
            line-height: 1.8;
        }

        @media (max-width: 1280px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 980px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-grid, .snapshot-grid, .filter-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .title { font-size: 32px; }
        }

        @media (max-width: 640px) {
            .stats-grid { grid-template-columns: 1fr; }
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
                    <span class="badge">Kasir Ledger</span>
                    <h1 class="title">Billing & Transaction Ledger</h1>
                    <p class="subtitle">
                        Pusat monitoring transaksi kasir, invoice pasien, pembayaran, promo, status unpaid, dan void transaction.
                    </p>
                </div>

                <div class="actions">
                    <a href="/admin/cashier" class="btn btn-primary">+ Kasir Checkout</a>
                    <a href="/admin/billings/create" class="btn btn-soft">Create Manual Billing</a>
                    <a href="/admin/promos" class="btn btn-soft">Promo Setting</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <span class="badge">Finance Overview</span>
                        <h2 class="section-title">Ledger transaksi klinik dengan status pembayaran yang lebih jelas.</h2>
                        <p class="section-subtitle">
                            Gunakan filter untuk melihat transaksi berdasarkan tanggal, status, payment method, invoice, atau data pasien.
                            Void transaction tetap disimpan sebagai arsip audit, tapi tidak dihitung sebagai revenue aktif.
                        </p>
                    </div>

                    <aside class="hero-side">
                        <h3>Current Filter Snapshot</h3>
                        <p>Ringkasan cepat dari periode dan filter yang sedang aktif.</p>

                        <div class="snapshot-grid">
                            <div class="snapshot-card">
                                <div class="snapshot-label">Date Range</div>
                                <div class="snapshot-value">
                                    {{ $dateFrom ?: 'All' }} → {{ $dateTo ?: 'All' }}
                                </div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Status</div>
                                <div class="snapshot-value">{{ $status ? ucfirst($status) : 'All Status' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Method</div>
                                <div class="snapshot-value">{{ $paymentMethod ? ucwords(str_replace('_', ' ', $paymentMethod)) : 'All Methods' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Search</div>
                                <div class="snapshot-value">{{ $search ?: 'No keyword' }}</div>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Net Revenue</div>
                    <div class="stat-value">Rp {{ number_format($netRevenue, 0, ',', '.') }}</div>
                    <div class="stat-sub">Grand total non-void.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Gross Sales</div>
                    <div class="stat-value">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</div>
                    <div class="stat-sub">Subtotal sebelum diskon.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Discount</div>
                    <div class="stat-value">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</div>
                    <div class="stat-sub">Total promo / potongan.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Paid Amount</div>
                    <div class="stat-value">Rp {{ number_format($paidAmount, 0, ',', '.') }}</div>
                    <div class="stat-sub">{{ $paidCount }} paid transaction.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Outstanding</div>
                    <div class="stat-value">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</div>
                    <div class="stat-sub">{{ $unpaidCount }} unpaid · {{ $partialCount }} partial.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Void</div>
                    <div class="stat-value">{{ $voidCount }}</div>
                    <div class="stat-sub">{{ $totalTransactions }} total transaction in period.</div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Filter Ledger</h2>
                        <p class="section-subtitle">Cari invoice, nama pasien, nomor MR, WhatsApp, status pembayaran, metode bayar, dan periode transaksi.</p>
                    </div>
                </div>

                <form method="GET" action="/admin/billings">
                    <div class="filter-grid">
                        <div class="field">
                            <label>Search</label>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Invoice / nama pasien / MR / WhatsApp">
                        </div>

                        <div class="field">
                            <label>Status</label>
                            <select name="status">
                                <option value="">All Status</option>
                                <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="void" {{ $status === 'void' ? 'selected' : '' }}>Void</option>
                            </select>
                        </div>

                        <div class="field">
                            <label>Payment Method</label>
                            <select name="payment_method">
                                <option value="">All Methods</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method }}" {{ $paymentMethod === $method ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $method)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Date From</label>
                            <input type="date" name="date_from" value="{{ $dateFrom }}">
                        </div>

                        <div class="field">
                            <label>Date To</label>
                            <input type="date" name="date_to" value="{{ $dateTo }}">
                        </div>

                        <button type="submit" class="btn btn-dark">Apply Filter</button>
                        <a href="/admin/billings" class="btn btn-soft">Reset</a>
                    </div>
                </form>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Transaction List</h2>
                        <p class="section-subtitle">
                            Daftar invoice terbaru. Transaksi void tetap muncul untuk audit, tapi tampil beda dan tidak dihitung sebagai revenue aktif.
                        </p>
                    </div>

                    <a href="/admin/cashier" class="btn btn-primary">+ New Checkout</a>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Patient</th>
                                <th>Date</th>
                                <th>Subtotal</th>
                                <th>Discount</th>
                                <th>Grand Total</th>
                                <th>Paid</th>
                                <th>Remaining</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Promo</th>
                                <th>Items</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($billings as $billing)
                                <tr class="{{ $billing->payment_status === 'void' ? 'void-row' : '' }}">
                                    <td>
                                        <div class="primary-text">{{ $billing->invoice_number ?: 'Billing #' . $billing->id }}</div>
                                        <div class="secondary-text">ID: {{ $billing->id }}</div>
                                        @if($billing->voided_at)
                                            <div class="secondary-text">Void at {{ $billing->voided_at->format('Y-m-d H:i') }}</div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="primary-text">{{ optional($billing->patient)->full_name ?: '-' }}</div>
                                        <div class="secondary-text">
                                            MR: {{ optional($billing->patient)->medical_record_number ?: '-' }}
                                            @if(optional($billing->patient)->whatsapp)
                                                · WA: {{ optional($billing->patient)->whatsapp }}
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="primary-text">{{ $billing->invoice_date ? $billing->invoice_date->format('Y-m-d') : '-' }}</div>
                                        <div class="secondary-text">{{ $billing->created_at ? $billing->created_at->format('H:i') : '-' }}</div>
                                    </td>

                                    <td class="money">
                                        Rp {{ number_format($billing->subtotal_amount ?: $billing->amount, 0, ',', '.') }}
                                    </td>

                                    <td class="money">
                                        Rp {{ number_format($billing->discount_amount ?: 0, 0, ',', '.') }}
                                    </td>

                                    <td class="money">
                                        Rp {{ number_format($billing->amount, 0, ',', '.') }}
                                    </td>

                                    <td class="money">
                                        Rp {{ number_format($billing->paid_amount ?: 0, 0, ',', '.') }}
                                        @if(($billing->change_amount ?: 0) > 0)
                                            <div class="secondary-text">Change: Rp {{ number_format($billing->change_amount, 0, ',', '.') }}</div>
                                        @endif
                                    </td>

                                    <td class="money">
                                        Rp {{ number_format($billing->remaining_amount ?: 0, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        <span class="method-pill">{{ $billing->payment_method_label }}</span>
                                    </td>

                                    <td>
                                        <span class="status-pill {{ $billing->payment_status }}">{{ $billing->payment_status }}</span>
                                        @if($billing->original_payment_status)
                                            <div class="secondary-text">Before: {{ $billing->original_payment_status }}</div>
                                        @endif
                                    </td>

                                    <td>
                                        @if($billing->promo_code)
                                            <span class="promo-pill">{{ $billing->promo_code }}</span>
                                        @else
                                            <span class="secondary-text">No promo</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="primary-text">{{ $billing->items->count() }} item</div>
                                        <div class="secondary-text">
                                            {{ $billing->items->where('item_type', 'service')->count() }} service ·
                                            {{ $billing->items->where('item_type', 'inventory')->count() }} inventory
                                        </div>
                                    </td>

                                    <td>
                                        <div class="action-stack">
                                            <a href="/admin/billings/{{ $billing->id }}" class="mini-btn mini-detail">Detail</a>
                                            <a href="/admin/billings/{{ $billing->id }}/print" target="_blank" class="mini-btn mini-print">Print</a>

                                            @if($billing->payment_status !== 'void')
                                                <a href="/admin/billings/{{ $billing->id }}/edit" class="mini-btn mini-edit">Edit</a>
                                                <a href="/admin/billings/{{ $billing->id }}" class="mini-btn mini-void">Void</a>
                                            @else
                                                <span class="mini-btn mini-void">Voided</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13">
                                        <div class="empty-state">
                                            Belum ada transaksi yang sesuai filter ini.<br>
                                            Coba reset filter atau buat transaksi baru dari Kasir Checkout.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
