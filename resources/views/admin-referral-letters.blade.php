<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Rujukan PDF - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1280px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
        .brand-kicker { font-size: 12px; font-weight: 800; letter-spacing: .5px; text-transform: uppercase; color: #7b8794; margin-bottom: 4px; }
        .brand-title { font-size: 18px; font-weight: 800; color: #22343a; }
        .primary-link, .ghost-link { display: inline-flex; align-items: center; text-decoration: none; padding: 11px 14px; border-radius: 12px; font-size: 13px; font-weight: 800; }
        .primary-link { background: #3f7f7e; border: 1px solid #3f7f7e; color: #ffffff; }
        .ghost-link { background: #ffffff; border: 1px solid #e6ebea; color: #2c5b5a; }
        .hero, .section-card { background: #ffffff; border: 1px solid #ecefef; border-radius: 24px; padding: 24px; box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04); margin-bottom: 18px; }
        .hero-title { margin: 0; font-size: 38px; font-weight: 800; color: #22343a; line-height: 1.08; }
        .hero-text { margin: 12px 0 0; font-size: 14px; line-height: 1.9; color: #6b7280; max-width: 840px; }
        .tag-row { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
        .tag { display: inline-block; padding: 9px 13px; border-radius: 999px; background: #f7faf9; border: 1px solid #e7eceb; color: #486168; font-size: 12px; font-weight: 700; }
        .section-title { margin: 0; font-size: 24px; color: #22343a; font-weight: 800; }
        .section-subtitle { margin: 8px 0 18px; font-size: 13px; line-height: 1.8; color: #6b7280; }
        .table-wrap { overflow-x: auto; border: 1px solid #edf1f0; border-radius: 20px; background: #ffffff; }
        table { width: 100%; border-collapse: collapse; min-width: 980px; }
        th { text-align: left; padding: 15px 16px; background: #f7faf9; color: #486168; font-size: 12px; font-weight: 800; border-bottom: 1px solid #edf1f0; }
        td { padding: 16px; font-size: 13px; color: #334155; border-bottom: 1px solid #f2f5f5; vertical-align: top; }
        tr:last-child td { border-bottom: none; }
        .primary-text { font-weight: 800; color: #22343a; line-height: 1.5; }
        .secondary-text { margin-top: 4px; font-size: 11px; line-height: 1.6; color: #94a3b8; }
        .action-stack { display: flex; flex-wrap: wrap; gap: 8px; }
        .action-link { display: inline-flex; align-items: center; justify-content: center; text-decoration: none; padding: 9px 12px; border-radius: 12px; font-size: 12px; font-weight: 800; border: 1px solid transparent; }
        .btn-detail { background: #eef7f5; color: #2f7c7a; border-color: #d8ebe7; }
        .btn-print { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
        .empty { padding: 24px; border-radius: 18px; border: 1px dashed #d9e2e1; color: #7b8794; background: #fbfcfc; line-height: 1.8; }
        @media (max-width: 900px) { .layout { display: block; } .main { padding: 16px; } }
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
    @include('partials.admin-sidebar', ['activeMenu' => 'documents'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <div class="brand-kicker">Add-on Module</div>
                    <div class="brand-title">Surat Rujukan PDF</div>
                </div>

                <a href="/admin/referral-letters/create" class="primary-link">+ Buat Surat Rujukan</a>
            </div>

            <section class="hero">
                <h1 class="hero-title">Kelola surat rujukan pasien yang bisa langsung dicetak atau disimpan sebagai PDF.</h1>
                <p class="hero-text">
                    Modul ini menjawab request client untuk surat menyurat / referan pasien. Surat dapat dikaitkan dengan biodata pasien dan visit,
                    lalu dicetak atau disimpan sebagai PDF dari browser.
                </p>

                <div class="tag-row">
                    <span class="tag">Referral Letter</span>
                    <span class="tag">Patient Document</span>
                    <span class="tag">Printable PDF</span>
                    <span class="tag">Add-on Package</span>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Daftar Surat Rujukan</h2>
                <p class="section-subtitle">Semua surat rujukan pasien yang sudah dibuat.</p>

                @if($letters->count())
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>No Surat</th>
                                    <th>Pasien</th>
                                    <th>Tanggal</th>
                                    <th>Tujuan Rujukan</th>
                                    <th>Visit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($letters as $letter)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ $letter->letter_number }}</div>
                                            <div class="secondary-text">Referral ID #{{ $letter->id }}</div>
                                        </td>
                                        <td>
                                            <div class="primary-text">{{ optional($letter->patient)->full_name ?: '-' }}</div>
                                            <div class="secondary-text">{{ optional($letter->patient)->medical_record_number ?: 'MR not available' }}</div>
                                        </td>
                                        <td>{{ optional($letter->letter_date)->format('Y-m-d') ?: '-' }}</td>
                                        <td>{{ $letter->referral_to ?: '-' }}</td>
                                        <td>
                                            @if($letter->visit)
                                                <div class="primary-text">Visit #{{ $letter->visit->id }}</div>
                                                <div class="secondary-text">{{ $letter->visit->visit_date ?: '-' }}</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-stack">
                                                <a href="/admin/referral-letters/{{ $letter->id }}" class="action-link btn-detail">Detail</a>
                                                <a href="/admin/referral-letters/{{ $letter->id }}/print" class="action-link btn-print" target="_blank">Print PDF</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty">
                        Belum ada surat rujukan. Klik tombol <strong>Buat Surat Rujukan</strong> untuk membuat dokumen pertama.
                    </div>
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
