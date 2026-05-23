<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Usage by Treatment - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1380px; margin: 0 auto; }

        .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
        .badge { display: inline-flex; padding: 8px 12px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 10px; }
        .title { margin: 0; font-size: 42px; line-height: 1.05; color: #22343a; font-weight: 900; }
        .subtitle { margin: 12px 0 0; max-width: 860px; color: #6b7280; font-size: 14px; line-height: 1.9; }

        .actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { min-height: 42px; border: 0; cursor: pointer; padding: 0 16px; border-radius: 14px; font-size: 13px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-family: Arial, sans-serif; white-space: nowrap; }
        .btn-primary { background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: #ffffff; box-shadow: 0 12px 24px rgba(47,124,122,.16); }
        .btn-soft { color: #2f7c7a; background: #ffffff; border: 1px solid #e6ebea; }
        .btn-dark { background: #17232b; color: #ffffff; }

        .hero { background: #ffffff; border: 1px solid #ecefef; border-radius: 28px; padding: 28px; box-shadow: 0 14px 34px rgba(15,23,42,.05); margin-bottom: 18px; }
        .hero-grid { display: grid; grid-template-columns: 1.1fr .9fr; gap: 18px; align-items: stretch; }
        .hero-card { background: linear-gradient(135deg, #ffffff 0%, #f7fbfa 58%, #eef7f5 100%); border: 1px solid #dfeae8; border-radius: 24px; padding: 24px; }
        .hero-side { background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%); border-radius: 24px; color: #ffffff; padding: 24px; }
        .hero-side h3 { margin: 0 0 10px; font-size: 26px; color: #ffffff; }
        .hero-side p { margin: 0; font-size: 13px; line-height: 1.85; color: rgba(255,255,255,.92); }

        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 18px; }
        .stat-card { background: #ffffff; border: 1px solid #ecefef; border-radius: 22px; padding: 22px; box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04); }
        .stat-label { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: #7b8794; font-weight: 900; margin-bottom: 10px; }
        .stat-value { font-size: 34px; line-height: 1; font-weight: 900; color: #22343a; }
        .stat-sub { margin-top: 8px; font-size: 12px; line-height: 1.75; color: #94a3b8; }
        .green .stat-value { color: #166534; }
        .blue .stat-value { color: #1d4ed8; }
        .orange .stat-value { color: #b45309; }

        .section-card { background: #ffffff; border: 1px solid #ecefef; border-radius: 26px; padding: 24px; box-shadow: 0 10px 26px rgba(15,23,42,.04); margin-bottom: 18px; }
        .section-title { margin: 0; font-size: 28px; font-weight: 900; color: #22343a; }
        .section-subtitle { margin: 8px 0 18px; color: #6b7280; font-size: 13px; line-height: 1.85; }

        .filter-form { display: grid; grid-template-columns: 1fr 220px auto; gap: 12px; align-items: end; margin-bottom: 18px; }
        .field label { display: block; margin-bottom: 8px; font-size: 12px; font-weight: 900; color: #64748b; text-transform: uppercase; letter-spacing: .05em; }
        input { width: 100%; min-height: 46px; border: 1px solid #d7dedd; border-radius: 14px; padding: 0 14px; font-size: 13px; }

        .table-wrap { overflow-x: auto; border: 1px solid #edf1f0; border-radius: 20px; background: #ffffff; }
        table { width: 100%; min-width: 1180px; border-collapse: collapse; }
        th, td { text-align: left; padding: 15px 14px; border-bottom: 1px solid #edf1f0; vertical-align: top; font-size: 13px; }
        th { background: #f7faf9; color: #486168; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover td { background: #fbfdfc; }

        .primary-text { font-weight: 900; color: #22343a; line-height: 1.45; }
        .secondary-text { margin-top: 4px; font-size: 11px; color: #94a3b8; line-height: 1.55; }
        .money { font-weight: 900; color: #22343a; white-space: nowrap; }
        .qty { font-size: 18px; font-weight: 900; color: #1d4ed8; }
        .pill { display: inline-flex; padding: 7px 11px; border-radius: 999px; font-size: 11px; font-weight: 900; text-transform: uppercase; }
        .pill-inventory { background: #fff7ed; color: #c2410c; }
        .mini-link { display: inline-flex; align-items: center; justify-content: center; text-decoration: none; padding: 8px 11px; border-radius: 11px; font-size: 11px; font-weight: 900; background: #eef7f5; color: #2f7c7a; border: 1px solid #d8ebe7; margin-right: 6px; margin-top: 6px; }
        .empty-state { padding: 22px; border-radius: 18px; border: 1px dashed #d9e2e1; background: #fafcfc; color: #7b8794; font-size: 13px; line-height: 1.8; text-align: center; }

        @media (max-width: 980px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-grid, .stats-grid, .filter-form { grid-template-columns: 1fr; }
            .title { font-size: 32px; }
        }
    </style>
    <!-- Khayra PWA -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#2f7c7a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Khayra ERM">
    <meta name="apple-mobile-web-app-title" content="Khayra ERM">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/images/khayra-logo.png">
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'inventory'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Inventory Usage</span>
                    <h1 class="title">Product Usage by Treatment</h1>
                    <p class="subtitle">
                        Report pemakaian produk inventory yang terhubung ke billing, visit, pasien, dan treatment. Cocok untuk audit stok, analisa consumable, dan laporan profit klinik.
                    </p>
                </div>

                <div class="actions">
                    <a href="/admin/inventory" class="btn btn-soft">← Inventory</a>
                    <a href="/admin/inventory/stock-movements" class="btn btn-dark">Stock Movements</a>
                    <a href="/admin/cashier" class="btn btn-primary">Cashier Checkout</a>
                </div>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div class="hero-card">
                        <span class="badge">Usage Report</span>
                        <h2 class="section-title">Produk yang dipakai bisa dilacak sampai invoice dan visit.</h2>
                        <p class="section-subtitle">
                            Setiap produk inventory yang masuk billing otomatis menjadi data usage. Dari sini admin bisa tahu produk apa yang paling sering dipakai, pasien/visit mana yang memakai, dan nilai pemakaian produk dalam periode tertentu.
                        </p>
                    </div>

                    <aside class="hero-side">
                        <h3>Flow data</h3>
                        <p>
                            Kasir Checkout → Billing Item Inventory → Stock Out Movement → Product Usage Report.
                            Jika billing di-void, stok kembali dan invoice void tidak dihitung di report ini.
                        </p>
                    </aside>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-label">Usage Lines</div>
                    <div class="stat-value">{{ $totalUsageLines }}</div>
                    <div class="stat-sub">Jumlah baris produk inventory dalam periode ini.</div>
                </div>

                <div class="stat-card green">
                    <div class="stat-label">Quantity Used</div>
                    <div class="stat-value">{{ $totalQuantityUsed }}</div>
                    <div class="stat-sub">Total qty produk yang digunakan / terjual.</div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-label">Usage Value</div>
                    <div class="stat-value" style="font-size:26px;">Rp {{ number_format($totalUsageValue, 0, ',', '.') }}</div>
                    <div class="stat-sub">Total nilai produk inventory pada invoice.</div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Filter Usage</h2>
                <p class="section-subtitle">Cari berdasarkan produk, SKU, kategori, atau nama pasien.</p>

                <form method="GET" action="/admin/inventory/product-usage" class="filter-form">
                    <div class="field">
                        <label>Search</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari produk, SKU, kategori, pasien...">
                    </div>

                    <div class="field">
                        <label>Month</label>
                        <input type="month" name="month" value="{{ $month }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </section>

            <section class="section-card">
                <h2 class="section-title">Top Product Usage</h2>
                <p class="section-subtitle">Ringkasan produk yang paling banyak digunakan dalam periode {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}.</p>

                @if($usageByProduct->count())
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Usage Lines</th>
                                    <th>Total Qty</th>
                                    <th>Total Value</th>
                                    <th>Inventory</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usageByProduct as $row)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ optional($row['item'])->name ?: $row['description'] }}</div>
                                            <div class="secondary-text">
                                                SKU: {{ optional($row['item'])->sku ?: '-' }} · Category: {{ optional($row['item'])->category ?: '-' }}
                                            </div>
                                        </td>
                                        <td>{{ $row['lines'] }}</td>
                                        <td><span class="qty">{{ $row['quantity'] }}</span></td>
                                        <td class="money">Rp {{ number_format($row['value'], 0, ',', '.') }}</td>
                                        <td>
                                            @if($row['item'])
                                                <a href="/admin/inventory/{{ $row['item']->id }}" class="mini-link">Open Item</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">Belum ada pemakaian produk inventory pada periode ini.</div>
                @endif
            </section>

            <section class="section-card">
                <h2 class="section-title">Usage Detail by Billing / Visit</h2>
                <p class="section-subtitle">Detail pemakaian produk berdasarkan invoice, patient, visit, dan therapist.</p>

                @if($usageItems->count())
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Produk</th>
                                    <th>Patient / Visit</th>
                                    <th>Therapist</th>
                                    <th>Invoice</th>
                                    <th>Qty</th>
                                    <th>Value</th>
                                    <th>Links</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usageItems as $item)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ optional($item->billing?->invoice_date)->format('Y-m-d') ?: '-' }}</div>
                                            <div class="secondary-text">{{ optional($item->billing?->created_at)->format('H:i') }}</div>
                                        </td>
                                        <td>
                                            <div class="primary-text">{{ $item->description }}</div>
                                            <div class="secondary-text">
                                                {{ optional($item->inventoryItem)->sku ?: '-' }} · {{ optional($item->inventoryItem)->category ?: '-' }}
                                            </div>
                                            <span class="pill pill-inventory">inventory</span>
                                        </td>
                                        <td>
                                            <div class="primary-text">{{ optional($item->billing?->patient)->full_name ?: '-' }}</div>
                                            <div class="secondary-text">
                                                {{ $item->billing && $item->billing->visit ? 'Visit #' . $item->billing->visit->id : 'No visit linked' }}
                                                @if($item->billing && $item->billing->visit && $item->billing->visit->visit_date)
                                                    · {{ $item->billing->visit->visit_date }}
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            {{ $item->billing && $item->billing->visit ? (optional($item->billing->visit->therapistRelation)->full_name ?: $item->billing->visit->therapist ?: '-') : '-' }}
                                        </td>
                                        <td>
                                            <div class="primary-text">{{ optional($item->billing)->invoice_number ?: '-' }}</div>
                                            <div class="secondary-text">{{ optional($item->billing)->payment_status ?: '-' }}</div>
                                        </td>
                                        <td><span class="qty">{{ $item->quantity }}</span></td>
                                        <td class="money">Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                                        <td>
                                            @if($item->billing)
                                                <a href="/admin/billings/{{ $item->billing->id }}" class="mini-link">Invoice</a>
                                            @endif
                                            @if($item->inventoryItem)
                                                <a href="/admin/inventory/{{ $item->inventoryItem->id }}" class="mini-link">Item</a>
                                            @endif
                                            @if($item->billing && $item->billing->visit)
                                                <a href="/admin/visits/{{ $item->billing->visit->id }}/medical-record" class="mini-link">Medical Record</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">Belum ada detail pemakaian produk inventory pada periode ini.</div>
                @endif
            </section>
        </div>
    </main>
</div>
<script>
(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }

    var deferredInstallPrompt = null;

    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault();
        deferredInstallPrompt = event;

        if (document.getElementById('khayraInstallAppButton')) {
            return;
        }

        var button = document.createElement('button');
        button.id = 'khayraInstallAppButton';
        button.type = 'button';
        button.innerText = 'Install App';
        button.style.position = 'fixed';
        button.style.right = '18px';
        button.style.bottom = '18px';
        button.style.zIndex = '99999';
        button.style.border = '0';
        button.style.borderRadius = '999px';
        button.style.padding = '12px 16px';
        button.style.background = '#2f7c7a';
        button.style.color = '#ffffff';
        button.style.fontWeight = '900';
        button.style.fontFamily = 'Arial, sans-serif';
        button.style.fontSize = '13px';
        button.style.boxShadow = '0 14px 30px rgba(47,124,122,.24)';
        button.style.cursor = 'pointer';

        button.addEventListener('click', function () {
            if (!deferredInstallPrompt) {
                return;
            }

            deferredInstallPrompt.prompt();
            deferredInstallPrompt.userChoice.finally(function () {
                deferredInstallPrompt = null;
                button.remove();
            });
        });

        document.body.appendChild(button);
    });
})();
</script>

</body>
</html>
