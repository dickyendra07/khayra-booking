<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $payload['document_type'] === 'therapy' ? 'Surat Keterangan Terapi' : 'Surat Kontrol' }} - Khayra Physio</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;background:#f4f6f5;color:#1f2937;padding:24px}
        .page{max-width:880px;margin:0 auto}
        .top{display:flex;justify-content:flex-end;gap:10px;margin-bottom:14px}
        .btn{border:0;border-radius:12px;padding:11px 15px;font-weight:900;cursor:pointer;text-decoration:none;font-size:13px}
        .btn-print{background:#2f7c7a;color:#fff}
        .sheet{background:#fff;border:1px solid #e7eceb;border-radius:20px;padding:34px;box-shadow:0 16px 32px rgba(15,23,42,.06)}
        .header{display:flex;align-items:center;gap:16px;border-bottom:2px solid #2f7c7a;padding-bottom:18px;margin-bottom:24px}
        .logo{width:64px;height:64px;object-fit:contain;border-radius:14px}
        .clinic h1{margin:0;font-size:28px;color:#22343a}
        .clinic p{margin:6px 0 0;color:#6b7280;font-size:13px;line-height:1.6}
        .title{text-align:center;margin:28px 0 6px;font-size:22px;font-weight:900;text-decoration:underline;color:#111827}
        .number{text-align:center;color:#6b7280;font-size:13px;margin-bottom:26px}
        .meta{display:grid;grid-template-columns:170px 1fr;gap:8px 14px;font-size:14px;line-height:1.7;margin-bottom:22px}
        .meta-label{font-weight:900;color:#374151}
        .body{font-size:14px;line-height:1.95;color:#374151}
        .section{margin-top:18px}
        .section-title{font-weight:900;margin-bottom:8px}
        .box{border:1px solid #eef2f1;background:#fbfcfc;border-radius:14px;padding:14px 16px;white-space:pre-line;line-height:1.9}
        .signature{display:flex;justify-content:flex-end;margin-top:36px}
        .signature-box{width:260px;text-align:center;font-size:14px;line-height:1.8}
        .space{height:72px}
        @media print{body{background:#fff;padding:0}.top{display:none}.sheet{box-shadow:none;border:0;border-radius:0}}
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
<div class="page">
    <div class="top">
        <button onclick="window.print()" class="btn btn-print">Print / Save PDF</button>
    </div>

    <div class="sheet">
        <div class="header">
            <img src="/images/khayra-logo.png" class="logo" alt="Khayra Logo">
            <div class="clinic">
                <h1>Khayra Physio</h1>
                <p>Leading Spinal & Pain Care<br>Jl. A. Yani No. 835C, Bandung</p>
            </div>
        </div>

        <div class="title">{{ $payload['document_type'] === 'therapy' ? 'SURAT KETERANGAN TERAPI' : 'SURAT KONTROL' }}</div>
        <div class="number">Tanggal: {{ \Carbon\Carbon::parse($payload['letter_date'])->format('Y-m-d') }}</div>

        <div class="meta">
            <div class="meta-label">Nama Pasien</div><div>{{ $patient->full_name }}</div>
            <div class="meta-label">No. Rekam Medis</div><div>{{ $patient->medical_record_number ?: '-' }}</div>
            <div class="meta-label">Tanggal Lahir</div><div>{{ $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '-' }}</div>
            <div class="meta-label">Tanggal Visit</div><div>{{ $visit && $visit->visit_date ? $visit->visit_date : '-' }}</div>
            <div class="meta-label">Fisioterapis</div><div>{{ $visit ? (optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: '-') : '-' }}</div>
        </div>

        <div class="body">
            Dengan ini menerangkan bahwa pasien tersebut menjalani layanan fisioterapi di Khayra Physio sesuai pemeriksaan dan kebutuhan klinis.
        </div>

        <div class="section">
            <div class="section-title">Diagnosis / Kondisi</div>
            <div class="box">{{ $payload['diagnosis'] ?: ($record ? ($record->physiotherapy_diagnosis ?: $record->assessment) : '-') ?: '-' }}</div>
        </div>

        <div class="section">
            <div class="section-title">Program Terapi</div>
            <div class="box">{{ $payload['therapy_program'] ?: ($record ? $record->program_patient : '-') ?: '-' }}</div>
        </div>

        <div class="section">
            <div class="section-title">Rencana Kontrol / Rekomendasi</div>
            <div class="box">{{ $payload['recommendation'] ?: ($record ? ($record->control_plan ?: $record->next_session_plan) : '-') ?: '-' }}</div>
        </div>

        <div class="section">
            <div class="section-title">Jadwal & Frekuensi</div>
            <div class="box">Tanggal kontrol berikutnya: {{ $payload['control_date'] ?: ($record && $record->date_of_control ? $record->date_of_control->format('Y-m-d') : '-') }}
Frekuensi terapi: {{ $payload['therapy_frequency'] ?: ($record ? $record->frequency_per_week : '-') ?: '-' }}
Total sesi: {{ $payload['total_session'] ?: ($record ? $record->total_session : '-') ?: '-' }}</div>
        </div>

        @if($payload['notes'])
            <div class="section">
                <div class="section-title">Catatan Tambahan</div>
                <div class="box">{{ $payload['notes'] }}</div>
            </div>
        @endif

        <div class="signature">
            <div class="signature-box">
                Hormat kami,
                <div class="space"></div>
                <strong>Khayra Physio</strong>
            </div>
        </div>
    </div>
</div>

</body>
</html>
