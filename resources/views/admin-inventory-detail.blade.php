<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item->name }} - Inventory Detail</title>
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

        .kicker {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef5f4;
            color: #35565d;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .7px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .page-title {
            margin: 0;
            font-size: 42px;
            line-height: 1.04;
            color: #22343a;
            font-weight: 900;
            letter-spacing: -.8px;
        }

        .page-subtitle {
            margin: 12px 0 0;
            max-width: 880px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.9;
        }

        .top-actions {
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
        }

        .btn-primary {
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
        }

        .btn-dark {
            background: linear-gradient(135deg, #22343a 0%, #17232b 100%);
            color: #ffffff;
        }

        .btn-soft {
            color: #2f7c7a;
            background: #ffffff;
            border: 1px solid #e6ebea;
        }

        .btn-danger {
            color: #b91c1c;
            background: #fff1f2;
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

        .alert-error {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #ffe0e6;
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
            grid-template-columns: 1.2fr .8fr;
            gap: 20px;
            align-items: stretch;
        }

        .item-meta-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-top: 20px;
        }

        .meta-box {
            border: 1px solid #edf1f0;
            border-radius: 18px;
            background: #fbfcfc;
            padding: 16px;
        }

        .meta-label {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #7b8794;
            margin-bottom: 8px;
        }

        .meta-value {
            font-size: 15px;
            line-height: 1.6;
            color: #22343a;
            font-weight: 800;
            word-break: break-word;
        }

        .hero-side {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            border-radius: 24px;
            padding: 24px;
            color: #ffffff;
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

        .hero-side > * {
            position: relative;
            z-index: 1;
        }

        .side-title {
            margin: 0 0 8px;
            font-size: 28px;
            line-height: 1.2;
            color: #ffffff;
            font-weight: 900;
        }

        .side-text {
            margin: 0;
            font-size: 13px;
            line-height: 1.85;
            color: rgba(255,255,255,.92);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .active {
            background: #dcfce7;
            color: #166534;
        }

        .inactive {
            background: #f1f5f9;
            color: #475569;
        }

        .low-stock {
            background: #fff1f2;
            color: #be123c;
        }

        .safe-stock {
            background: #ecfdf5;
            color: #166534;
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
            padding: 18px;
            box-shadow: 0 10px 24px rgba(15,23,42,.04);
        }

        .stat-label {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #7b8794;
            margin-bottom: 12px;
        }

        .stat-value {
            font-size: 28px;
            line-height: 1;
            font-weight: 900;
            color: #22343a;
        }

        .stat-sub {
            margin-top: 8px;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.6;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: .85fr 1.15fr;
            gap: 18px;
            align-items: start;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 26px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15,23,42,.04);
        }

        .section-title {
            margin: 0;
            font-size: 26px;
            color: #22343a;
            font-weight: 900;
        }

        .section-subtitle {
            margin: 8px 0 18px;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        .movement-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }

        .tab-card {
            border: 1px solid #edf1f0;
            border-radius: 18px;
            padding: 14px;
            background: #fbfcfc;
        }

        .tab-title {
            font-size: 13px;
            font-weight: 900;
            color: #22343a;
            margin-bottom: 6px;
        }

        .tab-desc {
            font-size: 11px;
            line-height: 1.6;
            color: #7b8794;
        }

        .form-grid {
            display: grid;
            gap: 14px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 900;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #d7dedd;
            border-radius: 14px;
            background: #ffffff;
            color: #111827;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
            line-height: 1.7;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #2f7c7a;
            box-shadow: 0 0 0 4px rgba(47,124,122,.08);
        }

        .hint {
            margin-top: 6px;
            font-size: 12px;
            color: #7b8794;
            line-height: 1.6;
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid #edf1f0;
            border-radius: 20px;
            background: #ffffff;
        }

        table {
            width: 100%;
            min-width: 880px;
            border-collapse: collapse;
        }

        th,
        td {
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

        tbody tr:last-child td {
            border-bottom: 0;
        }

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

        .type-in {
            background: #dcfce7;
            color: #166534;
        }

        .type-out {
            background: #fee2e2;
            color: #b91c1c;
        }

        .type-adjustment {
            background: #dbeafe;
            color: #1d4ed8;
        }

        @media (max-width: 1240px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .grid-2,
            .hero-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .layout {
                display: block;
            }

            .main {
                padding: 16px;
            }

            .page-title {
                font-size: 32px;
            }

            .item-meta-grid,
            .stats-grid,
            .movement-tabs {
                grid-template-columns: 1fr;
            }

            .btn {
                width: 100%;
            }
        }
    
        .movement-history-card {
            width: 100%;
            max-width: none;
            grid-column: 1 / -1;
        }

        .movement-history-card .table-wrap {
            width: 100%;
        }

        .movement-history-card table {
            width: 100%;
        }

        .movement-history-card .section-card,
        .movement-history-card.section-card {
            width: 100%;
            max-width: none;
        }

    </style>
</head>
<body>
@php
    $movements = $item->stockMovements ?? collect();

    $stockIn = $movements->where('type', 'in')->sum('quantity');
    $stockOut = $movements->where('type', 'out')->sum('quantity');
    $adjustments = $movements->where('type', 'adjustment')->count();

    $summary = $summary ?? [
        'current_stock' => (int) ($item->stock ?? 0),
        'stock_in' => (int) $stockIn,
        'stock_out' => (int) $stockOut,
        'adjustments' => (int) $adjustments,
        'stock_value' => (float) (($item->stock ?? 0) * ($item->purchase_price ?? 0)),
        'potential_sales' => (float) (($item->stock ?? 0) * ($item->selling_price ?? 0)),
    ];
@endphp

<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'inventory'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="kicker">Inventory Detail</span>
                    <h1 class="page-title">{{ $item->name }}</h1>
                    <p class="page-subtitle">
                        Detail barang, status stok, harga, lokasi penyimpanan, dan riwayat pergerakan stok.
                    </p>
                </div>

                <div class="top-actions">
                    <a href="/admin/inventory" class="btn btn-soft">← Inventory Control</a>
                    <a href="/admin/inventory/{{ $item->id }}/edit" class="btn btn-soft">Edit Barang</a>
                    <a href="/admin/inventory/monthly-summary" class="btn btn-primary">Monthly Summary</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <span class="status-pill {{ $item->status }}">{{ ucfirst($item->status) }}</span>
                        @if($item->stock <= $item->minimum_stock)
                            <span class="status-pill low-stock">Low Stock</span>
                        @else
                            <span class="status-pill safe-stock">Stock Safe</span>
                        @endif

                        <div class="item-meta-grid">
                            <div class="meta-box">
                                <div class="meta-label">SKU</div>
                                <div class="meta-value">{{ $item->sku }}</div>
                            </div>

                            <div class="meta-box">
                                <div class="meta-label">Kategori</div>
                                <div class="meta-value">{{ $item->category ?: '-' }}</div>
                            </div>

                            <div class="meta-box">
                                <div class="meta-label">Unit</div>
                                <div class="meta-value">{{ $item->unit }}</div>
                            </div>

                            <div class="meta-box">
                                <div class="meta-label">Supplier</div>
                                <div class="meta-value">{{ $item->supplier ?: '-' }}</div>
                            </div>

                            <div class="meta-box">
                                <div class="meta-label">Lokasi</div>
                                <div class="meta-value">{{ $item->storage_location ?: '-' }}</div>
                            </div>

                            <div class="meta-box">
                                <div class="meta-label">Minimum Stock</div>
                                <div class="meta-value">{{ $item->minimum_stock }} {{ $item->unit }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="hero-side">
                        <h2 class="side-title">Stock Control</h2>
                        <p class="side-text">
                            Gunakan Stock In untuk barang masuk, Stock Out untuk barang keluar / dipakai,
                            dan Adjustment untuk koreksi stok opname.
                        </p>

                        <div style="margin-top: 20px;">
                            <div style="font-size: 58px; font-weight: 900; line-height: 1;">{{ $item->stock }}</div>
                            <div style="font-size: 13px; color: rgba(255,255,255,.86); margin-top: 8px;">Current Stock / {{ $item->unit }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Current Stock</div>
                    <div class="stat-value">{{ $summary['current_stock'] }}</div>
                    <div class="stat-sub">{{ $item->unit }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total In</div>
                    <div class="stat-value">{{ $summary['stock_in'] }}</div>
                    <div class="stat-sub">Akumulasi barang masuk</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total Out</div>
                    <div class="stat-value">{{ $summary['stock_out'] }}</div>
                    <div class="stat-sub">Akumulasi barang keluar</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Adjustments</div>
                    <div class="stat-value">{{ $summary['adjustments'] }}</div>
                    <div class="stat-sub">Jumlah koreksi stok</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Stock Value</div>
                    <div class="stat-value">Rp {{ number_format($summary['stock_value'], 0, ',', '.') }}</div>
                    <div class="stat-sub">Berdasarkan harga beli</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Potential Sales</div>
                    <div class="stat-value">Rp {{ number_format($summary['potential_sales'], 0, ',', '.') }}</div>
                    <div class="stat-sub">Berdasarkan harga jual</div>
                </div>
            </section>

            <div class="grid-2">
<section class="section-card movement-history-card">
                    <h2 class="section-title">Stock Movement History</h2>
                    <p class="section-subtitle">
                        Riwayat keluar masuk stok untuk barang ini.
                    </p>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Type</th>
                                    <th>Qty</th>
                                    <th>Before</th>
                                    <th>After</th>
                                    <th>Reference</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($item->stockMovements as $movement)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ $movement->created_at ? $movement->created_at->format('Y-m-d') : '-' }}</div>
                                            <div class="secondary-text">{{ $movement->created_at ? $movement->created_at->format('H:i') : '-' }}</div>
                                        </td>
                                        <td>
                                            <span class="status-pill type-{{ $movement->type }}">{{ str_replace('_', ' ', $movement->type) }}</span>
                                        </td>
                                        <td>{{ $movement->quantity }}</td>
                                        <td>{{ $movement->stock_before }}</td>
                                        <td>{{ $movement->stock_after }}</td>
                                        <td>{{ $movement->reference ?: '-' }}</td>
                                        <td>{{ $movement->notes ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">Belum ada stock movement untuk barang ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>

<script>
function updateQuantityHint() {
    const type = document.getElementById('movementType').value;
    const label = document.getElementById('quantityLabel');
    const hint = document.getElementById('quantityHint');

    if (type === 'in') {
        label.textContent = 'Quantity';
        hint.textContent = 'Isi jumlah barang yang masuk.';
    } else if (type === 'out') {
        label.textContent = 'Quantity';
        hint.textContent = 'Isi jumlah barang yang keluar / dipakai.';
    } else {
        label.textContent = 'Set Stock To';
        hint.textContent = 'Untuk adjustment, isi angka stok akhir yang benar setelah stok opname.';
    }
}
</script>
</body>
</html>
