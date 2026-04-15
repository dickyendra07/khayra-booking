<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informed Consent {{ $consent->patient->full_name }}</title>
    <style>
        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #1f2937;
            font-family: Arial, sans-serif;
        }

        body {
            padding: 16px;
        }

        .page {
            max-width: 920px;
            margin: 0 auto;
        }

        .top-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .action-link,
        .print-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 9px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: Arial, sans-serif;
        }

        .action-link {
            background: #ffffff;
            color: #2f6f72;
            border-color: #dbe5e3;
        }

        .print-btn {
            background: #2f6f72;
            color: #ffffff;
            border: none;
        }

        .sheet {
            background: #ffffff;
            border: 1px solid #dfe7e5;
            border-radius: 18px;
            padding: 18px 20px 14px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        }

        .doc-header {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 18px;
            align-items: start;
            padding-bottom: 12px;
            border-bottom: 1px solid #e7efed;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid #dfe7e5;
            padding: 4px;
            background: #ffffff;
        }

        .brand-text h1 {
            margin: 0;
            font-size: 24px;
            line-height: 1.05;
            font-weight: 800;
            color: #203338;
        }

        .brand-text .tagline {
            margin-top: 4px;
            font-size: 9px;
            letter-spacing: .8px;
            text-transform: uppercase;
            color: #7b8794;
            font-weight: 700;
        }

        .brand-text .address {
            margin-top: 6px;
            font-size: 9px;
            line-height: 1.45;
            color: #6b7280;
            max-width: 420px;
        }

        .doc-meta {
            min-width: 180px;
            border: 1px solid #e5ecea;
            border-radius: 12px;
            overflow: hidden;
        }

        .doc-meta-row {
            display: grid;
            grid-template-columns: 88px 1fr;
            border-bottom: 1px solid #eef3f2;
        }

        .doc-meta-row:last-child {
            border-bottom: none;
        }

        .doc-meta-label {
            padding: 8px 10px;
            background: #f7faf9;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #6f7c87;
            font-weight: 800;
        }

        .doc-meta-value {
            padding: 8px 10px;
            font-size: 10px;
            color: #22343a;
            font-weight: 700;
            line-height: 1.4;
        }

        .doc-title-wrap {
            text-align: center;
            padding: 12px 0 10px;
        }

        .doc-title {
            margin: 0;
            font-size: 21px;
            font-weight: 800;
            letter-spacing: .3px;
            color: #203338;
        }

        .doc-subtitle {
            margin: 5px auto 0;
            max-width: 700px;
            font-size: 10px;
            line-height: 1.45;
            color: #6b7280;
        }

        .section-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 8px;
        }

        .section-box {
            border: 1px solid #e6eceb;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
        }

        .section-box.full {
            grid-column: 1 / -1;
        }

        .section-head {
            padding: 7px 10px;
            background: #f7faf9;
            border-bottom: 1px solid #edf2f1;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .75px;
            color: #5f6f79;
            font-weight: 800;
        }

        .section-body {
            padding: 8px 10px;
        }

        .field-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .field {
            min-width: 0;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        .field-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: .45px;
            color: #7b8794;
            font-weight: 800;
            margin-bottom: 3px;
        }

        .field-value {
            font-size: 10px;
            line-height: 1.35;
            color: #1f2937;
            font-weight: 700;
            word-break: break-word;
        }

        .agreement-text {
            font-size: 9px;
            line-height: 1.45;
            color: #334155;
            text-align: justify;
            white-space: pre-line;
        }

        .signature-wrap {
            margin-top: 10px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .signature-box {
            padding-top: 6px;
        }

        .signature-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: .45px;
            color: #7b8794;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .signature-line {
            border-top: 1px solid #cfd8d6;
            padding-top: 4px;
            font-size: 9px;
            color: #6b7280;
            line-height: 1.3;
        }

        .footer {
            margin-top: 10px;
            padding-top: 7px;
            border-top: 1px solid #edf2f1;
            font-size: 8px;
            color: #94a3b8;
            line-height: 1.35;
            text-align: center;
        }

        @page {
            size: A4 portrait;
            margin: 7mm;
        }

        @media print {
            html, body {
                margin: 0;
                padding: 0;
                background: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            body {
                padding: 0;
            }

            .top-actions {
                display: none !important;
            }

            .page {
                max-width: 100%;
                margin: 0;
            }

            .sheet {
                border: none;
                border-radius: 0;
                box-shadow: none;
                padding: 0;
            }

            .doc-header {
                padding-bottom: 10px;
            }

            .brand-logo {
                width: 42px;
                height: 42px;
                padding: 3px;
            }

            .brand-text h1 {
                font-size: 21px;
            }

            .brand-text .tagline {
                font-size: 8px;
                margin-top: 2px;
            }

            .brand-text .address {
                font-size: 8px;
                margin-top: 4px;
                line-height: 1.35;
            }

            .doc-meta-row {
                grid-template-columns: 78px 1fr;
            }

            .doc-meta-label,
            .doc-meta-value {
                padding: 6px 8px;
                font-size: 8px;
            }

            .doc-title-wrap {
                padding: 9px 0 8px;
            }

            .doc-title {
                font-size: 18px;
            }

            .doc-subtitle {
                font-size: 9px;
                line-height: 1.3;
                margin-top: 4px;
            }

            .section-grid {
                gap: 8px;
                margin-top: 6px;
            }

            .section-head {
                padding: 6px 8px;
                font-size: 8px;
            }

            .section-body {
                padding: 7px 8px;
            }

            .field-grid {
                gap: 7px;
            }

            .field-label {
                font-size: 7px;
                margin-bottom: 2px;
            }

            .field-value {
                font-size: 9px;
                line-height: 1.25;
            }

            .agreement-text {
                font-size: 8px;
                line-height: 1.32;
            }

            .signature-wrap {
                margin-top: 8px;
                gap: 16px;
            }

            .signature-label {
                font-size: 7px;
                margin-bottom: 14px;
            }

            .signature-line {
                font-size: 8px;
                padding-top: 3px;
            }

            .footer {
                margin-top: 8px;
                padding-top: 5px;
                font-size: 7px;
                line-height: 1.25;
            }
        }

        @media (max-width: 760px) {
            .doc-header,
            .section-grid,
            .field-grid,
            .signature-wrap {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="top-actions">
        <a href="/admin/patients/{{ $consent->patient->id }}" class="action-link">← Kembali ke Detail Patient</a>
        <button onclick="window.print()" class="print-btn">Print / Save PDF</button>
    </div>

    <div class="sheet">
        <div class="doc-header">
            <div class="brand">
                <img src="/images/khayra-logo.png" alt="Khayra Logo" class="brand-logo">
                <div class="brand-text">
                    <h1>Khayra Physio</h1>
                    <div class="tagline">Leading Spinal & Pain Care</div>
                    <div class="address">
                        Jl. A. Yani No. 835C, Padasuka, Kec. Cibeunying Kidul, Kota Bandung, Jawa Barat 40125
                    </div>
                </div>
            </div>

            <div class="doc-meta">
                <div class="doc-meta-row">
                    <div class="doc-meta-label">Tanggal</div>
                    <div class="doc-meta-value">{{ $consent->consent_date ? $consent->consent_date->format('Y-m-d') : '-' }}</div>
                </div>
                <div class="doc-meta-row">
                    <div class="doc-meta-label">Status</div>
                    <div class="doc-meta-value">{{ ucfirst($consent->status ?: '-') }}</div>
                </div>
                <div class="doc-meta-row">
                    <div class="doc-meta-label">Consent ID</div>
                    <div class="doc-meta-value">#{{ $consent->id }}</div>
                </div>
            </div>
        </div>

        <div class="doc-title-wrap">
            <h2 class="doc-title">FORMULIR INFORMED CONSENT</h2>
            <p class="doc-subtitle">
                Dokumen persetujuan tindakan fisioterapi sebagai bagian dari administrasi dan pelaksanaan layanan klinik.
            </p>
        </div>

        <div class="section-grid">
            <div class="section-box">
                <div class="section-head">Identitas Patient</div>
                <div class="section-body">
                    <div class="field-grid">
                        <div class="field">
                            <div class="field-label">Medical Record Number</div>
                            <div class="field-value">{{ $consent->patient->medical_record_number ?: '-' }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Nama Patient</div>
                            <div class="field-value">{{ $consent->patient->full_name ?: '-' }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Gender</div>
                            <div class="field-value">{{ $consent->patient->gender ? ucfirst($consent->patient->gender) : '-' }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Tanggal Lahir</div>
                            <div class="field-value">{{ $consent->patient->birth_date ? $consent->patient->birth_date->format('Y-m-d') : '-' }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">WhatsApp</div>
                            <div class="field-value">{{ $consent->patient->whatsapp ?: '-' }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Alamat</div>
                            <div class="field-value">{{ $consent->patient->address ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-box">
                <div class="section-head">Detail Pelaksanaan</div>
                <div class="section-body">
                    <div class="field-grid">
                        <div class="field">
                            <div class="field-label">Nama Physiotherapy</div>
                            <div class="field-value">{{ $consent->physiotherapy_name ?: '-' }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Lokasi Tindakan</div>
                            <div class="field-value">{{ $consent->treatment_location ?: '-' }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Visit Terkait</div>
                            <div class="field-value">
                                @if($consent->visit)
                                    #{{ $consent->visit->id }} - {{ $consent->visit->visit_date ?: '-' }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="field">
                            <div class="field-label">Perwakilan</div>
                            <div class="field-value">{{ $consent->representative_name ?: '-' }}</div>
                        </div>
                        <div class="field full">
                            <div class="field-label">Hubungan dengan Patient</div>
                            <div class="field-value">{{ $consent->relationship_to_patient ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-box full">
                <div class="section-head">Pernyataan Persetujuan</div>
                <div class="section-body">
                    <div class="agreement-text">{{ $consent->agreement_text ?: '-' }}</div>
                </div>
            </div>

            @if($consent->notes)
                <div class="section-box full">
                    <div class="section-head">Catatan Tambahan</div>
                    <div class="section-body">
                        <div class="field-value">{{ $consent->notes }}</div>
                    </div>
                </div>
            @endif
        </div>

        <div class="signature-wrap">
            <div class="signature-box">
                <div class="signature-label">Tanda Tangan Patient / Perwakilan</div>
                <div class="signature-line">
                    {{ $consent->representative_name ?: $consent->patient->full_name }}
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-label">Tanda Tangan Physiotherapy</div>
                <div class="signature-line">
                    {{ $consent->physiotherapy_name ?: 'Khayra Physio' }}
                </div>
            </div>
        </div>

        <div class="footer">
            Dokumen ini dicetak melalui sistem internal Khayra Physio dan digunakan untuk kebutuhan administrasi layanan patient.
        </div>
    </div>
</div>
</body>
</html>