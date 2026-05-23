<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporting - Khayra Physio</title>
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
        .btn { min-height:42px; border:0; cursor:pointer; padding:0 16px; border-radius:14px; font-size:13px; font-weight:900; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; font-family:Arial,sans-serif; white-space:nowrap; }
        .btn-soft { color:#2f7c7a; background:#fff; border:1px solid #e6ebea; }
        .hero { background:#fff; border:1px solid #ecefef; border-radius:30px; padding:28px; box-shadow:0 14px 34px rgba(15,23,42,.05); margin-bottom:18px; }
        .hero-grid { display:grid; grid-template-columns:1.08fr .92fr; gap:18px; align-items:stretch; }
        .hero-main { background:linear-gradient(135deg,#fff 0%,#f7fbfa 58%,#eef7f5 100%); border:1px solid #dfeae8; border-radius:26px; padding:26px; }
        .hero-side { background:linear-gradient(145deg,#467f83 0%,#346d73 52%,#244f55 100%); color:#fff; border-radius:26px; padding:26px; position:relative; overflow:hidden; }
        .hero-side::before { content:""; position:absolute; inset:0; background:radial-gradient(circle at top right, rgba(255,255,255,.13), transparent 28%); pointer-events:none; }
        .hero-side > * { position:relative; z-index:1; }
        .hero-side h2 { margin:0 0 10px; font-size:28px; color:#fff; }
        .hero-side p { margin:0; font-size:13px; line-height:1.9; color:rgba(255,255,255,.92); }
        .card-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:18px; }
        .report-card { text-decoration:none; color:inherit; background:#fff; border:1px solid #ecefef; border-radius:28px; padding:24px; box-shadow:0 12px 28px rgba(15,23,42,.045); display:flex; flex-direction:column; justify-content:space-between; min-height:230px; transition:transform .15s ease, box-shadow .15s ease; }
        .report-card:hover { transform:translateY(-2px); box-shadow:0 18px 34px rgba(15,23,42,.075); }
        .report-top { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:18px; }
        .report-badge { display:inline-flex; padding:7px 11px; border-radius:999px; background:#eef7f5; color:#2f7c7a; font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.04em; }
        .priority { display:inline-flex; padding:7px 11px; border-radius:999px; background:#f8fafc; color:#475569; font-size:11px; font-weight:900; text-transform:uppercase; }
        .priority.high { background:#dcfce7; color:#166534; }
        .priority.medium { background:#fef3c7; color:#92400e; }
        .priority.premium { background:#eef2ff; color:#3457d5; }
        .report-title { margin:0 0 10px; font-size:27px; line-height:1.15; color:#22343a; font-weight:900; }
        .report-subtitle { margin:0; color:#6b7280; font-size:13px; line-height:1.85; }
        .report-link { margin-top:22px; color:#2f7c7a; font-size:13px; font-weight:900; }
        @media (max-width:980px) {
            .layout { display:block; }
            .main { padding:16px; }
            .hero-grid, .card-grid { grid-template-columns:1fr; }
            .title { font-size:32px; }
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
    @include('partials.admin-sidebar', ['activeMenu' => 'reports'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Reporting</span>
                    <h1 class="title">Owner report untuk keputusan bisnis klinik.</h1>
                    <p class="subtitle">
                        Kumpulan laporan premium untuk melihat kondisi operasional, revenue, stok, promo, invoice void, dan performa therapist dalam satu area.
                    </p>
                </div>
                <a href="/admin/dashboard" class="btn btn-soft">← Dashboard</a>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div class="hero-main">
                        <span class="badge">Report Center</span>
                        <h2 class="report-title">Pilih report sesuai kebutuhan owner / admin.</h2>
                        <p class="report-subtitle">
                            Monthly Clinic cocok untuk snapshot bulanan. Revenue untuk finance. Inventory untuk kontrol stok.
                            Therapist Performance untuk evaluasi service dan kelengkapan rekam medis.
                        </p>
                    </div>
                    <aside class="hero-side">
                        <h2>Closing bulanan lebih gampang</h2>
                        <p>
                            Semua report sudah membaca data asli dari booking, visit, billing, promo, inventory usage, stock movement, dan void invoice.
                            Masing-masing report juga punya export CSV.
                        </p>
                    </aside>
                </div>
            </section>

            <section class="card-grid">
                @foreach($cards as $card)
                    @php
                        $priorityClass = strtolower($card['priority']);
                    @endphp
                    <a href="{{ $card['url'] }}" class="report-card">
                        <div>
                            <div class="report-top">
                                <span class="report-badge">{{ $card['badge'] }}</span>
                                <span class="priority {{ $priorityClass }}">{{ $card['priority'] }}</span>
                            </div>
                            <h2 class="report-title">{{ $card['title'] }}</h2>
                            <p class="report-subtitle">{{ $card['subtitle'] }}</p>
                        </div>
                        <div class="report-link">Open report →</div>
                    </a>
                @endforeach
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
