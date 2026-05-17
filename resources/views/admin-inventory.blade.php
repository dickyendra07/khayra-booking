<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Control - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1380px; margin: 0 auto; }

        .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
        .kicker { display: inline-flex; padding: 8px 12px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 900; letter-spacing: .7px; text-transform: uppercase; margin-bottom: 10px; }
        .page-title { margin: 0; font-size: 40px; line-height: 1.04; color: #22343a; font-weight: 900; letter-spacing: -.8px; }
        .page-subtitle { margin: 12px 0 0; max-width: 860px; color: #6b7280; font-size: 14px; line-height: 1.9; }

        .top-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { min-height: 42px; border: 0; cursor: pointer; padding: 0 16px; border-radius: 14px; color: #fff; font-size: 13px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; transition: transform .15s ease, opacity .15s ease; }
        .btn:hover { transform: translateY(-1px); opacity: .96; }
        .btn-primary { background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); box-shadow: 0 10px 20px rgba(47,124,122,.16); }
        .btn-dark { background: linear-gradient(135deg, #22343a 0%, #17232b 100%); }
        .btn-soft { color: #2f7c7a; background: #ffffff; border: 1px solid #e6ebea; }

        .shell { background: #ffffff; border: 1px solid #ecefef; border-radius: 30px; box-shadow: 0 14px 34px rgba(15,23,42,.05); overflow: hidden; }

        .hero-wrap { padding: 24px 24px 0; display: grid; grid-template-columns: 1fr; gap: 20px; }
        .hero-card { background: linear-gradient(135deg, #ffffff 0%, #f7fbfa 58%, #eef7f5 100%); border: 1px solid #dfeae8; border-radius: 28px; padding: 24px; position: relative; overflow: hidden; }
        .hero-card::after { content: ""; position: absolute; right: -50px; top: -50px; width: 180px; height: 180px; border-radius: 999px; background: radial-gradient(circle, rgba(47,124,122,.16) 0%, rgba(47,124,122,.04) 65%, transparent 80%); pointer-events: none; }
        .hero-heading { margin: 0 0 10px; font-size: 34px; font-weight: 900; line-height: 1.06; color: #22343a; position: relative; z-index: 1; }
        .hero-text { margin: 0; color: #6b7280; font-size: 14px; line-height: 1.85; max-width: 760px; position: relative; z-index: 1; }
        .rule-card { background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%); border-radius: 28px; padding: 24px; color: #fff; }
        .rule-title { margin: 0 0 14px; font-size: 22px; font-weight: 900; }
        .rule-line { font-size: 13px; line-height: 1.9; color: rgba(255,255,255,.92); }
        .rule-line strong { color: #fff; }

        .stats-grid { padding: 20px 24px 0; display: grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap: 16px; }
        .stat-link { color: inherit; text-decoration: none; display: block; }
        .stat-card { border-radius: 22px; padding: 20px; border: 1px solid #e8eeee; background: #ffffff; box-shadow: 0 10px 24px rgba(15,23,42,.04); min-height: 132px; transition: transform .15s ease, box-shadow .15s ease; }
        .stat-link:hover .stat-card { transform: translateY(-2px); box-shadow: 0 16px 26px rgba(15,23,42,.07); }
        .stat-card.green { background: linear-gradient(180deg, #f3fbf7 0%, #ffffff 100%); border-color: #d8f0de; }
        .stat-card.blue { background: linear-gradient(180deg, #f5f9ff 0%, #ffffff 100%); border-color: #dbe7ff; }
        .stat-card.orange { background: linear-gradient(180deg, #fff8ed 0%, #ffffff 100%); border-color: #fed7aa; }
        .stat-card.red { background: linear-gradient(180deg, #fff5f5 0%, #ffffff 100%); border-color: #fecaca; }
        .stat-card.violet { background: linear-gradient(180deg, #f8f7ff 0%, #ffffff 100%); border-color: #ddd6fe; }
        .stat-label { font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; margin-bottom: 14px; }
        .stat-value { font-size: 34px; font-weight: 900; line-height: 1; margin-bottom: 10px; color: #22343a; }
        .stat-desc { font-size: 12px; color: #6b7280; line-height: 1.7; }
        .green .stat-value { color: #166534; } .blue .stat-value { color: #1d4ed8; } .orange .stat-value { color: #b45309; } .red .stat-value { color: #b91c1c; } .violet .stat-value { color: #6d28d9; }

        .action-grid { padding: 20px 24px 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .action-card { text-decoration: none; color: inherit; background: #ffffff; border: 1px solid #e8eeee; border-radius: 24px; padding: 20px; box-shadow: 0 10px 24px rgba(15,23,42,.04); min-height: 164px; display: flex; flex-direction: column; justify-content: space-between; transition: transform .15s ease, box-shadow .15s ease; }
        .action-card:hover { transform: translateY(-2px); box-shadow: 0 16px 26px rgba(15,23,42,.07); }
        .action-icon { width: 54px; height: 54px; border-radius: 18px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; font-size: 24px; }
        .icon-green { background: #eefaf1; color: #166534; }
        .icon-blue { background: #eff6ff; color: #1d4ed8; }
        .icon-orange { background: #fff7ed; color: #b45309; }
        .icon-violet { background: #f5f3ff; color: #6d28d9; }
        .action-title { font-size: 20px; font-weight: 900; color: #22343a; margin-bottom: 8px; }
        .action-desc { font-size: 13px; color: #6b7280; line-height: 1.7; }
        .action-link { margin-top: 14px; font-size: 13px; font-weight: 900; color: #2f7c7a; }

        .panel-grid { padding: 20px 24px 24px; display: grid; grid-template-columns: 1fr; gap: 20px; }
        .section-card { background: #ffffff; border: 1px solid #e8eeee; border-radius: 26px; box-shadow: 0 10px 24px rgba(15,23,42,.04); overflow: hidden; }
        .section-head { padding: 22px 22px 0; }
        .section-title { margin: 0 0 8px; font-size: 24px; font-weight: 900; color: #22343a; }
        .section-subtitle { margin: 0 0 18px; color: #6b7280; font-size: 13px; line-height: 1.8; }

        .filter-form { padding: 0 22px 22px; display: grid; grid-template-columns: 1.3fr .8fr .8fr .8fr auto; gap: 12px; align-items: end; }
        .field label { display: block; font-size: 11px; font-weight: 900; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 8px; }
        .field input, .field select { width: 100%; min-height: 46px; border: 1px solid #d7dedd; border-radius: 14px; background: #fff; padding: 0 14px; font-size: 13px; outline: none; }
        .field input:focus, .field select:focus { border-color: rgba(47,124,122,.75); box-shadow: 0 0 0 4px rgba(47,124,122,.10); }

        .table-wrap { padding: 0 22px 22px; overflow-x: auto; }
        table { width: 100%; min-width: 1180px; border-collapse: collapse; background: #fff; border: 1px solid #e8eeee; border-radius: 18px; overflow: hidden; }
        th, td { text-align: left; padding: 15px 14px; border-bottom: 1px solid #edf1f0; vertical-align: middle; font-size: 13px; }
        th { background: #f7faf9; color: #486168; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: #fcfdfd; }
        .primary-text { font-weight: 900; color: #22343a; line-height: 1.45; }
        .secondary-text { margin-top: 4px; font-size: 11px; color: #94a3b8; line-height: 1.55; }
        .qty-value { font-weight: 900; color: #1d4ed8; }
        .qty-zero { color: #b91c1c !important; background: #fff1f1; border-radius: 10px; padding: 8px 10px; display: inline-flex; align-items: center; font-weight: 900; }
        .status-badge { display: inline-flex; align-items: center; padding: 8px 12px; border-radius: 999px; font-size: 11px; font-weight: 900; white-space: nowrap; }
        .status-safe { background: #eefaf1; color: #166534; }
        .status-low { background: #fff7ed; color: #b45309; }
        .status-empty { background: #fff1f1; color: #b91c1c; }
        .status-inactive { background: #f1f5f9; color: #475569; }
        .action-stack { display: flex; flex-wrap: wrap; gap: 8px; }
        .mini-link { display: inline-flex; align-items: center; justify-content: center; text-decoration: none; padding: 9px 12px; border-radius: 12px; font-size: 12px; font-weight: 900; border: 1px solid transparent; }
        .mini-detail { background: #eef7f5; color: #2f7c7a; border-color: #d8ebe7; }
        .mini-edit { background: #eef2ff; color: #3457d5; border-color: #dde5ff; }

        .note-list { padding: 0 22px 22px; display: grid; gap: 14px; }
        .note-card { border: 1px solid #e8eeee; background: linear-gradient(180deg, #ffffff 0%, #fbfcfc 100%); border-radius: 20px; padding: 16px; }
        .note-title { font-size: 16px; font-weight: 900; color: #22343a; margin-bottom: 8px; }
        .note-desc { font-size: 13px; line-height: 1.75; color: #6b7280; }
        .note-highlight { color: #22343a; font-weight: 900; }

        .movement-list { padding: 0 22px 22px; display: grid; gap: 12px; }
        .movement-item { border: 1px solid #edf1f0; border-radius: 18px; padding: 15px; background: #fbfcfc; }
        .movement-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
        .movement-title { font-size: 14px; font-weight: 900; color: #22343a; }
        .movement-meta { margin-top: 6px; font-size: 12px; color: #6b7280; line-height: 1.7; }
        .movement-plus { color: #166534; font-weight: 900; }
        .movement-minus { color: #b91c1c; font-weight: 900; }
        .movement-neutral { color: #1d4ed8; font-weight: 900; }

        .empty-state { margin: 0 22px 22px; padding: 18px; background: #fff7ed; color: #9a3412; border-radius: 16px; font-weight: 800; border: 1px solid #fed7aa; line-height: 1.7; }

        @media (max-width: 1320px) {
            .hero-wrap, .panel-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr 1fr; }
            .action-grid { grid-template-columns: 1fr 1fr; }
            .filter-form { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 860px) {
            .main { padding: 16px; }
            .layout { display: block; }
            .page-title { font-size: 32px; }
            .hero-heading { font-size: 28px; }
            .stats-grid, .action-grid, .filter-form { grid-template-columns: 1fr; }
            .top-actions { width: 100%; }
            .btn { width: 100%; }
        }
    
        .bulk-panel {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 22px;
            padding: 16px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
            margin-bottom: 18px;
        }
        .bulk-form {
            display: flex;
            gap: 10px;
            align-items: end;
            flex-wrap: wrap;
        }
        .bulk-field label {
            display: block;
            margin-bottom: 7px;
            font-size: 11px;
            font-weight: 900;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .bulk-field select,
        .bulk-field input {
            min-height: 42px;
            border: 1px solid #d7dedd;
            border-radius: 13px;
            padding: 0 12px;
            background: #ffffff;
            font-size: 13px;
            min-width: 190px;
        }
        .bulk-help {
            margin-top: 10px;
            color: #7b8794;
            font-size: 12px;
            line-height: 1.6;
        }
        .bulk-checkbox {
            width: 18px;
            height: 18px;
            accent-color: #2f7c7a;
        }

    
        .import-export-panel {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 22px;
            padding: 16px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
            margin-bottom: 18px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 14px;
            align-items: center;
        }
        .import-title {
            font-size: 15px;
            font-weight: 900;
            color: #22343a;
            margin-bottom: 5px;
        }
        .import-help {
            color: #7b8794;
            font-size: 12px;
            line-height: 1.7;
        }
        .import-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
        }
        .file-input {
            max-width: 260px;
            padding: 10px;
            border: 1px solid #d7dedd;
            border-radius: 13px;
            background: #ffffff;
            font-size: 12px;
        }
        @media (max-width: 900px) {
            .import-export-panel {
                grid-template-columns: 1fr;
            }
            .import-actions {
                justify-content: flex-start;
            }
        }

    
        /* Inventory vertical layout polish */
        .inventory-layout,
        .content-grid,
        .main-grid,
        .dashboard-grid,
        .grid-2 {
            grid-template-columns: 1fr !important;
        }

        .side-card,
        .right-panel,
        .need-action-card,
        .recent-movements-card {
            width: 100%;
        }

        .import-export-panel {
            grid-template-columns: 1fr !important;
            align-items: stretch !important;
        }

        .import-actions {
            justify-content: flex-start !important;
        }

        .bulk-form {
            align-items: stretch !important;
        }

        .bulk-field {
            flex: 1;
            min-width: 220px;
        }

        .bulk-field select,
        .bulk-field input {
            width: 100%;
        }

        .table-wrap {
            width: 100%;
        }

    
        /* Inventory list polish */
        .inventory-table-wrap {
            overflow-x: auto;
            border: 1px solid #edf1f0;
            border-radius: 22px;
            background: #ffffff;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.035);
        }

        .inventory-table {
            width: 100%;
            min-width: 1180px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .inventory-table th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #f7faf9;
            color: #486168;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 15px 14px;
            border-bottom: 1px solid #edf1f0;
            white-space: nowrap;
        }

        .inventory-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #f0f4f3;
            vertical-align: top;
            font-size: 13px;
            color: #334155;
            background: #ffffff;
        }

        .inventory-table tbody tr:hover td {
            background: #fbfdfc;
        }

        .inventory-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .inventory-item-title {
            font-size: 14px;
            line-height: 1.45;
            color: #22343a;
            font-weight: 900;
            margin-bottom: 5px;
        }

        .inventory-item-meta {
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.6;
        }

        .inventory-muted {
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.6;
            margin-top: 4px;
        }

        .stock-number {
            display: inline-flex;
            align-items: baseline;
            gap: 5px;
            font-weight: 900;
            color: #22343a;
            font-size: 16px;
        }

        .stock-unit {
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
        }

        .stock-pill {
            display: inline-flex;
            margin-top: 8px;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
        }

        .stock-safe {
            background: #dcfce7;
            color: #166534;
        }

        .stock-low {
            background: #fef3c7;
            color: #92400e;
        }

        .stock-empty {
            background: #fee2e2;
            color: #b91c1c;
        }

        .item-status {
            display: inline-flex;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .item-active {
            background: #eef7f5;
            color: #2f7c7a;
        }

        .item-inactive {
            background: #f1f5f9;
            color: #64748b;
        }

        .price-stack {
            display: grid;
            gap: 7px;
        }

        .price-line {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            min-width: 150px;
            font-size: 12px;
            line-height: 1.4;
        }

        .price-label {
            color: #94a3b8;
            font-weight: 800;
        }

        .price-value {
            color: #22343a;
            font-weight: 900;
            white-space: nowrap;
        }

        .inventory-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            min-width: 250px;
        }

        .inventory-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: 0 11px;
            border-radius: 11px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 900;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .action-detail {
            background: #eef7f5;
            color: #2f7c7a;
            border-color: #d8ebe7;
        }

        .action-edit {
            background: #eef2ff;
            color: #3457d5;
            border-color: #dde5ff;
        }

        .action-movement {
            background: #f8fafc;
            color: #475569;
            border-color: #e2e8f0;
        }

        .action-opname {
            background: #fff7ed;
            color: #c2410c;
            border-color: #fed7aa;
        }

        .sticky-action {
            position: sticky;
            right: 0;
            z-index: 1;
            box-shadow: -8px 0 16px rgba(255,255,255,.88);
        }

        .inventory-table th.sticky-action {
            z-index: 3;
            background: #f7faf9;
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
                    <span class="kicker">Inventory Control</span>
                    <h1 class="page-title">Stock control untuk barang klinik Khayra.</h1>
                    <p class="page-subtitle">
                        Pantau stok saat ini, stok minimum, barang menipis, barang habis, dan riwayat movement barang klinik dengan flow yang lebih mirip POS.
                    </p>
                </div>

                <div class="top-actions">
                    <a href="/admin/inventory/create" class="btn btn-primary">+ Tambah Barang</a>
                    <a href="/admin/inventory/import" class="btn btn-soft">Import CSV</a>
                    <a href="/admin/inventory/export/csv" class="btn btn-soft">Export CSV</a>
                    <a href="/admin/inventory/monthly-summary" class="btn btn-soft">Monthly Summary</a>
                    <a href="/admin/inventory/stock-opname" class="btn btn-primary">Stock Opname</a>
                    <a href="/admin/inventory/product-usage" class="btn btn-soft">Product Usage</a>
                    <a href="/admin/inventory/stock-movements" class="btn btn-dark">Stock Movements</a>
                </div>
            </div>

            <section class="shell">
                <div class="hero-wrap">
                    <div class="hero-card">
                        <span class="kicker">Stock Balance</span>
                        <h2 class="hero-heading">Satu layar untuk melihat kondisi stok dan kebutuhan restock.</h2>
                        <p class="hero-text">
                            Inventory Control menampilkan balance stok per barang, nilai modal, status stok, serta item yang butuh action.
                            Untuk Khayra, modul ini cocok untuk consumable, alat terapi, produk jual, dan kebutuhan operasional klinik.
                        </p>
                    </div>

                    <div class="rule-card">
                        <h3 class="rule-title">Aturan status stok</h3>
                        <div class="rule-line"><strong>Aman</strong> jika stok di atas minimum.</div>
                        <div class="rule-line"><strong>Menipis</strong> jika stok masih ada tapi sudah mencapai minimum.</div>
                        <div class="rule-line"><strong>Habis</strong> jika stok nol atau kurang.</div>
                    </div>
                </div>

                <div class="stats-grid">
                    <a href="/admin/inventory" class="stat-link">
                        <div class="stat-card blue">
                            <div class="stat-label">Total Items</div>
                            <div class="stat-value">{{ $totalItems }}</div>
                            <div class="stat-desc">Semua barang inventory.</div>
                        </div>
                    </a>

                    <a href="/admin/inventory?status=active" class="stat-link">
                        <div class="stat-card green">
                            <div class="stat-label">Active Items</div>
                            <div class="stat-value">{{ $activeItems }}</div>
                            <div class="stat-desc">Barang aktif digunakan.</div>
                        </div>
                    </a>

                    <div class="stat-card violet">
                        <div class="stat-label">Stock Value</div>
                        <div class="stat-value" style="font-size:24px;">Rp {{ number_format($stockValue, 0, ',', '.') }}</div>
                        <div class="stat-desc">Nilai modal stok saat ini.</div>
                    </div>

                    <a href="/admin/inventory?stock_status=low" class="stat-link">
                        <div class="stat-card orange">
                            <div class="stat-label">Low Stock</div>
                            <div class="stat-value">{{ $lowStockItems }}</div>
                            <div class="stat-desc">Butuh restock segera.</div>
                        </div>
                    </a>

                    <a href="/admin/inventory?stock_status=empty" class="stat-link">
                        <div class="stat-card red">
                            <div class="stat-label">Out of Stock</div>
                            <div class="stat-value">{{ $emptyStockItems }}</div>
                            <div class="stat-desc">Barang habis.</div>
                        </div>
                    </a>
                </div>

                <div class="stats-grid" style="padding-top:16px;">
                    <div class="stat-card green">
                        <div class="stat-label">Potential Sales</div>
                        <div class="stat-value" style="font-size:24px;">Rp {{ number_format($potentialSalesValue, 0, ',', '.') }}</div>
                        <div class="stat-desc">Estimasi nilai jual stok.</div>
                    </div>

                    <a href="/admin/inventory/stock-movements" class="stat-link">
                        <div class="stat-card blue">
                            <div class="stat-label">Movement This Month</div>
                            <div class="stat-value">{{ $monthlyMovementCount }}</div>
                            <div class="stat-desc">Total transaksi stok bulan ini.</div>
                        </div>
                    </a>

                    <a href="/admin/inventory/stock-movements?type=in" class="stat-link">
                        <div class="stat-card green">
                            <div class="stat-label">Stock In</div>
                            <div class="stat-value">{{ $monthlyStockIn }}</div>
                            <div class="stat-desc">Barang masuk bulan ini.</div>
                        </div>
                    </a>

                    <a href="/admin/inventory/stock-movements?type=out" class="stat-link">
                        <div class="stat-card red">
                            <div class="stat-label">Stock Out</div>
                            <div class="stat-value">{{ $monthlyStockOut }}</div>
                            <div class="stat-desc">Barang keluar bulan ini.</div>
                        </div>
                    </a>

                    <a href="/admin/inventory/stock-opname" class="stat-link">
                        <div class="stat-card orange">
                            <div class="stat-label">Adjustments</div>
                            <div class="stat-value">{{ $monthlyAdjustmentCount }}</div>
                            <div class="stat-desc">Opname / adjustment bulan ini.</div>
                        </div>
                    </a>
                </div>

                <div class="action-grid">
                    <a href="/admin/inventory/create" class="action-card">
                        <div>
                            <div class="action-icon icon-green">＋</div>
                            <div class="action-title">Tambah Barang</div>
                            <div class="action-desc">Buat barang baru beserta stok awal, minimum stok, harga beli, harga jual, dan lokasi.</div>
                        </div>
                        <div class="action-link">Create item →</div>
                    </a>

                    <a href="/admin/inventory/stock-movements" class="action-card">
                        <div>
                            <div class="action-icon icon-blue">↕</div>
                            <div class="action-title">Stock Movements</div>
                            <div class="action-desc">Lihat semua riwayat stok masuk, keluar, adjustment, dan catatan referensi.</div>
                        </div>
                        <div class="action-link">View movements →</div>
                    </a>

                    <a href="/admin/inventory/stock-opname" class="action-card">
                        <div>
                            <div class="action-icon icon-violet">✓</div>
                            <div class="action-title">Stock Opname</div>
                            <div class="action-desc">Cek stok fisik, hitung selisih, dan buat adjustment stok otomatis untuk audit.</div>
                        </div>
                        <div class="action-link">Start opname →</div>
                    </a>

                    <a href="/admin/inventory/product-usage" class="action-card">
                        <div>
                            <div class="action-icon icon-blue">◎</div>
                            <div class="action-title">Product Usage</div>
                            <div class="action-desc">Lihat produk inventory yang dipakai atau terjual berdasarkan invoice, visit, dan treatment.</div>
                        </div>
                        <div class="action-link">View usage →</div>
                    </a>

                    <a href="/admin/inventory?stock_status=low" class="action-card">
                        <div>
                            <div class="action-icon icon-orange">!</div>
                            <div class="action-title">Low Stock</div>
                            <div class="action-desc">Filter barang yang sudah menipis dan perlu dipantau untuk restock.</div>
                        </div>
                        <div class="action-link">Check low stock →</div>
                    </a>

                    <a href="/admin/inventory?stock_status=empty" class="action-card">
                        <div>
                            <div class="action-icon icon-violet">0</div>
                            <div class="action-title">Out of Stock</div>
                            <div class="action-desc">Lihat barang yang habis agar bisa segera dipesan ulang atau disesuaikan.</div>
                        </div>
                        <div class="action-link">Check empty stock →</div>
                    </a>
                </div>

                <div class="panel-grid">
                    <section class="section-card">
                        <div class="section-head">
                            <h2 class="section-title">Stock Balance</h2>
                            <p class="section-subtitle">Daftar barang dengan stok saat ini, minimum stok, harga, lokasi, dan status.</p>
                        </div>

                        <form method="GET" action="/admin/inventory" class="filter-form">
                            <div class="field">
                                <label>Search</label>
                                <input type="text" name="search" value="{{ $search }}" placeholder="Nama barang, SKU, supplier, lokasi...">
                            </div>

                            <div class="field">
                                <label>Category</label>
                                <select name="category">
                                    <option value="">Semua</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field">
                                <label>Stock Status</label>
                                <select name="stock_status">
                                    <option value="">Semua</option>
                                    <option value="safe" {{ $stockStatus === 'safe' ? 'selected' : '' }}>Aman</option>
                                    <option value="low" {{ $stockStatus === 'low' ? 'selected' : '' }}>Menipis</option>
                                    <option value="empty" {{ $stockStatus === 'empty' ? 'selected' : '' }}>Habis</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>Item Status</label>
                                <select name="status">
                                    <option value="">Semua</option>
                                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>

                        @if($items->count())
            

                <div class="import-export-panel">
                    <div>
                        <div class="import-title">Import / Export Inventory</div>
                        <div class="import-help">
                            Export untuk backup atau edit massal. Import CSV wajib punya kolom <strong>sku</strong> dan <strong>name</strong>.
                            Kolom lain yang didukung: category, unit, stock, minimum_stock, purchase_price, selling_price, supplier, storage_location, status, notes.
                        </div>
                    </div>

                    <div class="import-actions">
                        <a href="/admin/inventory/export" class="btn btn-primary">Export CSV</a>

                        <form method="POST" action="/admin/inventory/import" enctype="multipart/form-data" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                            @csrf
                            <input type="file" name="inventory_file" accept=".csv,text/csv" class="file-input" required>
                            <button type="submit" class="btn btn-dark">Import CSV</button>
                        </form>
                    </div>
                </div>

                <form id="bulkInventoryForm" method="POST" action="/admin/inventory/bulk-action" class="bulk-panel">
                    @csrf
                    <div class="bulk-form">
                        <div class="bulk-field">
                            <label>Bulk Action</label>
                            <select name="bulk_action" required>
                                <option value="">Pilih action</option>
                                <option value="set_active">Set Active</option>
                                <option value="set_inactive">Set Inactive</option>
                                <option value="update_category">Update Category</option>
                                <option value="update_location">Update Location</option>
                            </select>
                        </div>

                        <div class="bulk-field">
                            <label>Value</label>
                            <input type="text" name="bulk_value" placeholder="Isi untuk category / location">
                        </div>

                        <button type="submit" class="btn btn-primary">Apply ke Barang Terpilih</button>
                    </div>

                    <div class="bulk-help">
                        Centang barang di tabel, lalu pilih action. Untuk Set Active / Set Inactive, kolom Value boleh dikosongkan.
                    </div>
                </form>

                
                <div class="inventory-table-wrap">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Barang</th>
                                <th>Kategori / Lokasi</th>
                                <th>Stok</th>
                                <th>Minimum</th>
                                <th>Harga</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th class="sticky-action">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                @php
                                    $stock = (int) ($item->stock ?? 0);
                                    $minimum = (int) ($item->minimum_stock ?? 0);
                                    $stockClass = 'stock-safe';
                                    $stockLabel = 'Aman';

                                    if ($stock <= 0) {
                                        $stockClass = 'stock-empty';
                                        $stockLabel = 'Empty';
                                    } elseif ($minimum > 0 && $stock <= $minimum) {
                                        $stockClass = 'stock-low';
                                        $stockLabel = 'Low Stock';
                                    }

                                    $statusClass = ($item->status ?? 'active') === 'active' ? 'item-active' : 'item-inactive';
                                @endphp

                                <tr>
                                    <td>
                                        <input class="bulk-checkbox" type="checkbox" form="bulkInventoryForm" name="item_ids[]" value="{{ $item->id }}">
                                    </td>

                                    <td>
                                        <div class="inventory-item-title">{{ $item->name }}</div>
                                        <div class="inventory-item-meta">
                                            SKU: {{ $item->sku ?: '-' }} · Unit: {{ $item->unit ?: '-' }}
                                        </div>
                                        @if($item->notes)
                                            <div class="inventory-muted">{{ $item->notes }}</div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="inventory-item-title">{{ $item->category ?: '-' }}</div>
                                        <div class="inventory-muted">{{ $item->storage_location ?: 'Lokasi belum diisi' }}</div>
                                    </td>

                                    <td>
                                        <div class="stock-number">
                                            {{ $stock }} <span class="stock-unit">{{ $item->unit ?: 'unit' }}</span>
                                        </div>
                                        <div>
                                            <span class="stock-pill {{ $stockClass }}">{{ $stockLabel }}</span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="stock-number">
                                            {{ $minimum }} <span class="stock-unit">{{ $item->unit ?: 'unit' }}</span>
                                        </div>
                                        <div class="inventory-muted">Minimum restock</div>
                                    </td>

                                    <td>
                                        <div class="price-stack">
                                            <div class="price-line">
                                                <span class="price-label">Beli</span>
                                                <span class="price-value">Rp {{ number_format($item->purchase_price ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="price-line">
                                                <span class="price-label">Jual</span>
                                                <span class="price-value">Rp {{ number_format($item->selling_price ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="inventory-item-title">{{ $item->supplier ?: '-' }}</div>
                                        <div class="inventory-muted">Vendor / supplier</div>
                                    </td>

                                    <td>
                                        <span class="item-status {{ $statusClass }}">{{ $item->status ?: 'active' }}</span>
                                    </td>

                                    <td class="sticky-action">
                                        <div class="inventory-actions">
                                            <a href="/admin/inventory/{{ $item->id }}" class="inventory-action action-detail">Detail</a>
                                            <a href="/admin/inventory/{{ $item->id }}/edit" class="inventory-action action-edit">Edit</a>
                                            <a href="/admin/inventory/{{ $item->id }}/movements" class="inventory-action action-movement">Movement</a>
                                            <a href="/admin/inventory/stock-opname?item_id={{ $item->id }}" class="inventory-action action-opname">Opname</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">Belum ada barang inventory yang tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                        @else
                            <div class="empty-state">Tidak ada barang yang cocok dengan filter saat ini.</div>
                        @endif
                    </section>

                    <div>
                        <section class="section-card" style="margin-bottom:20px;">
                            <div class="section-head">
                                <h2 class="section-title">Need Action</h2>
                                <p class="section-subtitle">Barang yang stoknya menipis atau habis.</p>
                            </div>

                            @if($needActionItems->count())
                                <div class="note-list">
                                    @foreach($needActionItems->take(6) as $item)
                                        <div class="note-card">
                                            <div class="note-title">{{ $item->name }}</div>
                                            <div class="note-desc">
                                                Stok <span class="note-highlight">{{ $item->stock }} {{ $item->unit }}</span>,
                                                minimum <span class="note-highlight">{{ $item->minimum_stock }} {{ $item->unit }}</span>.
                                                @if($item->stock_status === 'empty')
                                                    Segera restock atau adjustment stok.
                                                @else
                                                    Pantau stok dan siapkan pembelian berikutnya.
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">Belum ada barang yang butuh action.</div>
                            @endif
                        </section>

                        <section class="section-card">
                            <div class="section-head">
                                <h2 class="section-title">Recent Movements</h2>
                                <p class="section-subtitle">Riwayat stok terbaru dari seluruh barang.</p>
                            </div>

                            @if($recentMovements->count())
                                <div class="movement-list">
                                    @foreach($recentMovements as $movement)
                                        <div class="movement-item">
                                            <div class="movement-top">
                                                <div>
                                                    <div class="movement-title">{{ optional($movement->item)->name ?: 'Item' }}</div>
                                                    <div class="movement-meta">
                                                        {{ $movement->created_at->format('Y-m-d H:i') }} ·
                                                        @if($movement->type === 'in')
                                                            <span class="movement-plus">+{{ $movement->quantity }}</span>
                                                        @elseif($movement->type === 'out')
                                                            <span class="movement-minus">-{{ $movement->quantity }}</span>
                                                        @else
                                                            <span class="movement-neutral">{{ $movement->stock_before }} → {{ $movement->stock_after }}</span>
                                                        @endif

                                                        @if($movement->type !== 'adjustment')
                                                            · {{ $movement->stock_before }} → {{ $movement->stock_after }}
                                                        @endif

                                                        @if($movement->reference)
                                                            · {{ $movement->reference }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="status-badge status-{{ $movement->type === 'out' ? 'empty' : ($movement->type === 'in' ? 'safe' : 'low') }}">
                                                    {{ $movement->type_label }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">Belum ada stock movement.</div>
                            @endif
                        </section>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
