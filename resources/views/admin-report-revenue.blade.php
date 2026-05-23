<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Report - Khayra Physio</title>
    
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
@php $activeReport = 'revenue'; @endphp
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'reports'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Reporting Premium</span>
                    <h1 class="title">Revenue Report</h1>
                    <p class="subtitle">Analisis pendapatan berdasarkan tanggal, status pembayaran, metode bayar, pasien, promo, outstanding, dan void.</p>
                </div>
                <div class="actions">
                    <a href="/admin/reports/revenue?date_from={{ $dateFrom }}&date_to={{ $dateTo }}&payment_method={{ $paymentMethod }}&status={{ $status }}&search={{ urlencode($search) }}&export=csv" class="btn btn-primary">Export CSV</a>
                    <a href="/admin/billings" class="btn btn-soft">Billing List</a>
                </div>
            </div>

            
<div class="nav-grid">
    <a class="nav-card {{ ($activeReport ?? '') === 'monthly' ? 'active' : '' }}" href="/admin/reports/monthly-clinic">Monthly Clinic</a>
    <a class="nav-card {{ ($activeReport ?? '') === 'revenue' ? 'active' : '' }}" href="/admin/reports/revenue">Revenue</a>
    <a class="nav-card {{ ($activeReport ?? '') === 'inventory' ? 'active' : '' }}" href="/admin/reports/inventory">Inventory</a>
    <a class="nav-card {{ ($activeReport ?? '') === 'therapist' ? 'active' : '' }}" href="/admin/reports/therapist-performance">Therapist Performance</a>
</div>


            <section class="filter-card">
                <form method="GET" action="/admin/reports/revenue" class="filter-form">
                    <div class="field"><label>Date From</label><input type="date" name="date_from" value="{{ $dateFrom }}"></div>
                    <div class="field"><label>Date To</label><input type="date" name="date_to" value="{{ $dateTo }}"></div>
                    <div class="field">
                        <label>Payment Method</label>
                        <select name="payment_method">
                            <option value="">Semua</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method }}" {{ $paymentMethod === $method ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $method)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select name="status">
                            <option value="">Semua</option>
                            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="void" {{ $status === 'void' ? 'selected' : '' }}>Void</option>
                        </select>
                    </div>
                    <div class="field"><label>Search</label><input type="text" name="search" value="{{ $search }}" placeholder="Invoice, patient, MR, promo..."></div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </section>

            <section class="stats-grid five">
                <div class="stat-card blue"><div class="stat-label">Transactions</div><div class="stat-value">{{ $summary['transactions'] }}</div><div class="stat-sub">Total invoice sesuai filter.</div></div>
                <div class="stat-card green"><div class="stat-label">Paid Amount</div><div class="stat-value">Rp {{ number_format($summary['paid_amount'], 0, ',', '.') }}</div><div class="stat-sub">Pembayaran diterima.</div></div>
                <div class="stat-card orange"><div class="stat-label">Outstanding</div><div class="stat-value">Rp {{ number_format($summary['outstanding'], 0, ',', '.') }}</div><div class="stat-sub">Sisa tagihan.</div></div>
                <div class="stat-card red"><div class="stat-label">Discount</div><div class="stat-value">Rp {{ number_format($summary['discount'], 0, ',', '.') }}</div><div class="stat-sub">Promo / diskon.</div></div>
                <div class="stat-card violet"><div class="stat-label">Net Revenue</div><div class="stat-value">Rp {{ number_format($summary['net_revenue'], 0, ',', '.') }}</div><div class="stat-sub">Exclude void.</div></div>
            </section>

            <section class="stats-grid">
                <div class="stat-card green"><div class="stat-label">Paid Invoice</div><div class="stat-value">{{ $summary['paid_count'] }}</div></div>
                <div class="stat-card orange"><div class="stat-label">Partial Invoice</div><div class="stat-value">{{ $summary['partial_count'] }}</div></div>
                <div class="stat-card red"><div class="stat-label">Unpaid Invoice</div><div class="stat-value">{{ $summary['unpaid_count'] }}</div></div>
                <div class="stat-card red"><div class="stat-label">Void Invoice</div><div class="stat-value">{{ $summary['void_count'] }}</div></div>
            </section>

            <div class="two-grid">
                <section class="section-card">
                    <h2 class="section-title">Payment Method Summary</h2>
                    <p class="section-subtitle">Ringkasan pembayaran berdasarkan metode bayar.</p>
                    @if($byMethod->count())
                        <div class="table-wrap">
                            <table style="min-width:700px;">
                                <thead><tr><th>Method</th><th>Count</th><th>Paid</th><th>Net</th><th>Outstanding</th></tr></thead>
                                <tbody>
                                    @foreach($byMethod as $row)
                                        <tr>
                                            <td><span class="pill pill-blue">{{ ucwords(str_replace('_', ' ', $row['method'])) }}</span></td>
                                            <td>{{ $row['count'] }}</td>
                                            <td class="money">Rp {{ number_format($row['paid'], 0, ',', '.') }}</td>
                                            <td class="money">Rp {{ number_format($row['net'], 0, ',', '.') }}</td>
                                            <td class="money">Rp {{ number_format($row['outstanding'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">Belum ada metode pembayaran pada filter ini.</div>
                    @endif
                </section>

                <section class="section-card">
                    <h2 class="section-title">Revenue Notes</h2>
                    <p class="section-subtitle">Angka revenue exclude invoice yang sudah void. Paid amount adalah uang yang diterima, outstanding adalah sisa tagihan partial/unpaid.</p>
                    <div class="empty-state">
                        Gunakan report ini untuk cek transaksi kasir, piutang, performa promo, dan validasi invoice void sebelum closing bulanan.
                    </div>
                </section>
            </div>

            <section class="section-card">
                <h2 class="section-title">Invoice Detail</h2>
                <p class="section-subtitle">Daftar invoice sesuai filter.</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th><th>Invoice</th><th>Patient</th><th>Status</th><th>Method</th><th>Subtotal</th><th>Discount</th><th>Total</th><th>Paid</th><th>Remaining</th><th>Promo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($billings as $billing)
                                <tr>
                                    <td>{{ $billing->invoice_date ? $billing->invoice_date->format('Y-m-d') : '-' }}</td>
                                    <td><a href="/admin/billings/{{ $billing->id }}" class="primary-text">{{ $billing->invoice_number ?: 'Invoice #' . $billing->id }}</a></td>
                                    <td><div class="primary-text">{{ optional($billing->patient)->full_name ?: '-' }}</div><div class="secondary-text">{{ optional($billing->patient)->medical_record_number ?: '-' }}</div></td>
                                    <td><span class="pill {{ $billing->payment_status === 'paid' ? 'pill-green' : ($billing->payment_status === 'void' ? 'pill-gray' : 'pill-orange') }}">{{ $billing->payment_status }}</span></td>
                                    <td>{{ $billing->payment_method_label }}</td>
                                    <td class="money">Rp {{ number_format($billing->subtotal_amount ?: $billing->amount, 0, ',', '.') }}</td>
                                    <td class="money">Rp {{ number_format($billing->discount_amount ?: 0, 0, ',', '.') }}</td>
                                    <td class="money">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
                                    <td class="money">Rp {{ number_format($billing->paid_amount ?: 0, 0, ',', '.') }}</td>
                                    <td class="money">Rp {{ number_format($billing->remaining_amount ?: 0, 0, ',', '.') }}</td>
                                    <td>{{ $billing->promo_code ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="11">Belum ada invoice sesuai filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
