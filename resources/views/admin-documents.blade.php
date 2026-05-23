<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat & Dokumen Klinik - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1240px; margin: 0 auto; }

        .hero {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 30px;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(15,23,42,.05);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 20px;
            align-items: stretch;
        }

        .badge {
            display: inline-flex;
            padding: 8px 13px;
            border-radius: 999px;
            background: #eef5f4;
            color: #35565d;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 14px;
        }

        .title {
            margin: 0;
            font-size: 42px;
            line-height: 1.05;
            color: #22343a;
            font-weight: 900;
            letter-spacing: -1.2px;
        }

        .subtitle {
            margin: 14px 0 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.85;
            max-width: 760px;
        }

        .snapshot {
            border-radius: 26px;
            padding: 24px;
            color: #ffffff;
            background:
                linear-gradient(135deg, rgba(255,255,255,.08) 1px, transparent 1px),
                linear-gradient(135deg, #3d8a89 0%, #2f7c7a 52%, #244f55 100%);
            background-size: 28px 28px, auto;
        }

        .snapshot h2 {
            margin: 0 0 10px;
            font-size: 26px;
            line-height: 1.1;
        }

        .snapshot p {
            margin: 0 0 18px;
            color: rgba(255,255,255,.86);
            font-size: 13px;
            line-height: 1.75;
        }

        .snapshot-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .snapshot-card {
            border: 1px solid rgba(255,255,255,.18);
            background: rgba(255,255,255,.09);
            border-radius: 18px;
            padding: 16px;
        }

        .snapshot-label {
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255,255,255,.78);
            font-weight: 900;
            margin-bottom: 8px;
        }

        .snapshot-value {
            font-size: 30px;
            font-weight: 900;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 14px;
            flex-wrap: wrap;
            margin: 22px 0 14px;
        }

        .section-title {
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            color: #22343a;
            letter-spacing: -.5px;
        }

        .section-subtitle {
            margin: 7px 0 0;
            color: #7b8794;
            font-size: 13px;
            line-height: 1.7;
        }

        .doc-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .doc-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 14px 34px rgba(15,23,42,.05);
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 18px;
            text-decoration: none;
            color: inherit;
        }

        .doc-top {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .doc-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: #eef7f5;
            color: #2f7c7a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            flex: 0 0 auto;
            letter-spacing: .02em;
        }

        .doc-card h3 {
            margin: 0;
            font-size: 24px;
            color: #22343a;
            line-height: 1.15;
            letter-spacing: -.4px;
        }

        .doc-card p {
            margin: 10px 0 0;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.75;
        }

        .doc-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
            background: #eef2ff;
            color: #4f46e5;
        }

        .doc-badge.medium { background: #fff7ed; color: #b45309; }
        .doc-badge.premium { background: #eef2ff; color: #4f46e5; }
        .doc-badge.ready { background: #ecfdf5; color: #047857; }

        .doc-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            min-height: 42px;
            border: 0;
            cursor: pointer;
            padding: 0 16px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(47,124,122,.16);
        }

        .btn-soft {
            color: #2f7c7a;
            background: #eef7f5;
            border: 1px solid #d8ebe7;
        }

        @media (max-width: 980px) {
            .hero-grid, .doc-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 900px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .title { font-size: 32px; }
            .doc-card h3 { font-size: 21px; }
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
    @include('partials.admin-sidebar', ['activeMenu' => 'documents'])

    <main class="main">
        <div class="container">
            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <span class="badge">Dokumen Profesional</span>
                        <h1 class="title">Surat & Dokumen Klinik</h1>
                        <p class="subtitle">
                            Semua dokumen klinik dikumpulkan di satu tempat agar tidak tercecer:
                            surat rujukan, surat kontrol, discharge summary, informed consent archive,
                            pembelian paket, dan surat izin / istirahat pasien.
                        </p>
                    </div>

                    <div class="snapshot">
                        <h2>Document Snapshot</h2>
                        <p>Ringkasan jumlah dokumen yang sudah tersimpan di sistem.</p>
                        <div class="snapshot-grid">
                            <div class="snapshot-card">
                                <div class="snapshot-label">Surat Rujukan</div>
                                <div class="snapshot-value">{{ $referralLettersCount ?? 0 }}</div>
                            </div>
                            <div class="snapshot-card">
                                <div class="snapshot-label">Consent Archive</div>
                                <div class="snapshot-value">{{ $consentsCount ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="section-head">
                <div>
                    <h2 class="section-title">Dokumen Utama</h2>
                    <p class="section-subtitle">Pilih dokumen yang ingin dibuat, dibuka, atau dicetak.</p>
                </div>
            </div>

            <section class="doc-grid">
                <div class="doc-card">
                    <div>
                        <div class="doc-top">
                            <div class="doc-icon">REF</div>
                            <div>
                                <span class="doc-badge medium">Medium</span>
                                <h3>Surat Rujukan Profesional</h3>
                                <p>Template rujukan klinik: tujuan, alasan, ringkasan klinis, rekomendasi, dan print PDF.</p>
                            </div>
                        </div>
                    </div>
                    <div class="doc-actions">
                        <a href="/admin/referral-letters" class="btn btn-primary">Buka Surat Rujukan</a>
                        <a href="/admin/referral-letters/create" class="btn btn-soft">+ Buat Baru</a>
                    </div>
                </div>

                <div class="doc-card">
                    <div>
                        <div class="doc-top">
                            <div class="doc-icon">CTRL</div>
                            <div>
                                <span class="doc-badge premium">Premium</span>
                                <h3>Surat Kontrol / Surat Terapi</h3>
                                <p>Generate dokumen kontrol atau keterangan terapi dari data pasien, visit, dan rencana terapi.</p>
                            </div>
                        </div>
                    </div>
                    <div class="doc-actions">
                        <a href="/admin/control-letter/create" class="btn btn-primary">Buat Surat Kontrol</a>
                    </div>
                </div>

                <div class="doc-card">
                    <div>
                        <div class="doc-top">
                            <div class="doc-icon">SUM</div>
                            <div>
                                <span class="doc-badge premium">Premium</span>
                                <h3>Discharge Summary</h3>
                                <p>Ringkasan selesai terapi: kondisi awal, kondisi akhir, program terapi, home exercise, dan rekomendasi lanjutan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="doc-actions">
                        <a href="/admin/discharge-summary/create" class="btn btn-primary">Buat Discharge Summary</a>
                    </div>
                </div>

                <div class="doc-card">
                    <div>
                        <div class="doc-top">
                            <div class="doc-icon">IC</div>
                            <div>
                                <span class="doc-badge medium">Medium</span>
                                <h3>Digital Consent Archive</h3>
                                <p>Arsip informed consent per pasien. Bisa dicari, dibuka ulang, dan dicetak kembali kapan pun dibutuhkan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="doc-actions">
                        <a href="/admin/consent-archive" class="btn btn-primary">Buka Consent Archive</a>
                    </div>
                </div>

                <div class="doc-card">
                    <div>
                        <div class="doc-top">
                            <div class="doc-icon">PKG</div>
                            <div>
                                <span class="doc-badge ready">Ready</span>
                                <h3>Dokumen Pembelian Paket</h3>
                                <p>Surat pembelian paket treatment dengan harga paket, masa berlaku, dan tabel rekap session/kedatangan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="doc-actions">
                        <a href="/admin/package-treatments" class="btn btn-primary">Buka Dokumen Paket</a>
                        <a href="/admin/package-treatments/create" class="btn btn-soft">+ Buat Baru</a>
                    </div>
                </div>

                <div class="doc-card">
                    <div>
                        <div class="doc-top">
                            <div class="doc-icon">SI</div>
                            <div>
                                <span class="doc-badge ready">Ready</span>
                                <h3>Surat Izin / Surat Istirahat Pasien</h3>
                                <p>Buat surat izin atau surat istirahat pasien berdasarkan kondisi fisioterapi, diagnosis, tanggal izin, dan pembatasan aktivitas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="doc-actions">
                        <a href="/admin/rest-letter/create" class="btn btn-primary">Buat Surat Izin</a>
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
