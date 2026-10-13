<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Invoice via WhatsApp - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f8f8;
            color: #17232b;
        }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1180px; margin: 0 auto; }
        .top-actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 18px;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
        }
        .title {
            font-size: 38px;
            font-weight: 800;
            color: #22343a;
            margin: 0 0 10px;
        }
        .subtitle {
            font-size: 14px;
            line-height: 1.8;
            color: #6b7280;
            margin: 0 0 22px;
        }
        .error-box {
            background: #fff1f2;
            border: 1px solid #ffe0e6;
            color: #be123c;
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 18px;
            line-height: 1.8;
            font-size: 13px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }
        .field.full { grid-column: 1 / -1; }
        .field label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }
        input, select, textarea {
            width: 100%;
            padding: 14px 14px;
            border: 1px solid #dde5e3;
            border-radius: 14px;
            font-size: 14px;
            background: #ffffff;
            color: #111827;
            font-family: Arial, sans-serif;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #176f69;
            box-shadow: 0 0 0 4px rgba(23,111,105,.08);
        }
        .readonly-box {
            width: 100%;
            padding: 14px 14px;
            border: 1px dashed #d9e4e1;
            border-radius: 14px;
            background: #f8fbfa;
            color: #334155;
            font-size: 14px;
            line-height: 1.7;
            font-weight: 700;
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 22px;
        }

        @media (max-width: 860px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .form-grid { grid-template-columns: 1fr; }
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
    @include('partials.ui-assets')
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'billings'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/billings/{{ $billing->id }}" class="kx-btn kx-btn--outline ghost-link">← Kembali ke Detail Billing</a>
            </div>

            <section class="section-card">
                <h1 class="title">Kirim Invoice via WhatsApp</h1>
                <p class="subtitle">
                    Periksa nomor tujuan dan isi pesan sebelum membuka WhatsApp. Pesan tidak terkirim otomatis dan PDF tidak terlampir otomatis.
                </p>

                <div class="wa-box">
                    <div class="wa-label">Nomor tujuan</div>
                    <div class="wa-number">{{ $displayNumber }}</div>
                    <div class="wa-meta">Pasien: {{ optional($billing->patient)->full_name ?: '-' }} · Invoice {{ $billing->invoice_number ?: '#' . $billing->id }}</div>
                    <div class="wa-warn">Pastikan nomor ini benar dan milik pasien yang bersangkutan sebelum melanjutkan.</div>
                </div>

                <div class="wa-box">
                    <div class="wa-label">Pratinjau pesan</div>
                    <pre class="wa-preview">{{ $message }}</pre>
                    <div class="wa-meta">Pesan sengaja hanya memuat nomor invoice, tanggal, total, dan status. Rincian layanan tidak disertakan.</div>
                </div>

                <div class="actions">
                    <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="kx-btn kx-btn--primary submit-btn" style="text-decoration:none;display:inline-flex;align-items:center;">Buka WhatsApp</a>
                    <a href="/admin/billings/{{ $billing->id }}/print" target="_blank" class="kx-btn kx-btn--outline ghost-link" style="margin-left:12px;">Print / Save PDF (lampirkan manual)</a>
                </div>
            </section>
        </div>
    </main>
</div>
<style>
    .wa-box { border: 1px solid #e6ebea; border-radius: 18px; padding: 16px; margin: 16px 0; background: #fbfcfc; }
    .wa-label { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
    .wa-number { font-size: 26px; font-weight: 900; margin: 6px 0; word-break: break-all; }
    .wa-meta { font-size: 13px; color: #64748b; margin-top: 6px; line-height: 1.6; }
    .wa-warn { margin-top: 10px; padding: 10px 12px; border-radius: 12px; background: #fffbeb; color: #92400e; font-size: 13px; font-weight: 700; }
    .wa-preview { white-space: pre-wrap; word-break: break-word; font-family: inherit; font-size: 14px; line-height: 1.7; margin: 8px 0 0; }
</style>
</body>
</html>
