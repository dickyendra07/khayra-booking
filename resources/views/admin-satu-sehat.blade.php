<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satu Sehat Ready - Khayra Physio ERM</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f8f8;
            color: #17232b;
        }

        .layout {
            min-height: 100vh;
            display: flex;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 28px;
        }

        .container {
            max-width: 1320px;
            margin: 0 auto;
        }

        .hero {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.05);
            margin-bottom: 18px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.12fr .88fr;
            gap: 20px;
            align-items: stretch;
        }

        .badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #eef5f4;
            color: #35565d;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .hero-title {
            margin: 0;
            font-size: 42px;
            line-height: 1.06;
            color: #22343a;
            max-width: 820px;
            font-weight: 800;
        }

        .hero-text {
            margin: 16px 0 0;
            font-size: 14px;
            line-height: 1.95;
            color: #6b7280;
            max-width: 760px;
        }

        .tag-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .tag {
            display: inline-block;
            padding: 9px 13px;
            border-radius: 999px;
            background: #f7faf9;
            border: 1px solid #e7eceb;
            color: #486168;
            font-size: 12px;
            font-weight: 700;
        }

        .hero-side {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            border-radius: 24px;
            padding: 24px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .hero-side h3 {
            margin: 0 0 8px;
            font-size: 25px;
            line-height: 1.2;
        }

        .hero-side p {
            margin: 0;
            font-size: 13px;
            line-height: 1.85;
            color: rgba(255,255,255,.94);
        }

        .status-grid {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .status-card {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.18);
        }

        .status-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .45px;
            color: rgba(255,255,255,.88);
            margin-bottom: 6px;
            font-weight: 700;
        }

        .status-value {
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.5;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 18px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
        }

        .card-title {
            margin: 0;
            font-size: 22px;
            color: #22343a;
            font-weight: 800;
            line-height: 1.3;
        }

        .card-subtitle {
            margin: 8px 0 18px;
            font-size: 13px;
            line-height: 1.8;
            color: #6b7280;
        }

        .mini-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
            min-height: 168px;
        }

        .mini-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .mini-title {
            margin: 0;
            font-size: 17px;
            line-height: 1.35;
            color: #22343a;
            font-weight: 800;
        }

        .mini-text {
            margin: 0;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        .pill {
            display: inline-block;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
        }

        .pill-ready { background: #dcfce7; color: #166534; }
        .pill-need { background: #fef3c7; color: #92400e; }
        .pill-plan { background: #dbeafe; color: #1d4ed8; }
        .pill-dev { background: #f1f5f9; color: #475569; }

        .checklist {
            display: grid;
            gap: 12px;
        }

        .check-item {
            border: 1px solid #edf1f0;
            border-radius: 18px;
            padding: 16px;
            background: #fbfcfc;
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 12px;
            align-items: start;
        }

        .check-icon {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            background: #eef5f4;
            color: #2d5f66;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 900;
            flex-shrink: 0;
        }

        .check-title {
            font-size: 15px;
            font-weight: 800;
            color: #22343a;
            line-height: 1.5;
        }

        .check-text {
            margin-top: 5px;
            font-size: 12px;
            line-height: 1.7;
            color: #6b7280;
        }

        .notice {
            border-radius: 22px;
            padding: 20px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            font-size: 13px;
            line-height: 1.85;
            margin-bottom: 18px;
        }

        .action-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 800;
            border: 1px solid #e5eceb;
            color: #35565d;
            background: #f7faf9;
        }

        .btn-primary {
            background: #3f7f7e;
            color: #ffffff;
            border-color: #3f7f7e;
        }

        @media (max-width: 1180px) {
            .grid-3 { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 980px) {
            .layout { display: block; }
            .main { padding: 18px; }
            .hero-grid, .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .hero-title { font-size: 32px; }
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
        @include('partials.admin-sidebar', ['activeMenu' => 'satu-sehat'])

        <main class="main">
            <div class="container">
                <section class="hero">
                    <div class="hero-grid">
                        <div>
                            <span class="badge">Satu Sehat Ready</span>
                            <h1 class="hero-title">Halaman kesiapan integrasi Satu Sehat untuk ERM Khayra Physio.</h1>
                            <p class="hero-text">
                                Modul ini disiapkan sebagai blueprint integrasi Satu Sehat. Status saat ini adalah preparation layer:
                                sistem sudah punya data inti seperti pasien, visit, rekam medis, dan billing, tetapi koneksi real ke API
                                Satu Sehat membutuhkan credential resmi, mapping data, dan validasi standar dari ekosistem SATUSEHAT.
                            </p>

                            <div class="tag-row">
                                <span class="tag">Patient Mapping</span>
                                <span class="tag">Encounter / Visit</span>
                                <span class="tag">Medical Record Data</span>
                                <span class="tag">API Credential Checklist</span>
                            </div>

                            <div class="action-row">
                                <a class="btn btn-primary" href="/admin/patients">Cek Data Pasien</a>
                                <a class="btn" href="/admin/visits">Cek Visit & Rekam Medis</a>
                            </div>
                        </div>

                        <div class="hero-side">
                            <h3>Integration Status</h3>
                            <p>
                                Modul ini belum mengklaim koneksi live. Ini adalah halaman persiapan agar kebutuhan teknis jelas sebelum integrasi real.
                            </p>

                            <div class="status-grid">
                                <div class="status-card">
                                    <div class="status-label">Current stage</div>
                                    <div class="status-value">Ready for technical preparation</div>
                                </div>
                                <div class="status-card">
                                    <div class="status-label">Core data available</div>
                                    <div class="status-value">Pasien, visit, rekam medis</div>
                                </div>
                                <div class="status-card">
                                    <div class="status-label">Needed next</div>
                                    <div class="status-value">Credential resmi & API mapping</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="notice">
                    <strong>Catatan penting:</strong> wording yang aman untuk client adalah “Satu Sehat Ready” atau “siap integrasi Satu Sehat”,
                    bukan “sudah terintegrasi”, sampai credential resmi dan koneksi API benar-benar aktif.
                </div>

                <section class="grid-3">
                    <div class="mini-card">
                        <div class="mini-top">
                            <h3 class="mini-title">Credential Setup</h3>
                            <span class="pill pill-need">Needed</span>
                        </div>
                        <p class="mini-text">
                            Dibutuhkan Organization ID, Client ID, Client Secret, environment API, dan akses resmi dari pihak terkait.
                        </p>
                    </div>

                    <div class="mini-card">
                        <div class="mini-top">
                            <h3 class="mini-title">Patient Identity</h3>
                            <span class="pill pill-plan">Mapping</span>
                        </div>
                        <p class="mini-text">
                            Data pasien perlu dipetakan ke format yang sesuai, termasuk identitas, kontak, tanggal lahir, dan identifier.
                        </p>
                    </div>

                    <div class="mini-card">
                        <div class="mini-top">
                            <h3 class="mini-title">Encounter / Visit</h3>
                            <span class="pill pill-plan">Mapping</span>
                        </div>
                        <p class="mini-text">
                            Kunjungan pasien akan menjadi dasar encounter, jadwal layanan, therapist, dan relasi ke catatan klinis.
                        </p>
                    </div>

                    <div class="mini-card">
                        <div class="mini-top">
                            <h3 class="mini-title">Medical Record</h3>
                            <span class="pill pill-ready">Core Ready</span>
                        </div>
                        <p class="mini-text">
                            Rekam medis sudah menjadi modul inti dan bisa disiapkan untuk mapping diagnosis, assessment, dan treatment plan.
                        </p>
                    </div>

                    <div class="mini-card">
                        <div class="mini-top">
                            <h3 class="mini-title">Audit & Logs</h3>
                            <span class="pill pill-dev">Next</span>
                        </div>
                        <p class="mini-text">
                            Integrasi real perlu log request, response, error handling, retry, dan status sinkronisasi per data.
                        </p>
                    </div>

                    <div class="mini-card">
                        <div class="mini-top">
                            <h3 class="mini-title">Production Sync</h3>
                            <span class="pill pill-dev">Next</span>
                        </div>
                        <p class="mini-text">
                            Setelah credential aktif, sistem bisa dikembangkan untuk sinkronisasi data secara bertahap dan aman.
                        </p>
                    </div>
                </section>

                <section class="grid-2">
                    <div class="card">
                        <h2 class="card-title">Checklist Sebelum Integrasi Live</h2>
                        <p class="card-subtitle">Daftar hal yang perlu disiapkan client sebelum koneksi API Satu Sehat dibuat.</p>

                        <div class="checklist">
                            <div class="check-item">
                                <div class="check-icon">1</div>
                                <div>
                                    <div class="check-title">Credential resmi SATUSEHAT</div>
                                    <div class="check-text">Client perlu menyiapkan akses resmi, credential, dan environment API yang akan digunakan.</div>
                                </div>
                            </div>

                            <div class="check-item">
                                <div class="check-icon">2</div>
                                <div>
                                    <div class="check-title">Standarisasi data pasien</div>
                                    <div class="check-text">Biodata pasien perlu konsisten agar bisa dimapping ke standar integrasi.</div>
                                </div>
                            </div>

                            <div class="check-item">
                                <div class="check-icon">3</div>
                                <div>
                                    <div class="check-title">Mapping visit dan rekam medis</div>
                                    <div class="check-text">Alur appointment → visit → rekam medis harus rapi agar data yang dikirim valid.</div>
                                </div>
                            </div>

                            <div class="check-item">
                                <div class="check-icon">4</div>
                                <div>
                                    <div class="check-title">Testing sandbox/staging</div>
                                    <div class="check-text">Sebelum production, integrasi sebaiknya diuji di environment aman.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <h2 class="card-title">Rekomendasi Tahap Build</h2>
                        <p class="card-subtitle">Urutan pengembangan agar integrasi tidak mengganggu modul klinik yang sudah berjalan.</p>

                        <div class="checklist">
                            <div class="check-item">
                                <div class="check-icon">A</div>
                                <div>
                                    <div class="check-title">Rapikan appointment flow</div>
                                    <div class="check-text">Booking perlu terhubung lebih jelas ke pasien, visit, dan rekam medis.</div>
                                </div>
                            </div>

                            <div class="check-item">
                                <div class="check-icon">B</div>
                                <div>
                                    <div class="check-title">Perkuat format rekam medis</div>
                                    <div class="check-text">Gunakan struktur yang lebih klinis seperti keluhan, assessment, tindakan, dan rencana terapi.</div>
                                </div>
                            </div>

                            <div class="check-item">
                                <div class="check-icon">C</div>
                                <div>
                                    <div class="check-title">Buat integration log</div>
                                    <div class="check-text">Setiap data yang dikirim perlu status sukses/gagal agar mudah diaudit.</div>
                                </div>
                            </div>

                            <div class="check-item">
                                <div class="check-icon">D</div>
                                <div>
                                    <div class="check-title">Aktifkan API setelah credential siap</div>
                                    <div class="check-text">Koneksi live baru dilakukan setelah credential, mapping, dan testing siap.</div>
                                </div>
                            </div>
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
