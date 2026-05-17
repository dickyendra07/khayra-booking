<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at 10% 0%, rgba(47,124,122,.12), transparent 28%),
                linear-gradient(180deg, #eef5f4 0%, #f7faf9 42%, #ffffff 100%);
            color: #17232b;
        }

        .page {
            min-height: 100vh;
            padding: 34px;
        }

        .container {
            max-width: 1440px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .brand img {
            width: 46px;
            height: 46px;
            object-fit: contain;
            border-radius: 14px;
            background: rgba(255,255,255,.75);
            border: 1px solid rgba(47,124,122,.14);
            padding: 4px;
        }

        .brand-name {
            font-size: 17px;
            font-weight: 900;
            color: #22343a;
        }

        .badge {
            display: inline-flex;
            padding: 8px 13px;
            border-radius: 999px;
            background: #eef7f5;
            color: #2f7c7a;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 10px;
        }

        .title {
            margin: 0;
            font-size: 46px;
            line-height: 1.03;
            letter-spacing: -1px;
            color: #22343a;
            font-weight: 900;
        }

        .subtitle {
            margin: 12px 0 0;
            max-width: 780px;
            font-size: 14px;
            line-height: 1.9;
            color: #64748b;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn {
            min-height: 44px;
            padding: 0 18px;
            border-radius: 15px;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            font-size: 13px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            white-space: nowrap;
        }

        .btn-primary {
            color: #ffffff;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            box-shadow: 0 12px 24px rgba(47,124,122,.18);
        }

        .btn-soft {
            color: #2f7c7a;
            background: rgba(255,255,255,.88);
            border: 1px solid #dfeae8;
        }

        .btn-danger {
            color: #be123c;
            background: #fff1f2;
            border: 1px solid #ffe0e6;
        }

        .hero {
            background: #ffffff;
            border: 1px solid #e6eeee;
            border-radius: 34px;
            box-shadow: 0 24px 54px rgba(15,23,42,.08);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            min-height: 360px;
        }

        .hero-main {
            padding: 30px;
            background: linear-gradient(135deg, #ffffff 0%, #f9fcfb 52%, #eef7f5 100%);
        }

        .hero-side {
            padding: 30px;
            color: #ffffff;
            background:
                radial-gradient(circle at 80% 18%, rgba(255,255,255,.15), transparent 28%),
                linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-side::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px);
            background-size: 56px 56px;
            pointer-events: none;
        }

        .hero-side > * {
            position: relative;
            z-index: 1;
        }

        .profile-card {
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.20);
            border-radius: 24px;
            padding: 20px;
            margin-bottom: 18px;
        }

        .profile-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.78);
            font-weight: 900;
            margin-bottom: 7px;
        }

        .profile-value {
            font-size: 21px;
            line-height: 1.35;
            font-weight: 900;
            color: #ffffff;
        }

        .profile-muted {
            margin-top: 7px;
            color: rgba(255,255,255,.80);
            font-size: 13px;
            line-height: 1.7;
        }

        .mini-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-top: 22px;
        }

        .mini-card {
            background: #ffffff;
            border: 1px solid #e8eeee;
            border-radius: 22px;
            padding: 18px;
            box-shadow: 0 12px 28px rgba(15,23,42,.045);
        }

        .mini-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            font-weight: 900;
            margin-bottom: 12px;
        }

        .mini-value {
            font-size: 32px;
            font-weight: 900;
            line-height: 1;
            color: #22343a;
        }

        .mini-sub {
            margin-top: 10px;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.65;
        }

        .green .mini-value { color: #166534; }
        .blue .mini-value { color: #1d4ed8; }
        .orange .mini-value { color: #b45309; }
        .violet .mini-value { color: #6d28d9; }

        .main-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 20px;
            align-items: start;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #e8eeee;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 14px 34px rgba(15,23,42,.055);
            margin-bottom: 20px;
        }

        .section-title {
            margin: 0;
            color: #22343a;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -.4px;
        }

        .section-subtitle {
            margin: 8px 0 20px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }

        .therapy-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .therapy-card {
            border: 1px solid #edf1f0;
            border-radius: 20px;
            padding: 16px;
            background: #fbfcfc;
        }

        .therapy-card.full { grid-column: 1 / -1; }

        .therapy-label {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #7b8794;
            margin-bottom: 8px;
        }

        .therapy-value {
            font-size: 14px;
            line-height: 1.75;
            color: #22343a;
            font-weight: 750;
            white-space: pre-line;
        }

        .pain-scale {
            display: flex;
            gap: 6px;
            align-items: center;
            margin-top: 10px;
        }

        .pain-dot {
            width: 20px;
            height: 9px;
            border-radius: 999px;
            background: #e5e7eb;
        }

        .pain-dot.active {
            background: #2f7c7a;
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid #edf1f0;
            border-radius: 20px;
            background: #ffffff;
        }

        table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px 14px;
            border-bottom: 1px solid #edf1f0;
            text-align: left;
            vertical-align: top;
            font-size: 13px;
        }

        th {
            background: #f7faf9;
            color: #486168;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-size: 11px;
            font-weight: 900;
        }

        tbody tr:last-child td { border-bottom: 0; }

        .primary-text {
            font-weight: 900;
            color: #22343a;
            line-height: 1.45;
        }

        .secondary-text {
            margin-top: 4px;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.55;
        }

        .money {
            font-weight: 900;
            color: #22343a;
            white-space: nowrap;
        }

        .pill {
            display: inline-flex;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .pill-green { background: #dcfce7; color: #166534; }
        .pill-blue { background: #dbeafe; color: #1d4ed8; }
        .pill-orange { background: #fef3c7; color: #92400e; }
        .pill-red { background: #fee2e2; color: #b91c1c; }
        .pill-gray { background: #e5e7eb; color: #374151; }
        .pill-violet { background: #ede9fe; color: #6d28d9; }

        .list {
            display: grid;
            gap: 12px;
        }

        .list-item {
            border: 1px solid #edf1f0;
            border-radius: 20px;
            padding: 16px;
            background: #fbfcfc;
        }

        .list-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
        }

        .exercise-title {
            font-size: 16px;
            font-weight: 900;
            color: #22343a;
            margin-bottom: 5px;
        }

        .exercise-meta {
            font-size: 12px;
            line-height: 1.7;
            color: #64748b;
        }

        .empty-state {
            padding: 18px;
            background: #fff7ed;
            color: #9a3412;
            border: 1px solid #fed7aa;
            border-radius: 18px;
            font-weight: 800;
            line-height: 1.7;
        }

        .follow-card {
            background:
                radial-gradient(circle at top right, rgba(47,124,122,.13), transparent 30%),
                linear-gradient(135deg, #ffffff 0%, #f7fbfa 56%, #eef7f5 100%);
            border: 1px solid #dfeae8;
            border-radius: 24px;
            padding: 20px;
            margin-bottom: 14px;
        }

        .follow-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 14px;
        }

        .follow-label {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #2f7c7a;
            margin-bottom: 8px;
        }

        .follow-date {
            font-size: 30px;
            line-height: 1.08;
            font-weight: 900;
            color: #22343a;
        }

        .follow-desc {
            margin-top: 8px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.75;
        }

        .follow-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 14px;
        }

        .follow-mini {
            background: rgba(255,255,255,.82);
            border: 1px solid #e8eeee;
            border-radius: 17px;
            padding: 13px;
        }

        .follow-mini-label {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #94a3b8;
            margin-bottom: 6px;
        }

        .follow-mini-value {
            font-size: 13px;
            font-weight: 900;
            color: #22343a;
            line-height: 1.55;
        }


        form { margin: 0; }

        @media (max-width: 1100px) {
            .hero-grid, .main-grid, .therapy-grid { grid-template-columns: 1fr; }
            .therapy-card.full { grid-column: auto; }
        }

        @media (max-width: 760px) {
            .page { padding: 18px; }
            .topbar { display: block; }
            .actions { justify-content: flex-start; margin-top: 14px; }
            .title { font-size: 34px; }
            .mini-grid { grid-template-columns: 1fr; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="container">
        <div class="topbar">
            <div>
                <div class="brand">
                    <img src="/images/khayra-logo.png" alt="Khayra Logo">
                    <div>
                        <div class="brand-name">Khayra Physio</div>
                        <div class="secondary-text">Patient Portal</div>
                    </div>
                </div>

                <span class="badge">Patient Portal Plus</span>
                <h1 class="title">Halo, {{ $patient->full_name }}</h1>
                <p class="subtitle">
                    Ringkasan perjalanan terapi Anda di Khayra Physio: invoice, visit, home exercise, dan update terapi terbaru dari rekam medis.
                </p>
            </div>

            <div class="actions">
                <a href="/booking" class="btn btn-soft">Booking Baru</a>
                <form method="POST" action="/patient/logout">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>

        <section class="hero">
            <div class="hero-grid">
                <div class="hero-main">
                    <span class="badge">Therapy Snapshot</span>
                    <h2 class="section-title">Dashboard personal untuk melihat progress terapi Anda.</h2>
                    <p class="section-subtitle">
                        Data di bawah ini diambil dari visit, invoice, dan rekam medis yang sudah dicatat oleh tim Khayra Physio.
                    </p>

                    <div class="mini-grid">
                        <div class="mini-card blue">
                            <div class="mini-label">Total Visit</div>
                            <div class="mini-value">{{ $visits->count() }}</div>
                            <div class="mini-sub">{{ $completedVisits }} completed, {{ $activeVisits }} aktif / berjalan.</div>
                        </div>

                        <div class="mini-card green">
                            <div class="mini-label">Invoice</div>
                            <div class="mini-value">{{ $billings->count() }}</div>
                            <div class="mini-sub">Total invoice yang terhubung ke profil Anda.</div>
                        </div>

                        <div class="mini-card orange">
                            <div class="mini-label">Outstanding</div>
                            <div class="mini-value">Rp {{ number_format($outstandingTotal, 0, ',', '.') }}</div>
                            <div class="mini-sub">Sisa tagihan unpaid / partial.</div>
                        </div>

                        <div class="mini-card violet">
                            <div class="mini-label">Home Exercise</div>
                            <div class="mini-value">{{ $homeExercises->count() }}</div>
                            <div class="mini-sub">Latihan rumah dari rekam medis.</div>
                        </div>
                    </div>
                </div>

                <aside class="hero-side">
                    <div class="profile-card">
                        <div class="profile-label">Patient Profile</div>
                        <div class="profile-value">{{ $patient->full_name }}</div>
                        <div class="profile-muted">
                            MR: {{ $patient->medical_record_number ?: 'Belum tersedia' }}<br>
                            WhatsApp: {{ $patient->whatsapp ?: '-' }}<br>
                            Tanggal lahir: {{ $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '-' }}
                        </div>
                    </div>

                    <div class="profile-card">
                        <div class="profile-label">Latest Visit</div>
                        <div class="profile-value">
                            {{ $latestVisit ? ($latestVisit->visit_date ?: '-') : 'Belum ada visit' }}
                        </div>
                        <div class="profile-muted">
                            Therapist:
                            {{ $latestVisit ? (optional($latestVisit->therapistRelation)->full_name ?: $latestVisit->therapist ?: '-') : '-' }}<br>
                            Status:
                            {{ $latestVisit ? strtoupper($latestVisit->status ?: '-') : '-' }}
                        </div>
                    </div>

                    <div class="profile-card">
                        <div class="profile-label">Payment</div>
                        <div class="profile-value">Rp {{ number_format($paidTotal, 0, ',', '.') }}</div>
                        <div class="profile-muted">Total pembayaran diterima dari invoice valid.</div>
                    </div>
                </aside>
            </div>
        </section>

        <div class="main-grid">
            <div>
                <section class="section-card">
                    <h2 class="section-title">Latest Therapy Summary</h2>
                    <p class="section-subtitle">
                        Ringkasan terbaru dari rekam medis. Bagian ini dibuat lebih aman untuk pasien dan tidak menampilkan catatan internal berlebihan.
                    </p>

                    @if($latestMedicalRecord)
                        <div class="therapy-grid">
                            <div class="therapy-card">
                                <div class="therapy-label">Keluhan / Condition</div>
                                <div class="therapy-value">{{ $therapyHighlights['complaint'] ?: $therapyHighlights['assessment'] ?: 'Belum ada ringkasan keluhan.' }}</div>
                            </div>

                            <div class="therapy-card">
                                <div class="therapy-label">Pain Scale</div>
                                <div class="therapy-value">
                                    {{ $therapyHighlights['pain_scale'] !== null ? $therapyHighlights['pain_scale'] . ' / 10' : 'Belum dicatat' }}
                                    <div class="pain-scale">
                                        @for($i = 1; $i <= 10; $i++)
                                            <span class="pain-dot {{ $therapyHighlights['pain_scale'] && $i <= $therapyHighlights['pain_scale'] ? 'active' : '' }}"></span>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <div class="therapy-card full">
                                <div class="therapy-label">Treatment Terakhir</div>
                                <div class="therapy-value">{{ $therapyHighlights['treatment'] ?: 'Belum ada treatment yang bisa ditampilkan.' }}</div>
                            </div>

                            <div class="therapy-card">
                                <div class="therapy-label">Response / Progress</div>
                                <div class="therapy-value">{{ $therapyHighlights['response'] ?: 'Belum ada progress note.' }}</div>
                            </div>

                            <div class="therapy-card">
                                <div class="therapy-label">Rekomendasi / Next Plan</div>
                                <div class="therapy-value">{{ $therapyHighlights['recommendation'] ?: 'Belum ada rekomendasi lanjutan.' }}</div>
                            </div>

                            <div class="therapy-card">
                                <div class="therapy-label">Goal Terapi</div>
                                <div class="therapy-value">{{ $therapyHighlights['patient_goal'] ?: 'Belum dicatat.' }}</div>
                            </div>

                            <div class="therapy-card">
                                <div class="therapy-label">Program Pasien</div>
                                <div class="therapy-value">{{ $therapyHighlights['program_patient'] ?: 'Belum dicatat.' }}</div>
                            </div>

                            <div class="therapy-card full">
                                <div class="therapy-label">Kontrol Berikutnya</div>
                                <div class="therapy-value">{{ $therapyHighlights['control_date'] ?: 'Belum dijadwalkan / belum dicatat.' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">Belum ada rekam medis yang bisa ditampilkan di portal pasien.</div>
                    @endif
                </section>

                <section class="section-card">
                    <h2 class="section-title">Visit & Exercise History</h2>
                    <p class="section-subtitle">Riwayat visit dan jumlah latihan rumah yang tercatat pada setiap visit.</p>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Therapist</th>
                                    <th>Status</th>
                                    <th>Exercise</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $visit)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ $visit->visit_date ?: '-' }}</div>
                                            <div class="secondary-text">Visit #{{ $visit->id }}</div>
                                        </td>
                                        <td>{{ optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: '-' }}</td>
                                        <td>
                                            <span class="pill {{ $visit->status === 'completed' ? 'pill-green' : ($visit->status === 'in_progress' ? 'pill-orange' : 'pill-blue') }}">
                                                {{ $visit->status ?: '-' }}
                                            </span>
                                        </td>
                                        <td>{{ $visit->medicalRecord ? $visit->medicalRecord->homeExercises->count() : 0 }}</td>
                                        <td><a href="/patient/visits/{{ $visit->id }}" class="btn btn-soft">Detail</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Belum ada riwayat visit.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="section-card">
                    <h2 class="section-title">Invoice History</h2>
                    <p class="section-subtitle">Riwayat invoice dan status pembayaran Anda.</p>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($billings as $billing)
                                    <tr>
                                        <td>{{ $billing->invoice_date ? $billing->invoice_date->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            <div class="primary-text">{{ $billing->invoice_number ?: 'Invoice #' . $billing->id }}</div>
                                            <div class="secondary-text">{{ $billing->items->count() }} item</div>
                                        </td>
                                        <td class="money">Rp {{ number_format($billing->amount, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="pill {{ $billing->payment_status === 'paid' ? 'pill-green' : ($billing->payment_status === 'void' ? 'pill-gray' : 'pill-orange') }}">
                                                {{ $billing->payment_status }}
                                            </span>
                                        </td>
                                        <td><a href="/patient/invoices/{{ $billing->id }}" class="btn btn-soft">Detail</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Belum ada invoice.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <div>
                <section class="section-card">
                    <h2 class="section-title">Home Exercise</h2>
                    <p class="section-subtitle">Latihan rumah yang pernah dicatat oleh therapist.</p>

                    @if($homeExercises->count())
                        <div class="list">
                            @foreach($homeExercises->take(8) as $row)
                                <div class="list-item">
                                    <div class="list-top">
                                        <div>
                                            <div class="exercise-title">{{ $row['exercise']->exercise ?: 'Exercise' }}</div>
                                            <div class="exercise-meta">
                                                Visit: {{ $row['visit']->visit_date ?: '-' }}<br>
                                                Dosage: {{ $row['exercise']->dosage ?: '-' }}
                                            </div>
                                        </div>
                                        <span class="pill pill-green">Active</span>
                                    </div>
                                    @if($row['exercise']->note_caution)
                                        <div class="exercise-meta" style="margin-top:10px;">
                                            Catatan: {{ $row['exercise']->note_caution }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">Belum ada home exercise yang tercatat.</div>
                    @endif
                </section>

                <section class="section-card">
                    <h2 class="section-title">Progress Tracking</h2>
                    <p class="section-subtitle">Pantauan progress pasien dari catatan admin/therapist.</p>

                    @if($progressEntries->count())
                        <div class="list">
                            @foreach($progressEntries as $progress)
                                <div class="list-item">
                                    <div class="list-top">
                                        <div>
                                            <div class="exercise-title">
                                                {{ $progress->entry_date ? $progress->entry_date->format('Y-m-d') : 'Progress Entry' }}
                                            </div>
                                            <div class="exercise-meta">
                                                {{ $progress->visit_id ? 'Visit #' . $progress->visit_id : 'Progress umum pasien' }}
                                            </div>
                                        </div>
                                        <span class="pill pill-blue">
                                            {{ is_null($progress->pain_scale) ? 'Progress' : 'Pain ' . $progress->pain_scale . '/10' }}
                                        </span>
                                    </div>

                                    <div class="exercise-meta" style="margin-top:10px;">
                                        @if($progress->rom_notes)
                                            <strong>ROM / Movement:</strong> {{ $progress->rom_notes }}<br>
                                        @endif

                                        @if($progress->functional_goal)
                                            <strong>Goal:</strong> {{ $progress->functional_goal }}<br>
                                        @endif

                                        @if($progress->progress_notes)
                                            <strong>Progress:</strong> {{ $progress->progress_notes }}
                                        @endif

                                        @if(!$progress->rom_notes && !$progress->functional_goal && !$progress->progress_notes)
                                            Belum ada catatan detail progress.
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif($latestMedicalRecord && ($therapyHighlights['response'] || $therapyHighlights['recommendation']))
                        <div class="list">
                            <div class="list-item">
                                <div class="exercise-title">Progress terakhir</div>
                                <div class="exercise-meta">
                                    {{ $therapyHighlights['response'] ?: $therapyHighlights['recommendation'] }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">Belum ada progress tracking khusus.</div>
                    @endif
                </section>

                <section class="section-card">
                    <h2 class="section-title">Next Control / Follow-up</h2>
                    <p class="section-subtitle">Rencana kontrol berikutnya dari rekam medis terbaru.</p>

                    <div class="follow-card">
                        <div class="follow-top">
                            <div>
                                <div class="follow-label">Jadwal Kontrol Berikutnya</div>
                                <div class="follow-date">
                                    {{ $therapyHighlights['control_date'] ?: 'Belum dijadwalkan' }}
                                </div>
                                <div class="follow-desc">
                                    @if($latestMedicalRecord && ($latestMedicalRecord->control_plan || $latestMedicalRecord->next_session_plan || $latestMedicalRecord->recommendation))
                                        {{ $latestMedicalRecord->control_plan ?: ($latestMedicalRecord->next_session_plan ?: $latestMedicalRecord->recommendation) }}
                                    @else
                                        Rencana kontrol belum dicatat oleh therapist. Silakan konfirmasi ke admin Khayra Physio jika membutuhkan jadwal lanjutan.
                                    @endif
                                </div>
                            </div>

                            @if($therapyHighlights['control_date'])
                                <span class="pill pill-green">Scheduled</span>
                            @else
                                <span class="pill pill-orange">Pending</span>
                            @endif
                        </div>

                        <div class="follow-grid">
                            <div class="follow-mini">
                                <div class="follow-mini-label">Frequency</div>
                                <div class="follow-mini-value">
                                    {{ $latestMedicalRecord && $latestMedicalRecord->frequency_per_week ? $latestMedicalRecord->frequency_per_week : '-' }}
                                </div>
                            </div>

                            <div class="follow-mini">
                                <div class="follow-mini-label">Total Session</div>
                                <div class="follow-mini-value">
                                    {{ $latestMedicalRecord && $latestMedicalRecord->total_session ? $latestMedicalRecord->total_session : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="section-card">
                    <h2 class="section-title">Portal Information</h2>
                    <p class="section-subtitle">Informasi singkat untuk pasien.</p>

                    <div class="list">
                        <div class="list-item">
                            <div class="exercise-title">Data yang bisa diakses</div>
                            <div class="exercise-meta">
                                Profil pasien, invoice, status pembayaran, visit, ringkasan terapi, home exercise, progress tracking, dan rencana kontrol berikutnya.
                            </div>
                        </div>

                        <div class="list-item">
                            <div class="exercise-title">Butuh bantuan?</div>
                            <div class="exercise-meta">
                                Jika ada data yang kurang sesuai, silakan hubungi admin Khayra Physio untuk pengecekan.
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
</body>
</html>
