<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f8f8;
            color: #17232b;
        }

        .page {
            min-height: 100vh;
            padding: 28px;
        }

        .container {
            max-width: 1180px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 800;
            color: #22343a;
        }

        .brand img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 10px;
        }

        .topbar-actions {
            display: flex;
            gap: 10px;
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
            grid-template-columns: 1.15fr .85fr;
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

        .hero h1 {
            margin: 0;
            font-size: 40px;
            line-height: 1.08;
            color: #22343a;
            max-width: 680px;
        }

        .hero p {
            margin: 14px 0 0;
            font-size: 14px;
            line-height: 1.9;
            color: #6b7280;
            max-width: 650px;
        }

        .tag-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .tag {
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
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
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

        .hero-side-title {
            margin: 0 0 10px;
            font-size: 22px;
            line-height: 1.2;
            color: #ffffff;
            font-weight: 800;
        }

        .hero-side-text {
            margin: 0;
            font-size: 13px;
            line-height: 1.85;
            color: rgba(255,255,255,.96);
        }

        .mini-stack {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .mini-card {
            padding: 15px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.22);
            backdrop-filter: blur(4px);
        }

        .mini-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .45px;
            color: rgba(255,255,255,.92);
            margin-bottom: 7px;
            font-weight: 700;
        }

        .mini-value {
            font-size: 15px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.55;
            word-break: break-word;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 18px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
        }

        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #7b8794;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 34px;
            line-height: 1;
            font-weight: 800;
            color: #22343a;
        }

        .stat-sub {
            margin-top: 8px;
            font-size: 12px;
            line-height: 1.7;
            color: #94a3b8;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 18px;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
        }

        .section-card + .section-card {
            margin-top: 18px;
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

        .stack {
            display: grid;
            gap: 12px;
        }

        .info-box {
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid #edf1f0;
            background: #fafcfc;
        }

        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #7b8794;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 14px;
            line-height: 1.75;
            color: #24333a;
            word-break: break-word;
        }

        .callout {
            padding: 18px;
            border-radius: 18px;
            background: linear-gradient(180deg, #f9fbfb 0%, #f6f9f9 100%);
            border: 1px solid #e8eeee;
        }

        .callout-title {
            margin: 0 0 8px;
            font-size: 16px;
            color: #294047;
            font-weight: 800;
        }

        .callout-text {
            margin: 0;
            font-size: 13px;
            line-height: 1.85;
            color: #6b7280;
        }

        .summary-grid {
            display: grid;
            gap: 12px;
        }

        .exercise-grid {
            display: grid;
            gap: 12px;
        }

        .exercise-item {
            padding: 16px;
            border-radius: 18px;
            background: #fafcfc;
            border: 1px solid #edf1f0;
        }

        .exercise-name {
            font-size: 15px;
            font-weight: 800;
            color: #294047;
            margin-bottom: 12px;
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid #edf1f0;
            border-radius: 18px;
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
            padding: 15px 16px;
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
        }

        .secondary-text {
            margin-top: 4px;
            font-size: 11px;
            line-height: 1.6;
            color: #94a3b8;
        }

        .amount {
            font-weight: 800;
            color: #2f7c7a;
        }

        .status-pill {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
            text-transform: capitalize;
        }

        .status-scheduled { background: #dbeafe; color: #1d4ed8; }
        .status-in_progress { background: #fef3c7; color: #92400e; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        .billing-paid { background: #dcfce7; color: #166534; }
        .billing-unpaid { background: #fee2e2; color: #b91c1c; }
        .billing-partial { background: #fef3c7; color: #92400e; }

        .detail-link,
        .primary-link,
        .secondary-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
        }

        .detail-link {
            background: #f4f8f8;
            color: #2f7c7a;
            border: 1px solid #e3ebea;
        }

        .primary-link {
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            border: none;
        }

        .secondary-link {
            background: #ffffff;
            color: #2f7c7a;
            border: 1px solid #e4ebea;
        }

        .action-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .empty-state {
            padding: 26px;
            border-radius: 18px;
            border: 1px dashed #d9e2e1;
            background: #fafcfc;
            text-align: center;
            color: #7b8794;
            font-size: 13px;
            line-height: 1.8;
        }

        @media (max-width: 1080px) {
            .hero-grid,
            .content-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 680px) {
            .page {
                padding: 16px;
            }

            .hero,
            .section-card,
            .stat-card {
                padding: 20px;
                border-radius: 22px;
            }

            .hero h1 {
                font-size: 30px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .brand {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
@php
    $visitCount = $patient->visits->count();
    $billingCount = $patient->billings->count();
    $paidBillingCount = $patient->billings->where('payment_status', 'paid')->count();
    $latestVisit = $patient->visits->first();
    $latestRecord = $latestVisit?->medicalRecord;
    $homeExercises = $latestRecord && $latestRecord->homeExercises ? $latestRecord->homeExercises : collect();
    $adminWhatsapp = '6281234567890';
    $adminMessage = rawurlencode('Halo Admin Khayra Physio, saya ingin mengajukan perubahan data patient atas nama ' . ($patient->full_name ?? 'patient') . '.');
    $adminWhatsappLink = 'https://wa.me/' . $adminWhatsapp . '?text=' . $adminMessage;
@endphp

<div class="page">
    <div class="container">
        <div class="topbar">
            <div class="brand">
                <img src="/images/khayra-logo.png" alt="Khayra Logo">
                <span>Khayra Patient Portal</span>
            </div>

            <div class="topbar-actions">
                <a href="/patient" class="ghost-link">← Kembali ke Portal</a>
                <a href="/" class="ghost-link">Home</a>
            </div>
        </div>

        <section class="hero">
            <div class="hero-grid">
                <div>
                    <div class="hero-badge">Patient Dashboard</div>
                    <h1>Hello {{ explode(' ', $patient->full_name)[0] }}, your therapy information is available here.</h1>
                    <p>
                        Portal ini menampilkan informasi patient, riwayat visit, billing, dan ringkasan terapi
                        yang aman untuk ditinjau patient dengan tampilan yang lebih sederhana, tenang, dan mudah dipahami.
                    </p>

                    <div class="tag-row">
                        <span class="tag">{{ $visitCount }} visit recorded</span>
                        <span class="tag">{{ $billingCount }} billing entries</span>
                        <span class="tag">{{ $paidBillingCount }} paid billings</span>
                    </div>
                </div>

                <div class="hero-side">
                    <div>
                        <h3 class="hero-side-title">Latest Summary</h3>
                        </p>
                    </div>

                    <div class="mini-stack">
                        <div class="mini-card">
                            <div class="mini-label">Patient Name</div>
                            <div class="mini-value">{{ $patient->full_name }}</div>
                        </div>

                        <div class="mini-card">
                            <div class="mini-label">WhatsApp</div>
                            <div class="mini-value">{{ $patient->whatsapp ?: '-' }}</div>
                        </div>

                        <div class="mini-card">
                            <div class="mini-label">Latest Visit</div>
                            <div class="mini-value">{{ $latestVisit ? $latestVisit->visit_date : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Visits</div>
                <div class="stat-value">{{ $visitCount }}</div>
                <div class="stat-sub">Jumlah visit terapi yang tercatat pada patient ini.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Total Billings</div>
                <div class="stat-value">{{ $billingCount }}</div>
                <div class="stat-sub">Jumlah invoice yang terhubung dengan patient.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Paid Billings</div>
                <div class="stat-value">{{ $paidBillingCount }}</div>
                <div class="stat-sub">Billing yang sudah tercatat lunas.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Patient ID</div>
                <div class="stat-value">#{{ $patient->id }}</div>
                <div class="stat-sub">ID internal patient di sistem Khayra Physio.</div>
            </div>
        </section>

        <section class="content-grid">
            <div>
                <section class="section-card">
                    <h2 class="section-title">Patient Profile</h2>
                    <p class="section-subtitle">Informasi dasar patient yang tersimpan di sistem klinik.</p>

                    <div class="stack">
                        <div class="info-box">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">{{ $patient->full_name ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Gender</div>
                            <div class="info-value">{{ $patient->gender ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Birth Date</div>
                            <div class="info-value">{{ $patient->birth_date ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">WhatsApp</div>
                            <div class="info-value">{{ $patient->whatsapp ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Address</div>
                            <div class="info-value">{{ $patient->address ?: '-' }}</div>
                        </div>
                    </div>
                </section>

                <section class="section-card">
                    <h2 class="section-title">Portal Information</h2>
                    <p class="section-subtitle">Catatan singkat mengenai tampilan data pada patient portal.</p>

                    <div class="callout">
                        <h3 class="callout-title">Read-only patient access</h3>
                        <p class="callout-text">
                            Data yang tampil pada portal ini bersifat informasi patient. Jika ada data yang perlu diperbarui,
                            patient dapat menghubungi admin klinik untuk proses verifikasi dan perubahan data.
                        </p>
                    </div>
                </section>
            </div>

            <div>
                <section class="section-card">
                    <h2 class="section-title">Therapy Summary</h2>
                    <p class="section-subtitle">Ringkasan terapi patient berdasarkan visit terbaru yang tersedia.</p>

                    @if($latestVisit && $latestRecord)
                        <div class="summary-grid">
                            <div class="info-box">
                                <div class="info-label">Latest Visit Date</div>
                                <div class="info-value">{{ $latestVisit->visit_date ?: '-' }}</div>
                            </div>

                            <div class="info-box">
                                <div class="info-label">Therapist</div>
                                <div class="info-value">{{ $latestVisit->therapist ?: '-' }}</div>
                            </div>

                            <div class="info-box">
                                <div class="info-label">Chief Complaint</div>
                                <div class="info-value">{{ $latestRecord->complaint ?: '-' }}</div>
                            </div>

                            <div class="info-box">
                                <div class="info-label">Progress Note</div>
                                <div class="info-value">{{ $latestRecord->progress_note ?: '-' }}</div>
                            </div>

                            <div class="info-box">
                                <div class="info-label">Recommendation</div>
                                <div class="info-value">{{ $latestRecord->recommendation ?: $latestRecord->next_session_plan ?: '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            Belum ada ringkasan terapi yang tersedia untuk patient ini.
                        </div>
                    @endif
                </section>

                <section class="section-card">
                    <h2 class="section-title">Home Exercise Program</h2>
                    <p class="section-subtitle">Latihan rumah yang direkomendasikan therapist untuk patient.</p>

                    @if($homeExercises->count())
                        <div class="exercise-grid">
                            @foreach($homeExercises as $exercise)
                                <div class="exercise-item">
                                    <div class="exercise-name">{{ $exercise->exercise ?: 'Exercise' }}</div>

                                    <div class="stack">
                                        <div class="info-box">
                                            <div class="info-label">Dosage</div>
                                            <div class="info-value">{{ $exercise->dosage ?: '-' }}</div>
                                        </div>

                                        <div class="info-box">
                                            <div class="info-label">Note / Caution</div>
                                            <div class="info-value">{{ $exercise->note_caution ?: '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            Belum ada home exercise program yang tercatat untuk patient ini.
                        </div>
                    @endif
                </section>

                <section class="section-card">
                    <h2 class="section-title">Visit History</h2>
                    <p class="section-subtitle">Daftar riwayat visit terapi yang tercatat untuk patient.</p>

                    @if($patient->visits->count() > 0)
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Visit Date</th>
                                        <th>Therapist</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient->visits as $visit)
                                        <tr>
                                            <td>
                                                <div class="primary-text">{{ $visit->visit_date ?: '-' }}</div>
                                            </td>
                                            <td>
                                                <div class="primary-text">{{ $visit->therapist ?: '-' }}</div>
                                            </td>
                                            <td>
                                                <span class="status-pill status-{{ $visit->status }}">
                                                    {{ str_replace('_', ' ', $visit->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $visit->notes ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            Belum ada riwayat visit untuk patient ini.
                        </div>
                    @endif
                </section>

                <section class="section-card">
                    <h2 class="section-title">Billing History</h2>
                    <p class="section-subtitle">Daftar invoice dan status pembayaran yang terhubung ke patient.</p>

                    @if($patient->billings->count() > 0)
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
                                    @foreach($patient->billings as $billing)
                                        <tr>
                                            <td>
                                                <div class="primary-text">{{ $billing->invoice_number ?: '-' }}</div>
                                                <div class="secondary-text">Billing ID #{{ $billing->id }}</div>
                                            </td>
                                            <td>{{ $billing->invoice_date ?: '-' }}</td>
                                            <td>
                                                <div class="amount">Rp {{ number_format($billing->amount, 0, ',', '.') }}</div>
                                            </td>
                                            <td>
                                                <span class="status-pill billing-{{ $billing->payment_status }}">
                                                    {{ $billing->payment_status }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="/patient/billings/{{ $billing->id }}" class="detail-link">Lihat Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            Belum ada data billing untuk patient ini.
                        </div>
                    @endif
                </section>

                <section class="section-card">
                    <h2 class="section-title">Need Help?</h2>
                    <p class="section-subtitle">Hubungi admin klinik jika terdapat data yang belum sesuai.</p>

                    <div class="callout">
                        <h3 class="callout-title">Request data update</h3>
                        <p class="callout-text">
                            Jika ada informasi patient, jadwal, atau billing yang perlu dikoreksi, silakan hubungi admin klinik.
                            Tim admin akan membantu proses pengecekan dan pembaruan data yang diperlukan.
                        </p>

                        <div class="action-row">
                            <a href="{{ $adminWhatsappLink }}" target="_blank" class="primary-link">Hubungi Admin Klinik</a>
                            <a href="/patient" class="secondary-link">Kembali ke Portal</a>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</div>
</body>
</html>