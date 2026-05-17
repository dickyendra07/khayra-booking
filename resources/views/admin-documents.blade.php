<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Surat & Dokumen Klinik - Khayra Physio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;background:#f6f8f8;color:#17232b}
        .layout{display:flex;min-height:100vh}
        .main{flex:1;padding:28px;min-width:0}
        .container{max-width:1320px;margin:0 auto}
        .topbar{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;flex-wrap:wrap;margin-bottom:18px}
        .badge{display:inline-flex;padding:8px 13px;border-radius:999px;background:#eef7f5;color:#2f7c7a;font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px}
        h1{margin:0;font-size:42px;line-height:1.05;color:#22343a;letter-spacing:-.7px}
        .subtitle{margin:12px 0 0;color:#64748b;line-height:1.85;font-size:14px;max-width:880px}
        .hero{background:#fff;border:1px solid #ecefef;border-radius:28px;padding:28px;box-shadow:0 14px 34px rgba(15,23,42,.05);margin-bottom:18px}
        .hero-grid{display:grid;grid-template-columns:1.08fr .92fr;gap:18px}
        .hero-main{background:linear-gradient(135deg,#fff,#f7fbfa 58%,#eef7f5);border:1px solid #dfeae8;border-radius:24px;padding:24px}
        .hero-side{background:linear-gradient(145deg,#467f83,#346d73 52%,#244f55);color:#fff;border-radius:24px;padding:24px}
        .hero-side h2{margin:0 0 12px;font-size:28px;color:#fff}
        .hero-side p{margin:0;color:rgba(255,255,255,.88);line-height:1.85;font-size:13px}
        .stats{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:18px}
        .stat{background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.18);border-radius:18px;padding:16px}
        .stat-label{font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:rgba(255,255,255,.8);font-weight:900;margin-bottom:8px}
        .stat-value{font-size:30px;font-weight:900;color:#fff}
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}
        .card{background:#fff;border:1px solid #ecefef;border-radius:26px;padding:24px;box-shadow:0 10px 26px rgba(15,23,42,.04)}
        .card-top{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;margin-bottom:16px}
        .icon{width:48px;height:48px;border-radius:16px;background:#eef7f5;color:#2f7c7a;display:flex;align-items:center;justify-content:center;font-weight:900}
        .pill{display:inline-flex;padding:7px 11px;border-radius:999px;font-size:11px;font-weight:900;text-transform:uppercase}
        .pill-medium{background:#fff1c7;color:#92400e}
        .pill-premium{background:#eef2ff;color:#3157d8}
        h2{margin:0;color:#22343a;font-size:25px}
        .text{color:#64748b;line-height:1.8;font-size:13px;margin:8px 0 18px}
        .actions{display:flex;gap:10px;flex-wrap:wrap}
        .btn{min-height:42px;padding:0 15px;border-radius:14px;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;font-weight:900;font-size:13px}
        .btn-primary{background:linear-gradient(135deg,#3d8a89,#2f7c7a);color:#fff}
        .btn-soft{background:#fff;color:#2f7c7a;border:1px solid #dfe8e6}
        @media(max-width:980px){.layout{display:block}.main{padding:16px}.hero-grid,.grid,.stats{grid-template-columns:1fr}h1{font-size:34px}}
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'documents'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Phase 6</span>
                    <h1>Surat & Dokumen Klinik</h1>
                    <p class="subtitle">Pusat dokumen profesional: surat rujukan, surat kontrol/terapi, discharge summary, dan arsip informed consent.</p>
                </div>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div class="hero-main">
                        <span class="badge">Dokumen Profesional</span>
                        <h2>Dokumen bisa langsung di-print atau disimpan sebagai PDF dari browser.</h2>
                        <p class="subtitle">Modul ini membuat sistem terasa siap dipakai klinik sungguhan: semua dokumen mengambil data pasien, visit, dan rekam medis yang sudah ada.</p>
                    </div>
                    <aside class="hero-side">
                        <h2>Document Snapshot</h2>
                        <p>Ringkasan jumlah dokumen yang sudah tersimpan di sistem.</p>
                        <div class="stats">
                            <div class="stat"><div class="stat-label">Surat Rujukan</div><div class="stat-value">{{ $totalReferralLetters }}</div></div>
                            <div class="stat"><div class="stat-label">Consent Archive</div><div class="stat-value">{{ $totalConsents }}</div></div>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="grid">
                <div class="card">
                    <div class="card-top">
                        <div class="icon">REF</div>
                        <span class="pill pill-medium">Medium</span>
                    </div>
                    <h2>Surat Rujukan Profesional</h2>
                    <p class="text">Template rujukan klinik: tujuan, alasan, ringkasan klinis, rekomendasi, dan print PDF.</p>
                    <div class="actions">
                        <a href="/admin/referral-letters" class="btn btn-primary">Buka Surat Rujukan</a>
                        <a href="/admin/referral-letters/create" class="btn btn-soft">+ Buat Baru</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-top">
                        <div class="icon">CTRL</div>
                        <span class="pill pill-premium">Premium</span>
                    </div>
                    <h2>Surat Kontrol / Surat Terapi</h2>
                    <p class="text">Generate dokumen kontrol atau keterangan terapi dari medical record terbaru: jadwal kontrol, frekuensi, total sesi, dan rencana terapi.</p>
                    <div class="actions">
                        <a href="/admin/control-letter/create" class="btn btn-primary">Buat Surat Kontrol</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-top">
                        <div class="icon">SUM</div>
                        <span class="pill pill-premium">Premium</span>
                    </div>
                    <h2>Discharge Summary</h2>
                    <p class="text">Ringkasan selesai terapi: kondisi awal, kondisi akhir, program terapi, home exercise, dan rekomendasi lanjutan.</p>
                    <div class="actions">
                        <a href="/admin/discharge-summary/create" class="btn btn-primary">Buat Discharge Summary</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-top">
                        <div class="icon">IC</div>
                        <span class="pill pill-medium">Medium</span>
                    </div>
                    <h2>Digital Consent Archive</h2>
                    <p class="text">Arsip informed consent per pasien. Bisa dicari, dibuka ulang, dan dicetak kembali kapan pun dibutuhkan.</p>
                    <div class="actions">
                        <a href="/admin/consent-archive" class="btn btn-primary">Buka Consent Archive</a>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
