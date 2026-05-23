<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapist Performance Report - Khayra Physio</title>
    
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
@php $activeReport = 'therapist'; @endphp
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'reports'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Reporting Premium</span>
                    <h1 class="title">Therapist Performance Report</h1>
                    <p class="subtitle">Laporan performa therapist: jumlah visit, completed visit, pasien ditangani, rekam medis, completion rate, dan revenue terkait visit.</p>
                </div>
                <div class="actions">
                    <a href="/admin/reports/therapist-performance?month={{ $month }}&export=csv" class="btn btn-primary">Export CSV</a>
                    <a href="/admin/therapists" class="btn btn-soft">Tim Fisioterapis</a>
                </div>
            </div>

            
<div class="nav-grid">
    <a class="nav-card {{ ($activeReport ?? '') === 'monthly' ? 'active' : '' }}" href="/admin/reports/monthly-clinic">Monthly Clinic</a>
    <a class="nav-card {{ ($activeReport ?? '') === 'revenue' ? 'active' : '' }}" href="/admin/reports/revenue">Revenue</a>
    <a class="nav-card {{ ($activeReport ?? '') === 'inventory' ? 'active' : '' }}" href="/admin/reports/inventory">Inventory</a>
    <a class="nav-card {{ ($activeReport ?? '') === 'therapist' ? 'active' : '' }}" href="/admin/reports/therapist-performance">Therapist Performance</a>
</div>


            <section class="filter-card">
                <form method="GET" action="/admin/reports/therapist-performance" class="filter-form">
                    <div class="field">
                        <label>Month</label>
                        <input type="month" name="month" value="{{ $month }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Lihat Report</button>
                </form>
            </section>

            <section class="stats-grid">
                <div class="stat-card blue"><div class="stat-label">Therapists</div><div class="stat-value">{{ $summary['therapists'] }}</div><div class="stat-sub">Total therapist.</div></div>
                <div class="stat-card green"><div class="stat-label">Visits</div><div class="stat-value">{{ $summary['visits'] }}</div><div class="stat-sub">{{ $summary['completed'] }} completed.</div></div>
                <div class="stat-card violet"><div class="stat-label">Patients Handled</div><div class="stat-value">{{ $summary['patients'] }}</div><div class="stat-sub">Unique patient.</div></div>
                <div class="stat-card green"><div class="stat-label">Revenue</div><div class="stat-value">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</div><div class="stat-sub">Invoice terkait visit.</div></div>
            </section>

            <section class="stats-grid">
                <div class="stat-card green"><div class="stat-label">Paid Amount</div><div class="stat-value">Rp {{ number_format($summary['paid'], 0, ',', '.') }}</div><div class="stat-sub">Pembayaran diterima.</div></div>
                <div class="stat-card orange"><div class="stat-label">Outstanding</div><div class="stat-value">Rp {{ number_format($summary['outstanding'], 0, ',', '.') }}</div><div class="stat-sub">Sisa tagihan terkait visit.</div></div>
                <div class="stat-card blue"><div class="stat-label">Medical Records</div><div class="stat-value">{{ $rows->sum('medical_records') }}</div><div class="stat-sub">Rekam medis dibuat.</div></div>
                <div class="stat-card red"><div class="stat-label">Need Attention</div><div class="stat-value">{{ $rows->filter(fn ($row) => $row['visits'] > 0 && $row['medical_record_rate'] < 100)->count() }}</div><div class="stat-sub">MR rate belum 100%.</div></div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Therapist Ranking</h2>
                <p class="section-subtitle">Urutan berdasarkan jumlah visit bulan ini.</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Therapist</th><th>Visits</th><th>Completed</th><th>Patients</th><th>Medical Records</th><th>Completion Rate</th><th>MR Rate</th><th>Revenue</th><th>Paid</th><th>Outstanding</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td><div class="primary-text">{{ $row['therapist']->full_name }}</div><div class="secondary-text">{{ $row['therapist']->specialty ?: '-' }}</div></td>
                                    <td>{{ $row['visits'] }}</td>
                                    <td>{{ $row['completed'] }}</td>
                                    <td>{{ $row['patients'] }}</td>
                                    <td>{{ $row['medical_records'] }}</td>
                                    <td><span class="pill {{ $row['completion_rate'] >= 80 ? 'pill-green' : 'pill-orange' }}">{{ $row['completion_rate'] }}%</span></td>
                                    <td><span class="pill {{ $row['medical_record_rate'] >= 100 ? 'pill-green' : 'pill-orange' }}">{{ $row['medical_record_rate'] }}%</span></td>
                                    <td class="money">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</td>
                                    <td class="money">Rp {{ number_format($row['paid'], 0, ',', '.') }}</td>
                                    <td class="money">Rp {{ number_format($row['outstanding'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Visit Detail</h2>
                <p class="section-subtitle">Detail visit bulan ini untuk audit performa.</p>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Date</th><th>Patient</th><th>Therapist</th><th>Status</th><th>Medical Record</th><th>Visit</th></tr></thead>
                        <tbody>
                            @forelse($visits as $visit)
                                <tr>
                                    <td>{{ $visit->visit_date ?: '-' }}</td>
                                    <td><div class="primary-text">{{ optional($visit->patient)->full_name ?: '-' }}</div><div class="secondary-text">{{ optional($visit->patient)->medical_record_number ?: '-' }}</div></td>
                                    <td>{{ optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: '-' }}</td>
                                    <td><span class="pill {{ $visit->status === 'completed' ? 'pill-green' : 'pill-blue' }}">{{ $visit->status ?: '-' }}</span></td>
                                    <td>
                                        @if($visit->medicalRecord)
                                            <span class="pill pill-green">Available</span>
                                        @else
                                            <span class="pill pill-orange">Missing</span>
                                        @endif
                                    </td>
                                    <td><a href="/admin/visits/{{ $visit->id }}/medical-record" class="btn btn-soft">Open</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6">Belum ada visit bulan ini.</td></tr>
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
