<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report - Khayra Physio</title>
    
<style>
    * { box-sizing: border-box; }
    body { margin:0; font-family:Arial, sans-serif; background:#f6f8f8; color:#17232b; }
    .layout { min-height:100vh; display:flex; }
    .main { flex:1; min-width:0; padding:28px; }
    .container { max-width:1380px; margin:0 auto; }
    .topbar { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:18px; }
    .badge { display:inline-flex; padding:8px 13px; border-radius:999px; background:#eef5f4; color:#35565d; font-size:12px; font-weight:900; text-transform:uppercase; letter-spacing:.06em; margin-bottom:12px; }
    .title { margin:0; font-size:42px; line-height:1.05; color:#22343a; font-weight:900; letter-spacing:-.7px; }
    .subtitle { margin:12px 0 0; max-width:900px; color:#6b7280; font-size:14px; line-height:1.9; }
    .actions { display:flex; gap:10px; flex-wrap:wrap; }
    .btn { min-height:42px; border:0; cursor:pointer; padding:0 16px; border-radius:14px; font-size:13px; font-weight:900; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; font-family:Arial,sans-serif; white-space:nowrap; }
    .btn-primary { background:linear-gradient(135deg,#3d8a89 0%,#2f7c7a 100%); color:#fff; box-shadow:0 12px 24px rgba(47,124,122,.16); }
    .btn-soft { color:#2f7c7a; background:#fff; border:1px solid #e6ebea; }
    .btn-dark { background:linear-gradient(135deg,#22343a 0%,#17232b 100%); color:#fff; }
    .nav-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:18px; }
    .nav-card { text-decoration:none; color:#22343a; background:#fff; border:1px solid #ecefef; border-radius:18px; padding:16px; font-weight:900; box-shadow:0 8px 20px rgba(15,23,42,.035); }
    .nav-card.active { background:#eef7f5; border-color:#bfe1dc; color:#2f7c7a; }
    .hero { background:#fff; border:1px solid #ecefef; border-radius:28px; padding:28px; box-shadow:0 14px 34px rgba(15,23,42,.05); margin-bottom:18px; }
    .hero-grid { display:grid; grid-template-columns:1.08fr .92fr; gap:18px; align-items:stretch; }
    .hero-main { background:linear-gradient(135deg,#fff 0%,#f7fbfa 58%,#eef7f5 100%); border:1px solid #dfeae8; border-radius:24px; padding:24px; }
    .hero-side { background:linear-gradient(145deg,#467f83 0%,#346d73 52%,#244f55 100%); color:#fff; border-radius:24px; padding:24px; }
    .hero-side h3 { margin:0 0 10px; font-size:26px; }
    .hero-side p { margin:0; font-size:13px; line-height:1.85; color:rgba(255,255,255,.92); }
    .filter-card { background:#fff; border:1px solid #ecefef; border-radius:24px; padding:22px; box-shadow:0 10px 26px rgba(15,23,42,.04); margin-bottom:18px; }
    .filter-form { display:grid; grid-template-columns:repeat(5,1fr) auto; gap:12px; align-items:end; }
    .field label { display:block; font-size:11px; font-weight:900; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; margin-bottom:8px; }
    .field input, .field select { width:100%; min-height:46px; border:1px solid #d7dedd; border-radius:14px; background:#fff; padding:0 14px; font-size:13px; outline:none; }
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:18px; }
    .stats-grid.five { grid-template-columns:repeat(5,1fr); }
    .stat-card { background:#fff; border:1px solid #ecefef; border-radius:22px; padding:20px; box-shadow:0 10px 26px rgba(15,23,42,.04); }
    .stat-label { font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; margin-bottom:12px; }
    .stat-value { font-size:31px; font-weight:900; line-height:1; color:#22343a; word-break:break-word; }
    .stat-sub { margin-top:9px; font-size:12px; color:#94a3b8; line-height:1.65; }
    .green .stat-value { color:#166534; } .blue .stat-value { color:#1d4ed8; } .orange .stat-value { color:#b45309; } .red .stat-value { color:#b91c1c; } .violet .stat-value { color:#6d28d9; }
    .section-card { background:#fff; border:1px solid #ecefef; border-radius:26px; padding:24px; box-shadow:0 10px 26px rgba(15,23,42,.04); margin-bottom:18px; }
    .section-title { margin:0 0 8px; font-size:26px; font-weight:900; color:#22343a; }
    .section-subtitle { margin:0 0 18px; color:#6b7280; font-size:13px; line-height:1.8; }
    .table-wrap { overflow-x:auto; border:1px solid #edf1f0; border-radius:20px; background:#fff; }
    table { width:100%; min-width:1100px; border-collapse:collapse; }
    th, td { text-align:left; padding:15px 14px; border-bottom:1px solid #edf1f0; vertical-align:top; font-size:13px; }
    th { background:#f7faf9; color:#486168; font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.05em; white-space:nowrap; }
    tbody tr:last-child td { border-bottom:0; }
    tbody tr:hover td { background:#fbfdfc; }
    .primary-text { font-weight:900; color:#22343a; line-height:1.45; }
    .secondary-text { margin-top:4px; font-size:11px; color:#94a3b8; line-height:1.55; }
    .money { font-weight:900; color:#22343a; white-space:nowrap; }
    .pill { display:inline-flex; padding:7px 11px; border-radius:999px; font-size:11px; font-weight:900; text-transform:uppercase; white-space:nowrap; }
    .pill-green { background:#dcfce7; color:#166534; }
    .pill-blue { background:#dbeafe; color:#1d4ed8; }
    .pill-orange { background:#fef3c7; color:#92400e; }
    .pill-red { background:#fee2e2; color:#b91c1c; }
    .pill-gray { background:#f1f5f9; color:#475569; }
    .two-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
    .empty-state { padding:18px; background:#fff7ed; color:#9a3412; border-radius:16px; border:1px solid #fed7aa; font-weight:800; line-height:1.7; }
    @media (max-width:1180px) {
        .nav-grid, .stats-grid, .stats-grid.five, .hero-grid, .two-grid { grid-template-columns:1fr 1fr; }
        .filter-form { grid-template-columns:1fr 1fr; }
    }
    @media (max-width:760px) {
        .layout { display:block; }
        .main { padding:16px; }
        .title { font-size:32px; }
        .nav-grid, .stats-grid, .stats-grid.five, .hero-grid, .two-grid, .filter-form { grid-template-columns:1fr; }
        .btn { width:100%; }
    }
</style>

</head>
<body>
@php $activeReport = 'inventory'; @endphp
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'reports'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Reporting Premium</span>
                    <h1 class="title">Inventory Report</h1>
                    <p class="subtitle">Laporan stok, stock value, potential sales, stok masuk/keluar, adjustment, low stock, empty stock, dan product usage.</p>
                </div>
                <div class="actions">
                    <a href="/admin/reports/inventory?month={{ $month }}&export=csv" class="btn btn-primary">Export CSV</a>
                    <a href="/admin/inventory" class="btn btn-soft">Inventory Control</a>
                </div>
            </div>

            
<div class="nav-grid">
    <a class="nav-card {{ ($activeReport ?? '') === 'monthly' ? 'active' : '' }}" href="/admin/reports/monthly-clinic">Monthly Clinic</a>
    <a class="nav-card {{ ($activeReport ?? '') === 'revenue' ? 'active' : '' }}" href="/admin/reports/revenue">Revenue</a>
    <a class="nav-card {{ ($activeReport ?? '') === 'inventory' ? 'active' : '' }}" href="/admin/reports/inventory">Inventory</a>
    <a class="nav-card {{ ($activeReport ?? '') === 'therapist' ? 'active' : '' }}" href="/admin/reports/therapist-performance">Therapist Performance</a>
</div>


            <section class="filter-card">
                <form method="GET" action="/admin/reports/inventory" class="filter-form">
                    <div class="field">
                        <label>Month</label>
                        <input type="month" name="month" value="{{ $month }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Lihat Report</button>
                </form>
            </section>

            <section class="stats-grid five">
                <div class="stat-card blue"><div class="stat-label">Total Items</div><div class="stat-value">{{ $summary['total_items'] }}</div><div class="stat-sub">Semua barang.</div></div>
                <div class="stat-card green"><div class="stat-label">Stock Value</div><div class="stat-value">Rp {{ number_format($summary['stock_value'], 0, ',', '.') }}</div><div class="stat-sub">Nilai modal stok.</div></div>
                <div class="stat-card violet"><div class="stat-label">Potential Sales</div><div class="stat-value">Rp {{ number_format($summary['potential_sales'], 0, ',', '.') }}</div><div class="stat-sub">Estimasi nilai jual.</div></div>
                <div class="stat-card orange"><div class="stat-label">Low Stock</div><div class="stat-value">{{ $summary['low_stock_items'] }}</div><div class="stat-sub">Barang menipis.</div></div>
                <div class="stat-card red"><div class="stat-label">Empty Stock</div><div class="stat-value">{{ $summary['empty_stock_items'] }}</div><div class="stat-sub">Barang habis.</div></div>
            </section>

            <section class="stats-grid">
                <div class="stat-card green"><div class="stat-label">Stock In</div><div class="stat-value">{{ $summary['stock_in'] }}</div><div class="stat-sub">Movement masuk bulan ini.</div></div>
                <div class="stat-card red"><div class="stat-label">Stock Out</div><div class="stat-value">{{ $summary['stock_out'] }}</div><div class="stat-sub">Movement keluar bulan ini.</div></div>
                <div class="stat-card blue"><div class="stat-label">Adjustments</div><div class="stat-value">{{ $summary['adjustments'] }}</div><div class="stat-sub">Opname / koreksi.</div></div>
                <div class="stat-card orange"><div class="stat-label">Product Usage</div><div class="stat-value">{{ $summary['product_usage_qty'] }}</div><div class="stat-sub">Qty produk di invoice valid.</div></div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Inventory Detail</h2>
                <p class="section-subtitle">Per barang: stok, status, value, movement, dan usage invoice.</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th><th>Category</th><th>Stock</th><th>Minimum</th><th>Status</th><th>Stock In</th><th>Stock Out</th><th>Adjustment</th><th>Usage Qty</th><th>Usage Value</th><th>Stock Value</th><th>Potential Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($itemRows as $row)
                                @php $item = $row['item']; @endphp
                                <tr>
                                    <td><div class="primary-text">{{ $item->name }}</div><div class="secondary-text">{{ $item->sku }} · {{ $item->unit }}</div></td>
                                    <td>{{ $item->category ?: '-' }}</td>
                                    <td>{{ $item->stock }}</td>
                                    <td>{{ $item->minimum_stock }}</td>
                                    <td><span class="pill {{ $item->stock_status === 'safe' ? 'pill-green' : ($item->stock_status === 'empty' ? 'pill-red' : 'pill-orange') }}">{{ $item->stock_status_label }}</span></td>
                                    <td>{{ $row['stock_in'] }}</td>
                                    <td>{{ $row['stock_out'] }}</td>
                                    <td>{{ $row['adjustments'] }}</td>
                                    <td>{{ $row['usage_qty'] }}</td>
                                    <td class="money">Rp {{ number_format($row['usage_value'], 0, ',', '.') }}</td>
                                    <td class="money">Rp {{ number_format($row['stock_value'], 0, ',', '.') }}</td>
                                    <td class="money">Rp {{ number_format($row['potential_sales'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Recent Stock Movements</h2>
                <p class="section-subtitle">Movement stok dalam bulan {{ $summary['month_label'] }}.</p>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Date</th><th>Item</th><th>Type</th><th>Qty</th><th>Before</th><th>After</th><th>Reference</th><th>Notes</th></tr></thead>
                        <tbody>
                            @forelse($movements->take(30) as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                    <td><div class="primary-text">{{ optional($movement->item)->name ?: '-' }}</div><div class="secondary-text">{{ optional($movement->item)->sku ?: '-' }}</div></td>
                                    <td><span class="pill {{ $movement->type === 'in' ? 'pill-green' : ($movement->type === 'out' ? 'pill-red' : 'pill-blue') }}">{{ $movement->type_label }}</span></td>
                                    <td>{{ $movement->quantity }}</td>
                                    <td>{{ $movement->stock_before }}</td>
                                    <td>{{ $movement->stock_after }}</td>
                                    <td>{{ $movement->reference ?: '-' }}</td>
                                    <td>{{ $movement->notes ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8">Belum ada stock movement bulan ini.</td></tr>
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
