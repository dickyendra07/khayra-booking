<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Inventory Summary - Khayra Physio</title>
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
        .container { max-width: 1380px; margin: 0 auto; }

        .topbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .badge {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef5f4;
            color: #35565d;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-bottom: 10px;
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
            align-items: center;
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

        .btn-soft {
            color: #2f7c7a;
            background: #ffffff;
            border: 1px solid #e6ebea;
        }

        .filter-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 22px;
            padding: 16px;
            box-shadow: 0 10px 26px rgba(15,23,42,.04);
            margin-bottom: 18px;
            display: flex;
            gap: 10px;
            align-items: end;
            flex-wrap: wrap;
        }

        .field label {
            display: block;
            margin-bottom: 7px;
            font-size: 11px;
            font-weight: 900;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .field input {
            min-height: 42px;
            border: 1px solid #d7dedd;
            border-radius: 13px;
            padding: 0 12px;
            background: #ffffff;
            font-size: 13px;
        }

        .hero {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(15,23,42,.05);
            margin-bottom: 18px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
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
            line-height: 1.1;
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
            grid-template-columns: 1fr 1fr;
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
            line-height: 1.85;
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

        .pill {
            display: inline-flex;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .in { background: #dcfce7; color: #166534; }
        .out { background: #fee2e2; color: #b91c1c; }
        .adjustment { background: #dbeafe; color: #1d4ed8; }

        @media (max-width: 1180px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .grid-2 { grid-template-columns: 1fr; }
        }

        @media (max-width: 760px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .title { font-size: 32px; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'inventory-summary'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Monthly Report</span>
                    <h1 class="title">Monthly Inventory Summary</h1>
                    <p class="subtitle">
                        Ringkasan inventory bulanan: stock in, stock out, adjustment, low stock, nilai stok, dan movement per barang.
                    </p>
                </div>

                <div class="actions">
                    <a href="/admin/inventory" class="btn btn-soft">← Inventory Control</a>
                    <a href="/admin/inventory/stock-opname" class="btn btn-primary">Stock Opname</a>
                </div>
            </div>

            <form method="GET" action="/admin/inventory/monthly-summary" class="filter-card">
                <div class="field">
                    <label>Pilih Bulan</label>
                    <input type="month" name="month" value="{{ $month }}">
                </div>

                <button type="submit" class="btn btn-primary">Tampilkan Summary</button>
            </form>

            <section class="hero">
                <span class="badge">{{ $summary['month'] }}</span>
                <h2 class="section-title">Ringkasan Bulan Ini</h2>
                <p class="section-subtitle">
                    Angka di bawah dihitung dari data inventory dan stock movement pada bulan yang dipilih.
                </p>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Barang</div>
                    <div class="stat-value">{{ $summary['total_items'] }}</div>
                    <div class="stat-sub">{{ $summary['active_items'] }} active item</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Low Stock</div>
                    <div class="stat-value">{{ $summary['low_stock_items'] }}</div>
                    <div class="stat-sub">Barang butuh perhatian</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Stock In</div>
                    <div class="stat-value">{{ $summary['stock_in'] }}</div>
                    <div class="stat-sub">Total barang masuk bulan ini</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Stock Out</div>
                    <div class="stat-value">{{ $summary['stock_out'] }}</div>
                    <div class="stat-sub">Total barang keluar bulan ini</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Adjustment</div>
                    <div class="stat-value">{{ $summary['adjustments'] }}</div>
                    <div class="stat-sub">Jumlah koreksi / opname</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Stock Value</div>
                    <div class="stat-value">Rp {{ number_format($summary['stock_value'], 0, ',', '.') }}</div>
                    <div class="stat-sub">Estimasi modal stok</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Potential Sales</div>
                    <div class="stat-value">Rp {{ number_format($summary['potential_sales'], 0, ',', '.') }}</div>
                    <div class="stat-sub">Estimasi nilai jual stok</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Movement Count</div>
                    <div class="stat-value">{{ $movements->count() }}</div>
                    <div class="stat-sub">Total movement bulan ini</div>
                </div>
            </section>

            <div class="grid-2">
                <section class="section-card">
                    <h2 class="section-title">Summary per Barang</h2>
                    <p class="section-subtitle">Akumulasi movement tiap barang pada bulan yang dipilih.</p>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Stock In</th>
                                    <th>Stock Out</th>
                                    <th>Adjustment</th>
                                    <th>Current Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($byItem as $row)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ optional($row['item'])->name ?: 'Barang terhapus' }}</div>
                                            <div class="secondary-text">{{ optional($row['item'])->sku ?: '-' }}</div>
                                        </td>
                                        <td>{{ $row['stock_in'] }}</td>
                                        <td>{{ $row['stock_out'] }}</td>
                                        <td>{{ $row['adjustments'] }}</td>
                                        <td>{{ $row['last_stock'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Belum ada movement pada bulan ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="section-card">
                    <h2 class="section-title">Movement Terbaru</h2>
                    <p class="section-subtitle">Riwayat stock movement bulan yang dipilih.</p>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Barang</th>
                                    <th>Type</th>
                                    <th>Qty</th>
                                    <th>Before</th>
                                    <th>After</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $movement)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ $movement->created_at ? $movement->created_at->format('Y-m-d') : '-' }}</div>
                                            <div class="secondary-text">{{ $movement->created_at ? $movement->created_at->format('H:i') : '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="primary-text">{{ optional($movement->inventoryItem)->name ?: '-' }}</div>
                                            <div class="secondary-text">{{ optional($movement->inventoryItem)->sku ?: '-' }}</div>
                                        </td>
                                        <td><span class="pill {{ $movement->type }}">{{ $movement->type }}</span></td>
                                        <td>{{ $movement->quantity }}</td>
                                        <td>{{ $movement->stock_before }}</td>
                                        <td>{{ $movement->stock_after }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">Belum ada stock movement pada bulan ini.</td>
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
</body>
</html>
