<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Movements - Khayra Physio</title>
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
        .actions { display:flex; gap:10px; flex-wrap:wrap; }
        .btn { min-height: 42px; border: 0; cursor: pointer; padding: 0 16px; border-radius: 14px; font-size: 13px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-primary { background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: #fff; }
        .btn-soft { color: #2f7c7a; background: #ffffff; border: 1px solid #e6ebea; }

        .hero { background:#fff; border:1px solid #ecefef; border-radius:28px; padding:24px; box-shadow:0 14px 34px rgba(15,23,42,.05); margin-bottom:18px; }
        .hero-grid { display:grid; grid-template-columns:1.1fr .9fr; gap:18px; align-items:stretch; }
        .hero-main { background:linear-gradient(135deg,#fff 0%,#f7fbfa 58%,#eef7f5 100%); border:1px solid #dfeae8; border-radius:24px; padding:24px; }
        .hero-side { background:linear-gradient(145deg,#467f83 0%,#346d73 52%,#244f55 100%); color:#fff; border-radius:24px; padding:24px; }
        .hero-side h3 { margin:0 0 10px; font-size:25px; }
        .hero-side p { margin:0; font-size:13px; line-height:1.85; color:rgba(255,255,255,.92); }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 18px; }
        .stat-card { border-radius: 22px; padding: 20px; border: 1px solid #e8eeee; background: #ffffff; box-shadow: 0 10px 24px rgba(15,23,42,.04); }
        .stat-card.green { background: linear-gradient(180deg, #f3fbf7 0%, #ffffff 100%); border-color: #d8f0de; }
        .stat-card.red { background: linear-gradient(180deg, #fff5f5 0%, #ffffff 100%); border-color: #fecaca; }
        .stat-card.blue { background: linear-gradient(180deg, #f5f9ff 0%, #ffffff 100%); border-color: #dbe7ff; }
        .stat-card.orange { background: linear-gradient(180deg, #fff8ed 0%, #ffffff 100%); border-color: #fed7aa; }
        .stat-label { font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; margin-bottom: 14px; }
        .stat-value { font-size: 34px; font-weight: 900; line-height: 1; margin-bottom: 10px; color: #22343a; }
        .green .stat-value { color: #166534; } .red .stat-value { color: #b91c1c; } .blue .stat-value { color: #1d4ed8; } .orange .stat-value { color:#b45309; }
        .stat-desc { font-size: 12px; color: #6b7280; line-height: 1.7; }

        .section-card { background: #ffffff; border: 1px solid #ecefef; border-radius: 26px; padding: 24px; box-shadow: 0 10px 26px rgba(15,23,42,.04); margin-bottom: 18px; }
        .section-title { margin: 0 0 8px; font-size: 24px; font-weight: 900; color: #22343a; }
        .section-subtitle { margin: 0 0 18px; color: #6b7280; font-size: 13px; line-height: 1.8; }

        .filter-form { display: grid; grid-template-columns: 1fr .9fr .8fr .8fr .8fr auto; gap: 12px; align-items: end; margin-bottom: 20px; }
        .field label { display: block; font-size: 11px; font-weight: 900; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 8px; }
        .field input, .field select { width: 100%; min-height: 46px; border: 1px solid #d7dedd; border-radius: 14px; background: #fff; padding: 0 14px; font-size: 13px; outline: none; }

        .table-wrap { overflow-x: auto; border: 1px solid #edf1f0; border-radius: 20px; background: #fff; }
        table { width: 100%; min-width: 1280px; border-collapse: collapse; }
        th, td { text-align: left; padding: 15px 14px; border-bottom: 1px solid #edf1f0; vertical-align: top; font-size: 13px; }
        th { background: #f7faf9; color: #486168; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover td { background:#fbfdfc; }
        .primary-text { font-weight: 900; color: #22343a; line-height: 1.45; }
        .secondary-text { margin-top: 4px; font-size: 11px; color: #94a3b8; line-height: 1.55; }
        .movement-plus { color: #166534; font-weight: 900; }
        .movement-minus { color: #b91c1c; font-weight: 900; }
        .movement-neutral { color: #1d4ed8; font-weight: 900; }
        .status-badge { display: inline-flex; align-items: center; padding: 8px 12px; border-radius: 999px; font-size: 11px; font-weight: 900; white-space: nowrap; }
        .type-in { background: #eefaf1; color: #166534; }
        .type-out { background: #fff1f1; color: #b91c1c; }
        .type-adjustment { background: #eff6ff; color: #1d4ed8; }
        .audit-pill { display:inline-flex; padding:7px 10px; border-radius:999px; font-size:11px; font-weight:900; background:#f8fafc; color:#475569; border:1px solid #e2e8f0; white-space:nowrap; }
        .audit-void { background:#fff1f2; color:#be123c; border-color:#ffe0e6; }
        .audit-invoice { background:#eef7f5; color:#2f7c7a; border-color:#d8ebe7; }
        .mini-link { display:inline-flex; text-decoration:none; padding:8px 11px; border-radius:11px; font-size:11px; font-weight:900; background:#eef7f5; color:#2f7c7a; border:1px solid #d8ebe7; margin-top:6px; }
        .empty-state { padding: 18px; background: #fff7ed; color: #9a3412; border-radius: 16px; font-weight: 800; border: 1px solid #fed7aa; line-height: 1.7; }

        @media (max-width: 1180px) {
            .stats-grid, .hero-grid { grid-template-columns: 1fr; }
            .filter-form { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 760px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .page-title { font-size: 32px; }
            .filter-form { grid-template-columns: 1fr; }
            .btn { width: 100%; }
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
                    <span class="kicker">Inventory Audit Log</span>
                    <h1 class="page-title">Stock Movements</h1>
                    <p class="page-subtitle">
                        Riwayat stok masuk, stok keluar, adjustment, checkout invoice, dan void return untuk audit stok klinik.
                    </p>
                </div>

                <div class="actions">
                    <a href="/admin/inventory" class="btn btn-soft">← Inventory Control</a>
                    <a href="/admin/inventory/product-usage" class="btn btn-primary">Product Usage</a>
                </div>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div class="hero-main">
                        <span class="kicker">Audit Trail</span>
                        <h2 class="section-title">Setiap perubahan stok punya jejak.</h2>
                        <p class="section-subtitle">
                            Stok keluar dari checkout, stok masuk dari pembelian/import, adjustment dari opname, dan stok kembali dari void invoice bisa dibaca dari satu halaman.
                        </p>
                    </div>
                    <aside class="hero-side">
                        <h3>Void stock rule</h3>
                        <p>
                            Jika invoice di-void, item inventory akan dikembalikan sebagai movement masuk dengan referensi VOID. Product Usage tidak menghitung invoice yang sudah void.
                        </p>
                    </aside>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card green">
                    <div class="stat-label">Total Stock In</div>
                    <div class="stat-value">{{ $totalIn }}</div>
                    <div class="stat-desc">Akumulasi qty movement masuk dari hasil filter.</div>
                </div>

                <div class="stat-card red">
                    <div class="stat-label">Total Stock Out</div>
                    <div class="stat-value">{{ $totalOut }}</div>
                    <div class="stat-desc">Akumulasi qty movement keluar dari hasil filter.</div>
                </div>

                <div class="stat-card blue">
                    <div class="stat-label">Adjustments</div>
                    <div class="stat-value">{{ $totalAdjustment }}</div>
                    <div class="stat-desc">Jumlah koreksi stok dari hasil filter.</div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-label">Total Movement</div>
                    <div class="stat-value">{{ $movements->count() }}</div>
                    <div class="stat-desc">Jumlah baris movement sesuai filter.</div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Movement History</h2>
                <p class="section-subtitle">Gunakan filter untuk melihat movement berdasarkan barang, tipe movement, tanggal, atau catatan.</p>

                <form method="GET" action="/admin/inventory/stock-movements" class="filter-form">
                    <div class="field">
                        <label>Search</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Reference, notes, item, SKU...">
                    </div>

                    <div class="field">
                        <label>Item</label>
                        <select name="item_id">
                            <option value="">Semua Barang</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ (string)$itemId === (string)$item->id ? 'selected' : '' }}>
                                    {{ $item->name }} - {{ $item->sku }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>Type</label>
                        <select name="type">
                            <option value="">Semua</option>
                            <option value="in" {{ $type === 'in' ? 'selected' : '' }}>Stok Masuk</option>
                            <option value="out" {{ $type === 'out' ? 'selected' : '' }}>Stok Keluar</option>
                            <option value="adjustment" {{ $type === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
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

                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>

                @if($movements->count())
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Item</th>
                                <th>Type</th>
                                <th>Qty In</th>
                                <th>Qty Out</th>
                                <th>Stock Before</th>
                                <th>Stock After</th>
                                <th>Audit Source</th>
                                <th>Reference</th>
                                <th>Notes</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($movements as $movement)
                                <tr>
                                    <td>
                                        <div class="primary-text">{{ $movement->created_at->format('Y-m-d') }}</div>
                                        <div class="secondary-text">{{ $movement->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="primary-text">{{ optional($movement->item)->name ?: '-' }}</div>
                                        <div class="secondary-text">{{ optional($movement->item)->sku ?: '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="status-badge type-{{ $movement->type }}">{{ $movement->type_label }}</span>
                                    </td>
                                    <td>
                                        @if($movement->type === 'in')
                                            <span class="movement-plus">+{{ $movement->quantity }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($movement->type === 'out')
                                            <span class="movement-minus">-{{ $movement->quantity }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $movement->stock_before }}</td>
                                    <td>
                                        @if($movement->type === 'adjustment')
                                            <span class="movement-neutral">{{ $movement->stock_after }}</span>
                                        @else
                                            {{ $movement->stock_after }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($movement->voided_billing_id)
                                            <span class="audit-pill audit-void">VOID RETURN</span>
                                            <div>
                                                <a href="/admin/billings/{{ $movement->voided_billing_id }}" class="mini-link">Open Voided Invoice</a>
                                            </div>
                                        @elseif($movement->billing_id)
                                            <span class="audit-pill audit-invoice">CHECKOUT</span>
                                            <div>
                                                <a href="/admin/billings/{{ $movement->billing_id }}" class="mini-link">Open Invoice</a>
                                            </div>
                                        @elseif(str_contains(strtoupper((string)$movement->reference), 'CSV'))
                                            <span class="audit-pill">CSV IMPORT</span>
                                        @elseif(str_contains(strtoupper((string)$movement->reference), 'OPNAME'))
                                            <span class="audit-pill">STOCK OPNAME</span>
                                        @else
                                            <span class="audit-pill">MANUAL</span>
                                        @endif
                                    </td>
                                    <td>{{ $movement->reference ?: '-' }}</td>
                                    <td>{{ $movement->notes ?: '-' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">Belum ada stock movement yang cocok dengan filter.</div>
                @endif
            </section>
        </div>
    </main>
</div>
</body>
</html>
