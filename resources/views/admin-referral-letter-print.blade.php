<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Surat Rujukan {{ $letter->letter_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f6f5; color: #1f2937; padding: 24px; }
        .page { max-width: 880px; margin: 0 auto; }
        .top-actions { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 14px; }
        .print-btn, .back-link { display: inline-flex; align-items: center; justify-content: center; text-decoration: none; padding: 11px 15px; border-radius: 12px; font-size: 13px; font-weight: 800; border: 1px solid transparent; cursor: pointer; font-family: Arial, sans-serif; }
        .back-link { background: #ffffff; color: #2f7c7a; border-color: #dfe8e6; }
        .print-btn { background: #2f7c7a; color: #ffffff; border: none; }
        .letter { background: #ffffff; border: 1px solid #e7eceb; border-radius: 20px; padding: 34px; box-shadow: 0 16px 32px rgba(15, 23, 42, 0.06); }
        .header { display: flex; align-items: center; gap: 16px; border-bottom: 2px solid #2f7c7a; padding-bottom: 18px; margin-bottom: 24px; }
        .logo { width: 64px; height: 64px; object-fit: contain; border-radius: 14px; }
        .clinic-name { margin: 0; font-size: 28px; color: #22343a; font-weight: 800; }
        .clinic-sub { margin: 6px 0 0; font-size: 13px; color: #6b7280; line-height: 1.6; }
        .title { text-align: center; margin: 28px 0 6px; font-size: 22px; font-weight: 800; text-decoration: underline; color: #111827; }
        .number { text-align: center; margin: 0 0 26px; font-size: 13px; color: #6b7280; }
        .meta { display: grid; grid-template-columns: 160px 1fr; gap: 8px 14px; margin-bottom: 24px; font-size: 14px; line-height: 1.7; }
        .meta-label { font-weight: 800; color: #374151; }
        .section { margin-top: 22px; }
        .section-title { font-size: 14px; font-weight: 800; color: #111827; margin-bottom: 8px; }
        .section-body { font-size: 14px; line-height: 1.9; color: #374151; white-space: pre-line; border: 1px solid #eef2f1; border-radius: 14px; padding: 14px 16px; background: #fbfcfc; }
        .closing { margin-top: 28px; font-size: 14px; line-height: 1.9; color: #374151; }
        .signature { display: flex; justify-content: flex-end; margin-top: 34px; }
        .signature-box { width: 260px; text-align: center; font-size: 14px; line-height: 1.8; }
        .signature-space { height: 72px; }
        @media print {
            body { background: #ffffff; padding: 0; }
            .top-actions { display: none; }
            .letter { box-shadow: none; border: none; border-radius: 0; }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="top-actions">
        <a href="/admin/referral-letters/{{ $letter->id }}" class="back-link">Kembali</a>
        <button onclick="window.print()" class="print-btn">Print / Save PDF</button>
    </div>

    <div class="letter">
        <div class="header">
            <img src="/images/khayra-logo.png" alt="Khayra Logo" class="logo">
            <div>
                <h1 class="clinic-name">Khayra Physio</h1>
                <p class="clinic-sub">Physiotherapy Clinic<br>Surat rujukan pasien dan dokumen klinik.</p>
            </div>
        </div>

        <div class="title">SURAT RUJUKAN</div>
        <div class="number">Nomor: {{ $letter->letter_number }}</div>

        <div class="meta">
            <div class="meta-label">Tanggal</div>
            <div>{{ optional($letter->letter_date)->format('Y-m-d') ?: '-' }}</div>

            <div class="meta-label">Nama Pasien</div>
            <div>{{ optional($letter->patient)->full_name ?: '-' }}</div>

            <div class="meta-label">No. Rekam Medis</div>
            <div>{{ optional($letter->patient)->medical_record_number ?: '-' }}</div>

            <div class="meta-label">Tanggal Lahir</div>
            <div>{{ optional(optional($letter->patient)->birth_date)->format('Y-m-d') ?: '-' }}</div>

            <div class="meta-label">Dirujuk Kepada</div>
            <div>{{ $letter->referral_to ?: '-' }}</div>
        </div>

        <div class="closing">
            Dengan hormat,<br>
            Bersama surat ini kami merujuk pasien tersebut untuk pemeriksaan / tindak lanjut sesuai kebutuhan klinis.
        </div>

        <div class="section">
            <div class="section-title">Alasan Rujukan</div>
            <div class="section-body">{{ $letter->referral_reason ?: '-' }}</div>
        </div>

        <div class="section">
            <div class="section-title">Ringkasan Klinis</div>
            <div class="section-body">{{ $letter->clinical_summary ?: '-' }}</div>
        </div>

        <div class="section">
            <div class="section-title">Rekomendasi</div>
            <div class="section-body">{{ $letter->recommendation ?: '-' }}</div>
        </div>

        @if($letter->notes)
            <div class="section">
                <div class="section-title">Catatan Tambahan</div>
                <div class="section-body">{{ $letter->notes }}</div>
            </div>
        @endif

        <div class="signature">
            <div class="signature-box">
                Hormat kami,
                <div class="signature-space"></div>
                <strong>Khayra Physio</strong>
            </div>
        </div>
    </div>
</div>
</body>
</html>
