<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Timeline - Khayra Physio</title>
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
            max-width: 1380px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
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
            margin-bottom: 12px;
        }

        .title {
            margin: 0;
            font-size: 42px;
            line-height: 1.05;
            color: #22343a;
            font-weight: 900;
        }

        .subtitle {
            margin: 12px 0 0;
            max-width: 900px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.9;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn {
            min-height: 42px;
            border: 0;
            cursor: pointer;
            padding: 0 15px;
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
            background: #ffffff;
            border: 1px solid #e6ebea;
        }

        .btn-blue {
            background: #eef2ff;
            color: #3457d5;
            border: 1px solid #dde5ff;
        }

        .btn-green {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 18px;
            font-size: 14px;
            line-height: 1.7;
            font-weight: 700;
        }

        .alert-success {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .hero {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(15,23,42,.05);
            margin-bottom: 18px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.08fr .92fr;
            gap: 18px;
            align-items: stretch;
        }

        .hero-tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .hero-tag {
            display: inline-flex;
            padding: 9px 13px;
            border-radius: 999px;
            background: #f7faf9;
            border: 1px solid #e7eceb;
            color: #486168;
            font-size: 12px;
            font-weight: 800;
        }

        .hero-side {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            border-radius: 24px;
            color: #ffffff;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        .hero-side::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.13), transparent 28%),
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
            margin: 0 0 10px;
            color: #ffffff;
            font-size: 26px;
            line-height: 1.2;
        }

        .hero-side p {
            margin: 0;
            font-size: 13px;
            line-height: 1.85;
            color: rgba(255,255,255,.92);
        }

        .snapshot-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 18px;
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
            color: rgba(255,255,255,.82);
            margin-bottom: 6px;
            font-weight: 900;
        }

        .snapshot-value {
            font-size: 16px;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.45;
            word-break: break-word;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 10px 26px rgba(15,23,42,.04);
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #7b8794;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 28px;
            line-height: 1.05;
            font-weight: 900;
            color: #22343a;
            word-break: break-word;
        }

        .stat-sub {
            margin-top: 8px;
            font-size: 12px;
            line-height: 1.6;
            color: #94a3b8;
        }

        .page-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
            align-items: start;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 26px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15,23,42,.04);
            margin-bottom: 18px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .section-title {
            margin: 0;
            font-size: 25px;
            color: #22343a;
            line-height: 1.2;
            font-weight: 900;
        }

        .section-subtitle {
            margin: 8px 0 0;
            font-size: 13px;
            line-height: 1.8;
            color: #6b7280;
        }

        .identity-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .identity-item {
            border: 1px solid #edf1f0;
            border-radius: 18px;
            padding: 15px;
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
            font-weight: 900;
            margin-bottom: 8px;
        }

        .identity-value {
            font-size: 15px;
            line-height: 1.6;
            color: #22343a;
            font-weight: 800;
            word-break: break-word;
        }

        .timeline {
            position: relative;
            display: grid;
            gap: 14px;
        }

        .timeline-item {
            display: grid;
            grid-template-columns: 42px 1fr;
            gap: 12px;
            align-items: flex-start;
        }

        .timeline-dot {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
            color: #ffffff;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            box-shadow: 0 10px 18px rgba(47,124,122,.16);
        }

        .timeline-card {
            border: 1px solid #edf1f0;
            border-radius: 20px;
            padding: 16px;
            background: #fbfcfc;
        }

        .timeline-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .timeline-type {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #2f7c7a;
            font-weight: 900;
        }

        .timeline-title {
            margin-top: 4px;
            font-size: 16px;
            font-weight: 900;
            color: #22343a;
            line-height: 1.45;
        }

        .timeline-date {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 800;
            white-space: nowrap;
        }

        .timeline-meta {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .timeline-desc {
            font-size: 13px;
            color: #334155;
            line-height: 1.7;
        }

        .status-pill {
            display: inline-flex;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .status-pending, .status-unpaid { background: #fee2e2; color: #b91c1c; }
        .status-confirmed, .status-scheduled { background: #dbeafe; color: #1d4ed8; }
        .status-in_progress, .status-partial { background: #fef3c7; color: #92400e; }
        .status-completed, .status-paid, .status-signed { background: #dcfce7; color: #166534; }
        .status-cancelled, .status-void { background: #e5e7eb; color: #374151; }

        .mini-link {
            margin-top: 12px;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: #2f7c7a;
            background: #eef7f5;
            border: 1px solid #d8ebe7;
            padding: 9px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 900;
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
            min-width: 860px;
        }

        th {
            text-align: left;
            padding: 14px 15px;
            background: #f7faf9;
            color: #486168;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-bottom: 1px solid #edf1f0;
            white-space: nowrap;
        }

        td {
            padding: 15px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f2f5f5;
            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .primary-text {
            font-weight: 900;
            color: #22343a;
            line-height: 1.45;
        }

        .secondary-text {
            margin-top: 4px;
            font-size: 11px;
            line-height: 1.55;
            color: #94a3b8;
        }

        .money {
            font-weight: 900;
            color: #22343a;
            white-space: nowrap;
        }

        .empty-state {
            padding: 24px;
            text-align: center;
            color: #6b7280;
            line-height: 1.8;
        }

        @media (max-width: 1280px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .page-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 980px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-grid, .snapshot-grid, .identity-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .title { font-size: 32px; }
        }

        @media (max-width: 640px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    
        .patient-timeline-section {
            width: 100%;
            max-width: none;
        }

        .timeline.full-timeline {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .timeline.full-timeline .timeline-item {
            grid-template-columns: 54px 1fr;
            position: relative;
        }

        .timeline.full-timeline .timeline-dot {
            width: 46px;
            height: 46px;
            border-radius: 18px;
        }

        .timeline.full-timeline .timeline-card {
            background: #ffffff;
            border: 1px solid #edf1f0;
            box-shadow: 0 8px 20px rgba(15,23,42,.035);
        }

        .timeline.full-timeline .timeline-desc {
            margin-top: 10px;
        }

        .timeline-action-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

    
        /* Patient progress tracking premium */
        .progress-shell {
            display: grid;
            grid-template-columns: 0.95fr 1.05fr;
            gap: 18px;
            align-items: start;
        }

        .progress-form-card,
        .progress-timeline-card {
            background: #ffffff;
            border: 1px solid #e8eeee;
            border-radius: 26px;
            padding: 22px;
            box-shadow: 0 10px 26px rgba(15,23,42,.04);
        }

        .progress-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .progress-form-grid .full {
            grid-column: 1 / -1;
        }

        .progress-field label {
            display: block;
            margin-bottom: 8px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #64748b;
        }

        .progress-field input,
        .progress-field select,
        .progress-field textarea {
            width: 100%;
            min-height: 46px;
            border: 1px solid #d7dedd;
            border-radius: 14px;
            padding: 0 14px;
            background: #ffffff;
            color: #17232b;
            font-size: 13px;
            font-family: Arial, sans-serif;
            outline: none;
        }

        .progress-field textarea {
            min-height: 104px;
            padding-top: 12px;
            resize: vertical;
            line-height: 1.7;
        }

        .progress-field input:focus,
        .progress-field select:focus,
        .progress-field textarea:focus {
            border-color: #2f7c7a;
            box-shadow: 0 0 0 4px rgba(47,124,122,.08);
        }

        .progress-submit {
            width: 100%;
            min-height: 48px;
            border: 0;
            cursor: pointer;
            border-radius: 15px;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            font-size: 13px;
            font-weight: 900;
            font-family: Arial, sans-serif;
            box-shadow: 0 12px 24px rgba(47,124,122,.16);
        }

        .progress-help {
            margin-top: 12px;
            padding: 14px;
            border-radius: 18px;
            background: #f8faf9;
            border: 1px solid #edf1f0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.75;
        }

        .progress-timeline {
            display: grid;
            gap: 12px;
        }

        .progress-item {
            border: 1px solid #edf1f0;
            border-radius: 20px;
            padding: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfc 100%);
        }

        .progress-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
        }

        .progress-date {
            font-size: 16px;
            font-weight: 900;
            color: #22343a;
            line-height: 1.35;
        }

        .progress-visit {
            margin-top: 4px;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.55;
        }

        .pain-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef7f5;
            color: #2f7c7a;
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
        }

        .pain-bar {
            display: flex;
            gap: 5px;
            margin: 10px 0 12px;
        }

        .pain-dot {
            height: 9px;
            width: 22px;
            border-radius: 999px;
            background: #e5e7eb;
        }

        .pain-dot.active {
            background: #2f7c7a;
        }

        .progress-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .progress-note-box {
            border-radius: 16px;
            background: #f8faf9;
            border: 1px solid #edf1f0;
            padding: 13px;
        }

        .progress-note-label {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #94a3b8;
            margin-bottom: 7px;
        }

        .progress-note-value {
            font-size: 12px;
            line-height: 1.7;
            color: #334155;
            white-space: pre-line;
        }

        .progress-empty {
            padding: 18px;
            background: #fff7ed;
            color: #9a3412;
            border-radius: 16px;
            font-weight: 800;
            border: 1px solid #fed7aa;
            line-height: 1.7;
        }

        @media (max-width: 1050px) {
            .progress-shell,
            .progress-form-grid,
            .progress-grid {
                grid-template-columns: 1fr;
            }

            .progress-form-grid .full {
                grid-column: auto;
            }
        }

    
        .progress-action-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 14px;
        }
        .progress-mini-btn {
            min-height: 34px;
            padding: 0 12px;
            border-radius: 12px;
            border: 1px solid #e6ebea;
            background: #ffffff;
            color: #2f7c7a;
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-family: Arial, sans-serif;
        }
        .progress-mini-danger {
            background: #fff1f2;
            color: #be123c;
            border-color: #ffe0e6;
        }

    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'patients'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Patient Timeline</span>
                    <h1 class="title">{{ $patient->full_name }}</h1>
                    <p class="subtitle">
                        Pusat riwayat pasien: biodata, booking, visit, rekam medis, informed consent, invoice, payment, dan aktivitas klinik yang terhubung.
                    </p>
                </div>

                <div class="actions">
                    <a href="/admin/patients" class="btn btn-soft">← Patients</a>
                    <a href="/admin/patients/{{ $patient->id }}/edit" class="btn btn-blue">Edit Patient</a>
                    <a href="/admin/patients/{{ $patient->id }}/informed-consent" class="btn btn-soft">Informed Consent</a>
                    <a href="/admin/visits/create?patient_id={{ $patient->id }}" class="btn btn-green">+ Create Visit</a>
                    <a href="/admin/cashier?patient_id={{ $patient->id }}" class="btn btn-primary">Kasir Checkout</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <span class="badge">Patient Profile</span>
                        <h2 class="section-title">Identitas dan riwayat klinik pasien dalam satu halaman.</h2>
                        <p class="section-subtitle">
                            Halaman ini membantu admin dan fisioterapis memahami perjalanan pasien dari booking, visit, rekam medis, sampai pembayaran.
                        </p>

                        <div class="hero-tags">
                            <span class="hero-tag">{{ $patient->medical_record_number ?: 'MR belum terbentuk' }}</span>
                            <span class="hero-tag">{{ $patient->gender ? ucfirst($patient->gender) : 'Gender belum diisi' }}</span>
                            <span class="hero-tag">{{ $patient->birth_date ? $patient->birth_date->format('Y-m-d') : 'Tanggal lahir belum diisi' }}</span>
                            <span class="hero-tag">{{ $patient->whatsapp ?: 'WhatsApp belum diisi' }}</span>
                        </div>
                    </div>

                    <aside class="hero-side">
                        <h3>Patient Snapshot</h3>
                        <p>Ringkasan cepat untuk membaca status pasien, kontak, dan angka operasionalnya.</p>

                        <div class="snapshot-grid">
                            <div class="snapshot-card">
                                <div class="snapshot-label">Medical Record</div>
                                <div class="snapshot-value">{{ $patient->medical_record_number ?: 'Belum ada' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">WhatsApp</div>
                                <div class="snapshot-value">{{ $patient->whatsapp ?: '-' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Outstanding</div>
                                <div class="snapshot-value">Rp {{ number_format($outstandingTotal, 0, ',', '.') }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Latest Activity</div>
                                <div class="snapshot-value">
                                    @if($timeline->first())
                                        {{ $timeline->first()['date']->format('Y-m-d') }}
                                    @else
                                        Belum ada
                                    @endif
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Bookings</div>
                    <div class="stat-value">{{ $patient->bookings->count() }}</div>
                    <div class="stat-sub">Total appointment booking.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Visits</div>
                    <div class="stat-value">{{ $patient->visits->count() }}</div>
                    <div class="stat-sub">Total sesi fisioterapi.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Medical Records</div>
                    <div class="stat-value">{{ $patient->visits->filter(fn($visit) => $visit->medicalRecord)->count() }}</div>
                    <div class="stat-sub">Rekam medis yang tersedia.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Revenue</div>
                    <div class="stat-value">Rp {{ number_format($revenueTotal, 0, ',', '.') }}</div>
                    <div class="stat-sub">Total billing non-void.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Paid</div>
                    <div class="stat-value">Rp {{ number_format($paidTotal, 0, ',', '.') }}</div>
                    <div class="stat-sub">Total pembayaran diterima.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Consent</div>
                    <div class="stat-value">{{ $patient->informedConsents->count() }}</div>
                    <div class="stat-sub">{{ $voidTotal }} void invoice.</div>
                </div>
            </section>


                    <section class="section-card patient-timeline-section">
                        <div class="section-head">
                            <div>
                                <h2 class="section-title">Patient Timeline</h2>
                                <p class="section-subtitle">Gabungan aktivitas booking, visit, rekam medis, billing, dan consent terbaru.</p>
                            </div>
                        </div>

                        <div class="timeline full-timeline">
                            @forelse($timeline as $item)
                                <div class="timeline-item">
                                    <div class="timeline-dot">{{ strtoupper(substr($item['type'], 0, 2)) }}</div>

                                    <div class="timeline-card">
                                        <div class="timeline-top">
                                            <div>
                                                <div class="timeline-type">{{ $item['type'] }}</div>
                                                <div class="timeline-title">{{ $item['title'] }}</div>
                                            </div>

                                            <div class="timeline-date">{{ $item['date']->format('Y-m-d H:i') }}</div>
                                        </div>

                                        <div class="timeline-meta">{{ $item['meta'] }}</div>

                                        <div>
                                            <span class="status-pill status-{{ $item['status'] }}">{{ str_replace('_', ' ', $item['status']) }}</span>
                                        </div>

                                        <div class="timeline-desc">{{ $item['description'] ?: '-' }}</div>

                                        @if($item['url'])
                                            <a href="{{ $item['url'] }}" class="mini-link">Open Detail</a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    Belum ada timeline aktivitas untuk patient ini.
                                </div>
                            @endforelse
                        </div>
                    </section>


            <div class="page-grid">
                    <section class="section-card">
                        <div class="section-head">
                            <div>
                                <h2 class="section-title">Identitas Patient</h2>
                                <p class="section-subtitle">Data biodata pasien untuk administrasi, portal pasien, dan readiness integrasi.</p>
                            </div>
                        </div>

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
                        <div class="section-head">
                            <div>
                                <h2 class="section-title">Billing History</h2>
                                <p class="section-subtitle">Riwayat invoice dan pembayaran pasien.</p>
                            </div>
                        </div>

                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($patient->billings as $billing)
                                        <tr>
                                            <td>
                                                <div class="primary-text">{{ $billing->invoice_number ?: 'Billing #' . $billing->id }}</div>
                                                <div class="secondary-text">{{ $billing->items->count() }} item · {{ $billing->payment_method_label }}</div>
                                            </td>
                                            <td>{{ $billing->invoice_date ? $billing->invoice_date->format('Y-m-d') : '-' }}</td>
                                            <td class="money">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
                                            <td class="money">Rp {{ number_format($billing->paid_amount ?: 0, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="status-pill status-{{ $billing->payment_status }}">{{ $billing->payment_status }}</span>
                                            </td>
                                            <td>
                                                <a href="/admin/billings/{{ $billing->id }}" class="mini-link">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <div class="empty-state">Belum ada billing untuk patient ini.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <div>


            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Visit & Rekam Medis</h2>
                        <p class="section-subtitle">Riwayat visit fisioterapi dan status rekam medis pasien.</p>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Visit</th>
                                <th>Date</th>
                                <th>Therapist</th>
                                <th>Status</th>
                                <th>Medical Record</th>
                                <th>Notes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->visits as $visit)
                                <tr>
                                    <td>
                                        <div class="primary-text">Visit #{{ $visit->id }}</div>
                                        @if($visit->booking)
                                            <div class="secondary-text">Booking #{{ $visit->booking->id }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $visit->visit_date ?: '-' }}</td>
                                    <td>{{ optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: '-' }}</td>
                                    <td>
                                        <span class="status-pill status-{{ $visit->status }}">{{ str_replace('_', ' ', $visit->status ?: '-') }}</span>
                                    </td>
                                    <td>
                                        @if($visit->medicalRecord)
                                            <span class="status-pill status-completed">Available</span>
                                        @else
                                            <span class="status-pill status-pending">Not Created</span>
                                        @endif
                                    </td>
                                    <td>{{ $visit->notes ?: '-' }}</td>
                                    <td>
                                        <a href="/admin/visits/{{ $visit->id }}/medical-record" class="mini-link">Rekam Medis</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">Belum ada visit untuk patient ini.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Booking History</h2>
                        <p class="section-subtitle">Riwayat appointment booking pasien.</p>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Service</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th>Complaint</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->bookings as $booking)
                                <tr>
                                    <td>
                                        <div class="primary-text">Booking #{{ $booking->id }}</div>
                                        <div class="secondary-text">{{ $booking->full_name }}</div>
                                    </td>
                                    <td>{{ $booking->service ?: '-' }}</td>
                                    <td>{{ $booking->booking_date ?: '-' }} {{ $booking->booking_time ?: '' }}</td>
                                    <td>
                                        <span class="status-pill status-{{ $booking->status }}">{{ $booking->status ?: '-' }}</span>
                                    </td>
                                    <td>{{ $booking->complaint ?: '-' }}</td>
                                    <td>
                                        <a href="/admin/bookings/{{ $booking->id }}" class="mini-link">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">Belum ada booking untuk patient ini.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Informed Consent History</h2>
                        <p class="section-subtitle">Dokumen consent yang tersimpan untuk pasien.</p>
                    </div>
                    <a href="/admin/patients/{{ $patient->id }}/informed-consent" class="btn btn-soft">+ Create Consent</a>
                </div>

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
                                        <span class="status-pill status-{{ $consent->status ?: 'signed' }}">{{ $consent->status ?: 'signed' }}</span>
                                    </td>
                                    <td>
                                        <a href="/admin/informed-consents/{{ $consent->id }}/print" target="_blank" class="mini-link">Print</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">Belum ada informed consent untuk patient ini.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    
            <section class="section-card" id="progress-tracking">
                <div class="section-head">
                    <h2 class="section-title">Progress Tracking</h2>
                    <p class="section-subtitle">
                        Catat progress pasien per tanggal. Data ini akan tampil di Patient Portal sebagai timeline perkembangan terapi.
                    </p>
                </div>

                <div class="progress-shell">
                    <div class="progress-form-card">
                        <h3 style="margin:0 0 8px; font-size:22px; color:#22343a; font-weight:900;">Tambah Progress</h3>
                        <p style="margin:0 0 18px; color:#64748b; font-size:13px; line-height:1.8;">
                            Isi pain scale, ROM, functional goal, dan catatan progress setelah visit / follow-up.
                        </p>

                        <form method="POST" action="/admin/patients/{{ $patient->id }}/progress">
                            @csrf

                            <div class="progress-form-grid">
                                <div class="progress-field">
                                    <label>Tanggal Progress</label>
                                    <input type="date" name="entry_date" value="{{ now()->toDateString() }}" required>
                                </div>

                                <div class="progress-field">
                                    <label>Visit Terkait</label>
                                    <select name="visit_id">
                                        <option value="">Tidak dikaitkan ke visit</option>
                                        @foreach($patient->visits->sortByDesc('visit_date') as $visit)
                                            <option value="{{ $visit->id }}">
                                                {{ $visit->visit_date ?: '-' }} · {{ optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: 'Therapist' }} · {{ $visit->status ?: '-' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="progress-field full">
                                    <label>Pain Scale 0-10</label>
                                    <input type="number" name="pain_scale" min="0" max="10" placeholder="Contoh: 4">
                                </div>

                                <div class="progress-field full">
                                    <label>ROM / Movement Notes</label>
                                    <textarea name="rom_notes" placeholder="Contoh: fleksi bahu membaik, nyeri berkurang saat elevasi tangan..."></textarea>
                                </div>

                                <div class="progress-field full">
                                    <label>Functional Goal</label>
                                    <textarea name="functional_goal" placeholder="Contoh: pasien bisa angkat tangan tanpa nyeri, bisa berjalan 30 menit..."></textarea>
                                </div>

                                <div class="progress-field full">
                                    <label>Progress Notes</label>
                                    <textarea name="progress_notes" placeholder="Contoh: pasien sudah latihan mandiri 2x sehari, nyeri turun dari 7 ke 4..."></textarea>
                                </div>

                                <div class="progress-field full">
                                    <button type="submit" class="progress-submit">Simpan Progress</button>
                                </div>
                            </div>
                        </form>

                        <div class="progress-help">
                            <strong>Tips:</strong> gunakan progress tracking untuk mencatat perubahan yang mudah dipahami pasien:
                            nyeri, range of motion, kemampuan aktivitas, dan target fungsi.
                        </div>
                    </div>

                    <div class="progress-timeline-card">
                        <h3 style="margin:0 0 8px; font-size:22px; color:#22343a; font-weight:900;">Timeline Progress</h3>
                        <p style="margin:0 0 18px; color:#64748b; font-size:13px; line-height:1.8;">
                            Riwayat progress terbaru pasien ini.
                        </p>

                        @if(isset($progressEntries) && $progressEntries->count())
                            <div class="progress-timeline">
                                @foreach($progressEntries as $entry)
                                    <div class="progress-item">
                                        <div class="progress-top">
                                            <div>
                                                <div class="progress-date">{{ $entry->entry_date ? $entry->entry_date->format('Y-m-d') : '-' }}</div>
                                                <div class="progress-visit">
                                                    @if($entry->visit)
                                                        Visit #{{ $entry->visit->id }} · {{ $entry->visit->visit_date ?: '-' }}
                                                    @else
                                                        Tidak dikaitkan ke visit
                                                    @endif
                                                </div>
                                            </div>

                                            <span class="pain-badge">
                                                Pain {{ is_null($entry->pain_scale) ? '-' : $entry->pain_scale . '/10' }}
                                            </span>
                                        </div>

                                        <div class="pain-bar">
                                            @for($i = 1; $i <= 10; $i++)
                                                <span class="pain-dot {{ !is_null($entry->pain_scale) && $i <= $entry->pain_scale ? 'active' : '' }}"></span>
                                            @endfor
                                        </div>

                                        <div class="progress-grid">
                                            <div class="progress-note-box">
                                                <div class="progress-note-label">ROM / Movement</div>
                                                <div class="progress-note-value">{{ $entry->rom_notes ?: '-' }}</div>
                                            </div>

                                            <div class="progress-note-box">
                                                <div class="progress-note-label">Functional Goal</div>
                                                <div class="progress-note-value">{{ $entry->functional_goal ?: '-' }}</div>
                                            </div>

                                            <div class="progress-note-box" style="grid-column:1/-1;">
                                                <div class="progress-note-label">Progress Notes</div>
                                                <div class="progress-note-value">{{ $entry->progress_notes ?: '-' }}</div>

                                                        <div class="progress-action-row">
                                                            <a href="/admin/patients/{{ $patient->id }}/progress/{{ $entry->id }}/edit" class="progress-mini-btn">Edit</a>
                                                            <form method="POST" action="/admin/patients/{{ $patient->id }}/progress/{{ $entry->id }}/delete" style="margin:0;" onsubmit="return confirm('Hapus progress ini?')">
                                                                @csrf
                                                                <button type="submit" class="progress-mini-btn progress-mini-danger">Delete</button>
                                                            </form>
                                                        </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="progress-empty">
                                Belum ada progress tracking. Isi form di sebelah kiri untuk membuat progress pertama pasien ini.
                            </div>
                        @endif
                    </div>
                </div>
            </section>

        </main>
</div>
</body>
</html>
