<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Surat Rujukan - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1120px; margin: 0 auto; }
        .top-actions { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 18px; flex-wrap: wrap; }
        .ghost-link, .primary-link { display: inline-flex; align-items: center; text-decoration: none; padding: 11px 14px; border-radius: 12px; font-size: 13px; font-weight: 800; }
        .ghost-link { background: #ffffff; border: 1px solid #e6ebea; color: #2c5b5a; }
        .primary-link { background: #3f7f7e; border: 1px solid #3f7f7e; color: #ffffff; }
        .card { background: #ffffff; border: 1px solid #ecefef; border-radius: 24px; padding: 24px; box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04); margin-bottom: 18px; }
        .badge { display: inline-block; padding: 8px 14px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 800; margin-bottom: 14px; }
        .title { margin: 0; font-size: 38px; font-weight: 800; color: #22343a; line-height: 1.08; }
        .subtitle { margin: 10px 0 0; font-size: 14px; line-height: 1.8; color: #6b7280; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 20px; }
        .info-item { border: 1px solid #edf1f0; border-radius: 18px; padding: 16px; background: #fbfcfc; }
        .info-item.full { grid-column: 1 / -1; }
        .info-key { font-size: 11px; text-transform: uppercase; letter-spacing: .45px; color: #7b8794; font-weight: 800; margin-bottom: 8px; }
        .info-value { font-size: 15px; line-height: 1.75; color: #22343a; font-weight: 700; white-space: pre-line; }
        @media (max-width: 900px) { .layout { display: block; } .main { padding: 16px; } .info-grid { grid-template-columns: 1fr; } }
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
            <div class="top-actions">
                <a href="/admin/referral-letters" class="ghost-link">← Kembali</a>
                <a href="/admin/referral-letters/{{ $letter->id }}/print" class="primary-link" target="_blank">Print / Save PDF</a>
            </div>

            <section class="card">
                <span class="badge">Surat Rujukan</span>
                <h1 class="title">{{ $letter->letter_number }}</h1>
                <p class="subtitle">Detail surat rujukan pasien yang siap dicetak atau disimpan sebagai PDF.</p>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-key">Patient</div>
                        <div class="info-value">{{ optional($letter->patient)->full_name ?: '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Tanggal Surat</div>
                        <div class="info-value">{{ optional($letter->letter_date)->format('Y-m-d') ?: '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Dirujuk Kepada</div>
                        <div class="info-value">{{ $letter->referral_to ?: '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Visit</div>
                        <div class="info-value">{{ $letter->visit ? 'Visit #' . $letter->visit->id . ' - ' . $letter->visit->visit_date : '-' }}</div>
                    </div>
                    <div class="info-item full">
                        <div class="info-key">Alasan Rujukan</div>
                        <div class="info-value">{{ $letter->referral_reason ?: '-' }}</div>
                    </div>
                    <div class="info-item full">
                        <div class="info-key">Ringkasan Klinis</div>
                        <div class="info-value">{{ $letter->clinical_summary ?: '-' }}</div>
                    </div>
                    <div class="info-item full">
                        <div class="info-key">Rekomendasi</div>
                        <div class="info-value">{{ $letter->recommendation ?: '-' }}</div>
                    </div>
                    <div class="info-item full">
                        <div class="info-key">Catatan Tambahan</div>
                        <div class="info-value">{{ $letter->notes ?: '-' }}</div>
                    </div>
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
