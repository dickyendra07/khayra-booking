<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dokumen Pembelian Paket - {{ $document->document_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef2f2; font-family: "Times New Roman", serif; color: #111; }
        .toolbar { position: sticky; top: 0; z-index: 10; background: #ffffff; border-bottom: 1px solid #e5e7eb; padding: 12px 18px; display: flex; justify-content: space-between; gap: 10px; font-family: Arial, sans-serif; }
        .btn { min-height: 40px; border: 0; cursor: pointer; padding: 0 15px; border-radius: 12px; font-size: 13px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-family: Arial, sans-serif; }
        .btn-primary { background: #2f7c7a; color: #fff; }
        .btn-soft { background: #eef7f5; color: #2f7c7a; border: 1px solid #d8ebe7; }
        .page { width: 210mm; min-height: 297mm; margin: 18px auto; padding: 16mm 16mm; background: #fff; box-shadow: 0 20px 60px rgba(15,23,42,.18); }
        .invoice-header {
            background:
                linear-gradient(135deg, rgba(255,255,255,.08) 1px, transparent 1px),
                linear-gradient(135deg, #3d8a89 0%, #2f7c7a 52%, #244f55 100%);
            background-size: 28px 28px, auto;
            color: #ffffff;
            border-radius: 28px 28px 0 0;
            padding: 26px 28px;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 22px;
            align-items: start;
            font-family: Arial, sans-serif;
            margin: -16mm -16mm 20px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            background: rgba(255,255,255,.12);
            border-radius: 16px;
            padding: 8px;
        }
        .brand-name {
            margin: 0;
            font-size: 29px;
            line-height: 1;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -.8px;
        }
        .brand-sub {
            margin-top: 7px;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(255,255,255,.84);
        }
        .brand-desc {
            margin-top: 18px;
            font-size: 12px;
            line-height: 1.65;
            color: rgba(255,255,255,.92);
            max-width: 380px;
        }
        .doc-side {
            text-align: right;
            font-family: Arial, sans-serif;
        }
        .doc-label {
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: rgba(255,255,255,.78);
        }
        .doc-number {
            margin-top: 8px;
            font-size: 24px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -.6px;
        }
        .doc-pill {
            display: inline-flex;
            margin-top: 12px;
            padding: 8px 13px;
            border-radius: 999px;
            background: rgba(255,255,255,.16);
            color: #ffffff;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .doc-date {
            margin-top: 26px;
            font-size: 12px;
            line-height: 1.5;
            color: rgba(255,255,255,.88);
        }
        .print-card {
            border: 1px solid #edf1f0;
            border-radius: 22px;
            padding: 18px;
            margin-bottom: 16px;
            background: #ffffff;
        }
        .meta { border-top: 0; border-bottom: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 0; margin-bottom: 0; font-size: 15px; }
        .meta-row { display: grid; grid-template-columns: 150px 12px 1fr; gap: 4px; margin: 2px 0; }
        .info { margin-left: 18mm; max-width: 140mm; font-size: 15.5px; line-height: 1.35; }
        .info-row { display: grid; grid-template-columns: 165px 12px 1fr; gap: 4px; margin: 2px 0; }
        .statement { margin: 8px 0 18px; font-weight: 700; }
        .title { text-align: center; font-size: 17px; font-weight: 900; margin: 20px 0 18px; letter-spacing: .02em; }
        .terms { margin-top: 18px; font-size: 15.5px; line-height: 1.38; }
        .terms-title { font-weight: 900; margin-bottom: 5px; }
        .closing { margin-top: 20px; font-size: 15.5px; line-height: 1.45; }
        .signature { margin-top: 26px; width: 250px; text-align: left; font-size: 15.5px; }
        .signature-line { width: 170px; border-top: 2px solid #111; margin-top: 70px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 22px; font-size: 15px; border-radius: 18px; overflow: hidden; }
        th, td { border: 1px solid #333; padding: 10px; text-align: center; height: 42px; }
        th { font-weight: 900; background: #f6f8f8; font-family: Arial, sans-serif; font-size: 12px; letter-spacing: .08em; }
        .no { width: 45px; }
        .session { width: 170px; }
        .date { width: 230px; }
        .page-break { page-break-before: always; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .page { margin: 0; box-shadow: none; width: 100%; min-height: auto; }
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
<div class="toolbar">
    <a href="/admin/package-treatments" class="btn btn-soft">Kembali</a>
    <button onclick="window.print()" class="btn btn-primary">Print / Save PDF</button>
</div>

@php
    $patient = $document->patient;
    $therapist = $document->therapist;
    $sessionLabels = [
        1 => 'Pertama', 2 => 'Kedua', 3 => 'Ketiga', 4 => 'Keempat', 5 => 'Kelima', 6 => 'Keenam',
        7 => 'Ketujuh', 8 => 'Kedelapan', 9 => 'Kesembilan', 10 => 'Kesepuluh', 11 => 'Kesebelas', 12 => 'Keduabelas',
    ];
    $totalSessions = max((int) $document->total_sessions, 1);
@endphp

<div class="page">
    <div class="invoice-header">
        <div>
            <div class="brand">
                <img src="/images/khayra-logo.png" alt="Khayra Physio" class="logo">
                <div>
                    <h1 class="brand-name">Khayra Physio</h1>
                    <div class="brand-sub">Physiotherapy & Rehabilitation Clinic</div>
                </div>
            </div>

            <div class="brand-desc">
                Dokumen pembelian paket treatment fisioterapi, rekap session, masa berlaku paket, dan persetujuan administratif pasien.<br>
                Website: app.khayraphysio.com
            </div>
        </div>

        <div class="doc-side">
            <div class="doc-label">Package Document</div>
            <div class="doc-number">{{ $document->document_number ?: 'PKG-' . $document->id }}</div>
            <div class="doc-pill">{{ $document->package_type ?: 'Treatment Package' }}</div>
            <div class="doc-date">
                <div class="doc-label">Document Date</div>
                <strong>{{ optional($document->document_date)->format('Y-m-d') ?: '-' }}</strong>
            </div>
        </div>
    </div>

    <div class="print-card">
    <div class="meta">
        <div class="meta-row">
            <strong>Patient Record Number</strong><strong>:</strong>
            <strong>{{ $patient->medical_record_number ?: '-' }}</strong>
        </div>
        <div>
            <div class="meta-row"><strong>Date</strong><strong>:</strong><strong>{{ optional($document->document_date)->translatedFormat('d F Y') ?: '-' }}</strong></div>
            <div class="meta-row"><strong>Physiotherapist</strong><strong>:</strong><span>{{ $therapist->name ?? $therapist->full_name ?? $therapist->email ?? '-' }}</span></div>
        </div>
    </div>
    </div>

    <div class="info">
        <div class="info-row"><span>Patient Name</span><strong>:</strong><span>{{ $patient->full_name ?: '-' }}</span></div>
        <div class="info-row"><span>Gender</span><strong>:</strong><span>{{ $patient->gender === 'male' ? 'Laki-laki' : ($patient->gender === 'female' ? 'Perempuan' : '-') }}</span></div>
        <div class="info-row"><span>Birth Date</span><strong>:</strong><span>{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->translatedFormat('d F Y') : '-' }}</span></div>
        <div class="info-row"><span>Number Phone</span><strong>:</strong><span>{{ $patient->whatsapp ?: '-' }}</span></div>

        <div class="statement">Saya yang bertanda tangan dibawah ini menyatakan telah melakukan pembelian:</div>

        <div class="title">PACKAGE TREATMENT PHYSIOTHERAPY</div>

        <div class="info-row"><span>Package Name</span><strong>:</strong><span>{{ $document->package_name ?: '-' }}</span></div>
        <div class="info-row"><span>Payment Package</span><strong>:</strong><span>Rp {{ number_format($document->package_price, 0, ',', '.') }},-</span></div>
        <div class="info-row"><span>Date Buying Package</span><strong>:</strong><span>{{ optional($document->buying_date)->translatedFormat('d F Y') ?: '-' }}</span></div>
        <div class="info-row"><span>Valid Until Package</span><strong>:</strong><span>{{ optional($document->valid_until)->translatedFormat('d F Y') ?: '-' }}</span></div>

        <div class="terms">
            <div class="terms-title">Dengan syarat ketentuan :</div>
            <div style="white-space: pre-line;">{{ $document->terms }}</div>
        </div>

        <div class="closing">
            Demikian pernyataan ini saya setujui dan merupakan bagian dari Informed Consent dimana sebelumnya
            telah dijelaskan rencana perawatan yang dirujuk oleh fisioterapis.
        </div>

        <div class="signature">
            <div>Bandung, {{ optional($document->document_date)->translatedFormat('d F Y') ?: '-' }}</div>
            <div class="signature-line"></div>
            <div style="min-height: 18px;"></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no">NO</th>
                <th class="session">SESSION</th>
                <th class="date">DATE</th>
                <th>SIGNATURE</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i <= $totalSessions; $i++)
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $sessionLabels[$i] ?? 'Session ' . $i }}</td>
                    <td>{{ $i === 1 ? optional($document->buying_date)->translatedFormat('d F Y') : '' }}</td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>

</body>
</html>
