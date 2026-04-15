<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Detail - Khayra Physio</title>
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
            max-width: 1280px;
            margin: 0 auto;
        }
        .top-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .ghost-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #e6ebea;
            color: #2c5b5a;
            font-size: 13px;
            font-weight: 700;
        }
        .edit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 14px;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
        }
        .consent-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid #dce7e4;
            color: #2c5b5a;
            font-size: 14px;
            font-weight: 800;
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
            grid-template-columns: 1.1fr .9fr;
            gap: 20px;
            align-items: stretch;
        }
        .hero-badge {
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
            max-width: 760px;
        }
        .hero-text {
            margin: 16px 0 0;
            font-size: 14px;
            line-height: 1.95;
            color: #6b7280;
            max-width: 700px;
        }
        .hero-tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }
        .hero-tag {
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
        .hero-side::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.12), transparent 28%),
                linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px);
            background-size: auto, 56px 56px, 56px 56px;
            pointer-events: none;
        }
        .hero-side > * {
            position: relative;
            z-index: 1;
        }
        .hero-side h3 {
            margin: 0 0 8px;
            font-size: 26px;
            line-height: 1.2;
        }
        .hero-side p {
            margin: 0;
            font-size: 13px;
            line-height: 1.85;
            color: rgba(255,255,255,.94);
        }
        .snapshot-grid {
            display: grid;
            gap: 12px;
            margin-top: 20px;
        }
        .snapshot-card {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.18);
        }
        .snapshot-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .45px;
            color: rgba(255,255,255,.88);
            margin-bottom: 6px;
            font-weight: 700;
        }
        .snapshot-value {
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.5;
            word-break: break-word;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 18px;
        }
        .info-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
        }
        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #7b8794;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .info-value {
            font-size: 28px;
            line-height: 1.1;
            font-weight: 800;
            color: #22343a;
        }
        .info-sub {
            margin-top: 8px;
            font-size: 12px;
            line-height: 1.75;
            color: #94a3b8;
        }
        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
            margin-bottom: 18px;
        }
        .section-title {
            margin: 0;
            font-size: 24px;
            color: #22343a;
            line-height: 1.2;
        }
        .section-subtitle {
            margin: 8px 0 18px;
            font-size: 13px;
            line-height: 1.8;
            color: #6b7280;
        }
        .identity-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .identity-item {
            border: 1px solid #edf1f0;
            border-radius: 18px;
            padding: 16px;
            background: #fbfcfc;
        }
        .identity-item.full {
            grid-column: 1 / -1;
        }
        .identity-key {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .45px;
            color: #7b8794;
            font-weight: 800;
            margin-bottom: 8px;
        }
        .identity-value {
            font-size: 16px;
            line-height: 1.7;
            color: #22343a;
            font-weight: 700;
            word-break: break-word;
        }
        .table-wrap {
            overflow-x: auto;
            border: 1px solid #edf1f0;
            border-radius: 20px;
            background: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }
        th {
            text-align: left;
            padding: 15px 16px;
            background: #f7faf9;
            color: #486168;
            font-size: 12px;
            font-weight: 800;
            border-bottom: 1px solid #edf1f0;
        }
        td {
            padding: 16px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f2f5f5;
            vertical-align: top;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .primary-text {
            font-weight: 800;
            color: #22343a;
            line-height: 1.5;
        }
        .secondary-text {
            margin-top: 4px;
            font-size: 11px;
            line-height: 1.6;
            color: #94a3b8;
        }
        .status-pill {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: capitalize;
            white-space: nowrap;
        }
        .status-scheduled { background: #dbeafe; color: #1d4ed8; }
        .status-in_progress { background: #fef3c7; color: #92400e; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }
        .billing-paid { background: #dcfce7; color: #166534; }
        .billing-unpaid { background: #fee2e2; color: #b91c1c; }
        .billing-partial { background: #fef3c7; color: #92400e; }
        .consent-pending { background: #fef3c7; color: #92400e; }
        .consent-signed { background: #dcfce7; color: #166534; }
        @media (max-width: 1180px) {
            .info-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 960px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-grid, .identity-grid { grid-template-columns: 1fr; }
            .info-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 640px) {
            .info-grid { grid-template-columns: 1fr; }
            .hero-title { font-size: 32px; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'patients'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/patients" class="ghost-link">← Kembali ke Patients</a>
                <a href="/admin/patients/{{ $patient->id }}/informed-consent" class="consent-btn">Informed Consent</a>
                <a href="/admin/patients/{{ $patient->id }}/edit" class="edit-btn">Edit Patient</a>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <div class="hero-badge">Patient Detail</div>
                        <h1 class="hero-title">{{ $patient->full_name }}</h1>
                        <p class="hero-text">
                            Halaman ini menampilkan identitas lengkap patient, nomor rekam medis, ringkasan visit, billing, dan informed consent yang sudah terhubung di sistem Khayra Physio.
                        </p>

                        <div class="hero-tags">
                            <span class="hero-tag">{{ $patient->medical_record_number ?: 'MR belum terbentuk' }}</span>
                            <span class="hero-tag">{{ $patient->gender ? ucfirst($patient->gender) : 'Gender belum diisi' }}</span>
                            <span class="hero-tag">{{ $patient->birth_date ? $patient->birth_date->format('Y-m-d') : 'Tanggal lahir belum diisi' }}</span>
                        </div>
                    </div>

                    <div class="hero-side">
                        <div>
                            <h3>Patient Snapshot</h3>
                            <p>Ringkasan singkat untuk membaca identitas dasar patient secara cepat.</p>
                        </div>

                        <div class="snapshot-grid">
                            <div class="snapshot-card">
                                <div class="snapshot-label">Medical Record Number</div>
                                <div class="snapshot-value">{{ $patient->medical_record_number ?: 'Belum terbentuk' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">WhatsApp</div>
                                <div class="snapshot-value">{{ $patient->whatsapp ?: '-' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Latest Consent</div>
                                <div class="snapshot-value">
                                    {{ $patient->informedConsents->first() && $patient->informedConsents->first()->consent_date ? $patient->informedConsents->first()->consent_date->format('Y-m-d') : 'Belum ada' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="info-grid">
                <div class="info-card">
                    <div class="info-label">Total Visits</div>
                    <div class="info-value">{{ $patient->visits->count() }}</div>
                    <div class="info-sub">Jumlah visit yang sudah terhubung ke patient ini.</div>
                </div>

                <div class="info-card">
                    <div class="info-label">Total Billings</div>
                    <div class="info-value">{{ $patient->billings->count() }}</div>
                    <div class="info-sub">Jumlah billing / invoice yang sudah terhubung.</div>
                </div>

                <div class="info-card">
                    <div class="info-label">Total Consents</div>
                    <div class="info-value">{{ $patient->informedConsents->count() }}</div>
                    <div class="info-sub">Dokumen informed consent yang tersimpan.</div>
                </div>

                <div class="info-card">
                    <div class="info-label">Patient ID</div>
                    <div class="info-value">#{{ $patient->id }}</div>
                    <div class="info-sub">ID internal patient di sistem.</div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Identitas Patient</h2>
                <p class="section-subtitle">Data identitas lengkap patient yang digunakan untuk kebutuhan operasional klinik dan rekam medis.</p>

                <div class="identity-grid">
                    <div class="identity-item">
                        <div class="identity-key">Medical Record Number</div>
                        <div class="identity-value">{{ $patient->medical_record_number ?: '-' }}</div>
                    </div>

                    <div class="identity-item">
                        <div class="identity-key">NIK</div>
                        <div class="identity-value">{{ $patient->nik ?: '-' }}</div>
                    </div>

                    <div class="identity-item">
                        <div class="identity-key">Nama Lengkap</div>
                        <div class="identity-value">{{ $patient->full_name ?: '-' }}</div>
                    </div>

                    <div class="identity-item">
                        <div class="identity-key">Gender</div>
                        <div class="identity-value">{{ $patient->gender ? ucfirst($patient->gender) : '-' }}</div>
                    </div>

                    <div class="identity-item">
                        <div class="identity-key">Tanggal Lahir</div>
                        <div class="identity-value">{{ $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '-' }}</div>
                    </div>

                    <div class="identity-item">
                        <div class="identity-key">WhatsApp</div>
                        <div class="identity-value">{{ $patient->whatsapp ?: '-' }}</div>
                    </div>

                    <div class="identity-item">
                        <div class="identity-key">Agama</div>
                        <div class="identity-value">{{ $patient->religion ?: '-' }}</div>
                    </div>

                    <div class="identity-item">
                        <div class="identity-key">Pekerjaan</div>
                        <div class="identity-value">{{ $patient->occupation ?: '-' }}</div>
                    </div>

                    <div class="identity-item">
                        <div class="identity-key">Pendidikan</div>
                        <div class="identity-value">{{ $patient->education ?: '-' }}</div>
                    </div>

                    <div class="identity-item">
                        <div class="identity-key">Status Perkawinan</div>
                        <div class="identity-value">{{ $patient->marital_status ?: '-' }}</div>
                    </div>

                    <div class="identity-item full">
                        <div class="identity-key">Alamat</div>
                        <div class="identity-value">{{ $patient->address ?: '-' }}</div>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Informed Consent History</h2>
                <p class="section-subtitle">Riwayat informed consent patient yang tersimpan di sistem.</p>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Consent</th>
                                <th>Date</th>
                                <th>Visit</th>
                                <th>Physiotherapy</th>
                                <th>Status</th>
                                <th>Print</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->informedConsents as $consent)
                                <tr>
                                    <td>
                                        <div class="primary-text">Consent #{{ $consent->id }}</div>
                                        <div class="secondary-text">{{ $patient->medical_record_number ?: 'MR belum terbentuk' }}</div>
                                    </td>
                                    <td>{{ $consent->consent_date ? $consent->consent_date->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        @if($consent->visit)
                                            <div class="primary-text">Visit #{{ $consent->visit->id }}</div>
                                            <div class="secondary-text">{{ $consent->visit->visit_date ?: '-' }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $consent->physiotherapy_name ?: '-' }}</td>
                                    <td>
                                        <span class="status-pill consent-{{ $consent->status }}">
                                            {{ $consent->status ?: '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/admin/informed-consents/{{ $consent->id }}/print" class="ghost-link" target="_blank">Print</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">Belum ada informed consent untuk patient ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Riwayat Visits</h2>
                <p class="section-subtitle">Daftar visit patient yang sudah tercatat di sistem.</p>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Visit</th>
                                <th>Date</th>
                                <th>Physiotherapy</th>
                                <th>Status</th>
                                <th>Medical Record</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->visits as $visit)
                                <tr>
                                    <td>
                                        <div class="primary-text">Visit #{{ $visit->id }}</div>
                                        <div class="secondary-text">{{ $patient->medical_record_number ?: 'MR belum terbentuk' }}</div>
                                    </td>
                                    <td>{{ $visit->visit_date ?: '-' }}</td>
                                    <td>{{ $visit->therapist ?: '-' }}</td>
                                    <td>
                                        <span class="status-pill status-{{ $visit->status }}">
                                            {{ str_replace('_', ' ', $visit->status ?: '-') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($visit->medicalRecord)
                                            <a href="/admin/visits/{{ $visit->id }}/medical-record" class="ghost-link">View Record</a>
                                        @else
                                            <span class="secondary-text">Belum ada</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Belum ada data visit untuk patient ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Riwayat Billings</h2>
                <p class="section-subtitle">Daftar billing / invoice yang terhubung ke patient ini.</p>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Invoice Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->billings as $billing)
                                <tr>
                                    <td>
                                        <div class="primary-text">{{ $billing->invoice_number ?: '-' }}</div>
                                        <div class="secondary-text">Billing ID #{{ $billing->id }}</div>
                                    </td>
                                    <td>{{ $billing->invoice_date ?: '-' }}</td>
                                    <td class="primary-text">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="status-pill billing-{{ $billing->payment_status }}">
                                            {{ $billing->payment_status ?: '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/admin/billings/{{ $billing->id }}" class="ghost-link">Lihat Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Belum ada data billing untuk patient ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>