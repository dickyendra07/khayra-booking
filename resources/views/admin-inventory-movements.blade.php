<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Movement - Khayra Physio</title>
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

        .hero {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(15,23,42,.05);
            margin-bottom: 18px;
        }

        .item-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
            margin-top: 20px;
        }

        .item-card {
            border: 1px solid #edf1f0;
            background: #fbfcfc;
            border-radius: 18px;
            padding: 16px;
        }

        .item-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #7b8794;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .item-value {
            font-size: 15px;
            color: #22343a;
            font-weight: 900;
            line-height: 1.5;
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
            font-size: 30px;
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

        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 26px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15,23,42,.04);
        }

        .section-title {
            margin: 0;
            font-size: 28px;
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
            min-width: 980px;
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

        @media (max-width: 980px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .item-grid, .stats-grid { grid-template-columns: 1fr; }
            .title { font-size: 32px; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'inventory'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Stock Movement</span>
                    <h1 class="title">{{ $item->name }}</h1>
                    <p class="subtitle">
                        Riwayat keluar masuk stok untuk barang ini. Halaman ini membantu admin melihat audit stok, adjustment, dan perubahan stok dari waktu ke waktu.
                    </p>
                </div>

                <div class="actions">
                    <a href="/admin/inventory" class="btn btn-soft">← Inventory Control</a>
                    <a href="/admin/inventory/{{ $item->id }}" class="btn btn-soft">Detail Barang</a>
                    <a href="/admin/inventory/stock-opname?item_id={{ $item->id }}" class="btn btn-primary">Stock Opname</a>
                </div>
            </div>

            <section class="hero">
                <span class="badge">Item Master</span>
                <h2 class="section-title">{{ $item->sku ?: '-' }} - {{ $item->name }}</h2>

                <div class="item-grid">
                    <div class="item-card">
                        <div class="item-label">Kategori</div>
                        <div class="item-value">{{ $item->category ?: '-' }}</div>
                    </div>

                    <div class="item-card">
                        <div class="item-label">Lokasi</div>
                        <div class="item-value">{{ $item->storage_location ?: '-' }}</div>
                    </div>

                    <div class="item-card">
                        <div class="item-label">Supplier</div>
                        <div class="item-value">{{ $item->supplier ?: '-' }}</div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Current Stock</div>
                    <div class="stat-value">{{ $summary['current_stock'] }}</div>
                    <div class="stat-sub">{{ $item->unit ?: 'unit' }}</div>
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
                    <div class="stat-label">Adjustment</div>
                    <div class="stat-value">{{ $summary['adjustments'] }}</div>
                    <div class="stat-sub">Jumlah koreksi stok</div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Movement History</h2>
                <p class="section-subtitle">Semua riwayat movement untuk barang ini.</p>

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
                            @forelse($movements as $movement)
                                <tr>
                                    <td>
                                        <div class="primary-text">{{ $movement->created_at ? $movement->created_at->format('Y-m-d') : '-' }}</div>
                                        <div class="secondary-text">{{ $movement->created_at ? $movement->created_at->format('H:i') : '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="pill {{ $movement->type }}">{{ $movement->type }}</span>
                                    </td>
                                    <td>{{ $movement->quantity }}</td>
                                    <td>{{ $movement->stock_before }}</td>
                                    <td>{{ $movement->stock_after }}</td>
                                    <td>{{ $movement->reference ?: '-' }}</td>
                                    <td>{{ $movement->notes ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">Belum ada movement untuk barang ini.</td>
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
