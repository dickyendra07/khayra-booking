<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo Setting - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1320px; margin: 0 auto; }

        .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
        .badge { display: inline-flex; padding: 8px 13px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px; }
        .title { margin: 0; font-size: 42px; line-height: 1.05; color: #22343a; font-weight: 900; }
        .subtitle { margin: 12px 0 0; max-width: 850px; color: #6b7280; font-size: 14px; line-height: 1.9; }

        .actions { display:flex; gap:10px; flex-wrap:wrap; }
        .btn { min-height: 42px; border: 0; cursor: pointer; padding: 0 16px; border-radius: 14px; font-size: 13px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-family: Arial, sans-serif; white-space: nowrap; }
        .btn-primary { background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: #ffffff; box-shadow: 0 12px 24px rgba(47,124,122,.16); }
        .btn-soft { color: #2f7c7a; background: #ffffff; border: 1px solid #e6ebea; }
        .btn-danger { background: #fff1f2; color: #be123c; border: 1px solid #ffe0e6; }

        .hero { background: #ffffff; border: 1px solid #ecefef; border-radius: 28px; padding: 28px; box-shadow: 0 14px 34px rgba(15,23,42,.05); margin-bottom: 18px; }
        .hero-grid { display:grid; grid-template-columns: 1.05fr .95fr; gap:18px; align-items:stretch; }
        .hero-main { background: linear-gradient(135deg, #ffffff 0%, #f7fbfa 58%, #eef7f5 100%); border:1px solid #dfeae8; border-radius:24px; padding:24px; }
        .hero-side { background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%); color:#fff; border-radius:24px; padding:24px; }
        .hero-side h3 { margin:0 0 10px; font-size:26px; }
        .hero-side p { margin:0; font-size:13px; line-height:1.85; color:rgba(255,255,255,.92); }

        .stats-grid { display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; margin-bottom:18px; }
        .stat-card { background:#fff; border:1px solid #ecefef; border-radius:22px; padding:20px; box-shadow:0 10px 26px rgba(15,23,42,.04); }
        .stat-label { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:#7b8794; font-weight:900; margin-bottom:10px; }
        .stat-value { font-size:34px; font-weight:900; line-height:1; color:#22343a; }
        .stat-sub { margin-top:8px; font-size:12px; line-height:1.75; color:#94a3b8; }
        .green .stat-value { color:#166534; }
        .blue .stat-value { color:#1d4ed8; }
        .orange .stat-value { color:#b45309; }
        .red .stat-value { color:#b91c1c; }

        .section-card { background: #ffffff; border: 1px solid #ecefef; border-radius: 28px; padding: 24px; box-shadow: 0 14px 34px rgba(15,23,42,.05); }
        .section-title { margin:0 0 8px; font-size:28px; font-weight:900; color:#22343a; }
        .section-subtitle { margin:0 0 18px; color:#6b7280; font-size:13px; line-height:1.85; }

        .alert { padding: 14px 16px; border-radius: 16px; margin-bottom: 18px; font-size: 14px; line-height: 1.7; font-weight: 700; }
        .alert-success { background: #ecfdf5; color: #166534; border: 1px solid #bbf7d0; }

        .table-wrap { overflow-x: auto; border: 1px solid #edf1f0; border-radius: 20px; }
        table { width: 100%; min-width: 1120px; border-collapse: collapse; }
        th { text-align: left; padding: 15px 16px; background: #f7faf9; color: #486168; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid #edf1f0; }
        td { padding: 16px; border-bottom: 1px solid #f2f5f5; vertical-align: top; font-size: 13px; color: #334155; }
        tr:last-child td { border-bottom: 0; }
        tbody tr:hover td { background:#fbfdfc; }

        .primary-text { font-weight: 900; color: #22343a; line-height: 1.45; }
        .secondary-text { margin-top: 4px; font-size: 11px; color: #94a3b8; line-height: 1.55; }
        .money { font-weight:900; color:#22343a; white-space:nowrap; }
        .pill { display: inline-flex; padding: 7px 11px; border-radius: 999px; font-size: 11px; font-weight: 900; text-transform: uppercase; white-space:nowrap; }
        .active { background: #dcfce7; color: #166534; }
        .inactive { background: #fee2e2; color: #b91c1c; }
        .upcoming { background: #dbeafe; color: #1d4ed8; }
        .expired { background: #f1f5f9; color: #64748b; }

        .discount-box { display:inline-grid; gap:4px; padding:12px 14px; border-radius:16px; background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; min-width:120px; }
        .discount-main { font-size:18px; font-weight:900; }
        .discount-sub { font-size:11px; font-weight:800; text-transform:uppercase; }

        .action-stack { display: flex; gap: 8px; flex-wrap: wrap; }
        form { margin: 0; }

        @media (max-width: 1000px) {
            .layout { display:block; }
            .main { padding:16px; }
            .title { font-size:32px; }
            .hero-grid, .stats-grid { grid-template-columns:1fr; }
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
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'promos'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Promo Management Advanced</span>
                    <h1 class="title">Promo & Discount Control</h1>
                    <p class="subtitle">Kelola kode promo dengan periode aktif, minimum transaksi, maksimum diskon, status, dan validasi otomatis saat Kasir Checkout.</p>
                </div>

                <div class="actions">
                    <a href="/admin/cashier" class="btn btn-soft">Kasir Checkout</a>
                    <a href="/admin/promos/create" class="btn btn-primary">+ Tambah Promo</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <section class="hero">
                <div class="hero-grid">
                    <div class="hero-main">
                        <span class="badge">Promo Engine</span>
                        <h2 class="section-title">Promo siap dipakai kasir tanpa hitung manual.</h2>
                        <p class="section-subtitle">
                            Promo bisa berupa nominal atau persentase. Sistem otomatis cek status aktif, tanggal berlaku, minimum transaksi, dan limit maksimum diskon sebelum invoice dibuat.
                        </p>
                    </div>
                    <aside class="hero-side">
                        <h3>Rule checkout</h3>
                        <p>
                            Promo yang tidak aktif, belum mulai, sudah expired, atau subtotal belum memenuhi minimum akan ditolak otomatis di checkout. Invoice menyimpan promo code, discount type, discount value, dan discount amount.
                        </p>
                    </aside>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card green">
                    <div class="stat-label">Active Now</div>
                    <div class="stat-value">{{ $activeNowPromos }}</div>
                    <div class="stat-sub">Promo yang bisa dipakai hari ini.</div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-label">Upcoming</div>
                    <div class="stat-value">{{ $upcomingPromos }}</div>
                    <div class="stat-sub">Promo aktif tapi belum mulai.</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-label">Expired</div>
                    <div class="stat-value">{{ $expiredPromos }}</div>
                    <div class="stat-sub">Promo lewat periode.</div>
                </div>
                <div class="stat-card red">
                    <div class="stat-label">Inactive</div>
                    <div class="stat-value">{{ $inactivePromos }}</div>
                    <div class="stat-sub">Promo dinonaktifkan manual.</div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Promo List</h2>
                <p class="section-subtitle">Daftar promo dan rule diskon yang akan dibaca oleh Kasir Checkout.</p>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Promo</th>
                                <th>Diskon</th>
                                <th>Minimum</th>
                                <th>Maks Diskon</th>
                                <th>Periode</th>
                                <th>Availability</th>
                                <th>Status Setting</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($promos as $promo)
                                <tr>
                                    <td>
                                        <div class="primary-text">{{ $promo->code }}</div>
                                        <div class="secondary-text">{{ $promo->name }}</div>
                                        @if($promo->notes)
                                            <div class="secondary-text">{{ $promo->notes }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="discount-box">
                                            <div class="discount-main">{{ $promo->discount_label }}</div>
                                            <div class="discount-sub">{{ $promo->discount_type }}</div>
                                        </div>
                                    </td>
                                    <td class="money">Rp {{ number_format($promo->minimum_purchase, 0, ',', '.') }}</td>
                                    <td class="money">{{ $promo->maximum_discount > 0 ? 'Rp ' . number_format($promo->maximum_discount, 0, ',', '.') : '-' }}</td>
                                    <td>
                                        <div class="primary-text">
                                            {{ $promo->start_date ? $promo->start_date->format('Y-m-d') : 'No start' }}
                                        </div>
                                        <div class="secondary-text">
                                            sampai {{ $promo->end_date ? $promo->end_date->format('Y-m-d') : 'No end' }}
                                        </div>
                                    </td>
                                    <td><span class="pill {{ $promo->availability_class }}">{{ $promo->availability_label }}</span></td>
                                    <td><span class="pill {{ $promo->status }}">{{ $promo->status }}</span></td>
                                    <td>
                                        <div class="action-stack">
                                            <a href="/admin/promos/{{ $promo->id }}/edit" class="btn btn-soft">Edit</a>
                                            <form method="POST" action="/admin/promos/{{ $promo->id }}/delete" onsubmit="return confirm('Hapus promo ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">Belum ada promo. Klik Tambah Promo untuk membuat kode promo pertama.</td>
                                </tr>
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
