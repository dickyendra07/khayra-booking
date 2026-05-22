<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Surat Izin / Istirahat Pasien - {{ $letterNumber }}</title>
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
            margin: -16mm -16mm 28px;
        }
        .brand { display: flex; align-items: center; gap: 14px; }
        .logo { width: 56px; height: 56px; object-fit: contain; background: rgba(255,255,255,.12); border-radius: 16px; padding: 8px; }
        .brand-name { margin: 0; font-size: 29px; line-height: 1; font-weight: 900; color: #ffffff; letter-spacing: -.8px; }
        .brand-sub { margin-top: 7px; font-size: 10px; font-weight: 900; letter-spacing: .12em; text-transform: uppercase; color: rgba(255,255,255,.84); }
        .brand-desc { margin-top: 18px; font-size: 12px; line-height: 1.65; color: rgba(255,255,255,.92); max-width: 380px; }
        .doc-side { text-align: right; font-family: Arial, sans-serif; }
        .doc-label { font-size: 10px; font-weight: 900; letter-spacing: .14em; text-transform: uppercase; color: rgba(255,255,255,.78); }
        .doc-number { margin-top: 8px; font-size: 24px; font-weight: 900; color: #ffffff; letter-spacing: -.6px; }
        .doc-pill { display: inline-flex; margin-top: 12px; padding: 8px 13px; border-radius: 999px; background: rgba(255,255,255,.16); color: #ffffff; font-size: 11px; font-weight: 900; text-transform: uppercase; }
        .doc-date { margin-top: 26px; font-size: 12px; line-height: 1.5; color: rgba(255,255,255,.88); }
        .title { text-align: center; font-size: 20px; font-weight: 900; text-decoration: underline; margin: 18px 0 26px; letter-spacing: .02em; }
        .content { font-size: 16px; line-height: 1.6; max-width: 165mm; margin: 0 auto; }
        .field-grid { margin: 14px 0 20px 10mm; }
        .field-row { display: grid; grid-template-columns: 160px 12px 1fr; gap: 4px; margin: 4px 0; }
        .paragraph { margin: 16px 0; text-align: justify; }
        .signature { margin-top: 42px; margin-left: auto; width: 260px; text-align: left; font-size: 16px; }
        .signature-line { width: 185px; border-top: 2px solid #111; margin-top: 78px; margin-bottom: 5px; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .page { margin: 0; box-shadow: none; width: 100%; min-height: auto; }
        }
    </style>
</head>
<body>
<div class="toolbar">
    <a href="/admin/rest-letter/create" class="btn btn-soft">Kembali</a>
    <button onclick="window.print()" class="btn btn-primary">Print / Save PDF</button>
</div>

@php
    $letterDate = !empty($data['letter_date']) ? \Carbon\Carbon::parse($data['letter_date']) : now();
    $startDate = !empty($data['rest_start_date']) ? \Carbon\Carbon::parse($data['rest_start_date']) : null;
    $endDate = !empty($data['rest_end_date']) ? \Carbon\Carbon::parse($data['rest_end_date']) : null;
    $therapistName = $therapist->name ?? $therapist->full_name ?? $therapist->email ?? '';
    $letterTitle = ($data['letter_type'] ?? 'istirahat') === 'izin'
        ? 'SURAT IZIN AKTIVITAS PASIEN'
        : 'SURAT ISTIRAHAT PASIEN';
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
                Surat keterangan izin atau istirahat pasien berdasarkan kondisi fisioterapi dan rekomendasi klinis.<br>
                Website: app.khayraphysio.com
            </div>
        </div>

        <div class="doc-side">
            <div class="doc-label">Letter Number</div>
            <div class="doc-number">{{ $letterNumber }}</div>
            <div class="doc-pill">{{ ($data['letter_type'] ?? 'istirahat') === 'izin' ? 'Izin Pasien' : 'Istirahat Pasien' }}</div>
            <div class="doc-date">
                <div class="doc-label">Letter Date</div>
                <strong>{{ $letterDate->format('Y-m-d') }}</strong>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="title">{{ $letterTitle }}</div>

        <p class="paragraph">
            Yang bertanda tangan di bawah ini menerangkan bahwa pasien dengan data sebagai berikut:
        </p>

        <div class="field-grid">
            <div class="field-row"><span>Nama Pasien</span><strong>:</strong><span>{{ $patient->full_name ?: '-' }}</span></div>
            <div class="field-row"><span>No. Rekam Medis</span><strong>:</strong><span>{{ $patient->medical_record_number ?: '-' }}</span></div>
            <div class="field-row"><span>Tanggal Lahir</span><strong>:</strong><span>{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->translatedFormat('d F Y') : '-' }}</span></div>
            <div class="field-row"><span>No. WhatsApp</span><strong>:</strong><span>{{ $patient->whatsapp ?: '-' }}</span></div>
            <div class="field-row"><span>Diagnosis/Kondisi</span><strong>:</strong><span>{{ $data['diagnosis'] ?: '-' }}</span></div>
        </div>

        <p class="paragraph">
            Berdasarkan pemeriksaan dan/atau proses fisioterapi di Khayra Physio, pasien tersebut
            @if(($data['letter_type'] ?? 'istirahat') === 'izin')
                memerlukan izin atau penyesuaian aktivitas
            @else
                disarankan untuk beristirahat
            @endif
            selama {{ $data['rest_days'] ?: '-' }} hari
            @if($startDate && $endDate)
                terhitung sejak tanggal {{ $startDate->translatedFormat('d F Y') }} sampai dengan {{ $endDate->translatedFormat('d F Y') }}.
            @else
                sesuai kebutuhan pemulihan pasien.
            @endif
        </p>

        @if(!empty($data['activity_limitation']))
            <p class="paragraph">
                Keterangan pembatasan aktivitas: {{ $data['activity_limitation'] }}
            </p>
        @endif

        @if(!empty($data['notes']))
            <p class="paragraph">
                Catatan tambahan: {{ $data['notes'] }}
            </p>
        @endif

        <p class="paragraph">
            Demikian surat ini dibuat agar dapat dipergunakan sebagaimana mestinya.
        </p>

        <div class="signature">
            <div>Bandung, {{ $letterDate->translatedFormat('d F Y') }}</div>
            <div class="signature-line"></div>
            <div>{{ $therapistName ?: '' }}</div>
        </div>
    </div>
</div>
</body>
</html>
