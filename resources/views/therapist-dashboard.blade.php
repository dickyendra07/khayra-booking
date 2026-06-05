<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Physio Dashboard - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #1f2937;
            background:
                radial-gradient(circle at top left, rgba(15,118,110,.10), transparent 30%),
                linear-gradient(180deg, #f6fbfa 0%, #eef7f5 100%);
        }

        .page {
            min-height: 100vh;
            padding: 24px 20px 40px;
        }

        .container {
            max-width: 1380px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .brand {
            font-size: 28px;
            font-weight: 900;
            color: #0f766e;
        }

        .topbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ghost-link,
        .logout-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 13px;
            font-size: 14px;
            font-weight: 800;
        }

        .ghost-link {
            background: white;
            color: #0f766e;
            border: 1px solid #d7ebe6;
        }

        .logout-btn {
            border: none;
            background: #111827;
            color: white;
            cursor: pointer;
            font-family: Arial, sans-serif;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.28fr .72fr;
            gap: 18px;
            margin-bottom: 20px;
        }

        .hero-main,
        .hero-side {
            border-radius: 30px;
            padding: 28px;
            box-shadow: 0 18px 42px rgba(15,118,110,.08);
        }

        .hero-main {
            background: linear-gradient(135deg, #0f766e 0%, #2f7f74 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-main::before {
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

        .hero-main > * { position: relative; z-index: 1; }

        .hero-side {
            background: white;
            border: 1px solid #e5f1ee;
        }

        .hero-badge {
            display: inline-flex;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.16);
            color: white;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .hero-title {
            margin: 0;
            font-size: 42px;
            line-height: 1.06;
            font-weight: 900;
        }

        .hero-text {
            margin: 14px 0 0;
            line-height: 1.85;
            font-size: 15px;
            color: rgba(255,255,255,.92);
            max-width: 760px;
        }

        .hero-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .hero-tag {
            display: inline-flex;
            padding: 9px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.18);
            color: white;
            font-size: 13px;
            font-weight: 800;
        }

        .side-title {
            margin: 0;
            font-size: 24px;
            color: #0f766e;
            font-weight: 900;
        }

        .side-subtitle {
            margin: 8px 0 18px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        .mini-grid {
            display: grid;
            gap: 12px;
        }

        .mini-box {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
        }

        .mini-label {
            font-size: 12px;
            font-weight: 800;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 6px;
        }

        .mini-value {
            font-size: 17px;
            font-weight: 900;
            color: #111827;
            line-height: 1.5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0,1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 22px;
            padding: 21px;
            box-shadow: 0 14px 35px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .5px;
            font-weight: 800;
        }

        .stat-value {
            font-size: 34px;
            font-weight: 900;
            color: #0f766e;
            margin-top: 10px;
        }

        .stat-sub {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 8px;
            line-height: 1.6;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            align-items: start;
        }

        .section-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
            margin-bottom: 18px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .section-title {
            margin: 0;
            font-size: 24px;
            color: #0f766e;
            font-weight: 900;
        }

        .section-subtitle {
            margin: 8px 0 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        .visit-card-grid {
            display: grid;
            gap: 14px;
        }

        .visit-card {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 20px;
            padding: 18px;
        }

        .visit-top {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .patient-name {
            font-weight: 900;
            font-size: 17px;
            color: #111827;
            line-height: 1.4;
        }

        .patient-sub {
            margin-top: 5px;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
        }

        .status-pill {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
            text-transform: uppercase;
        }

        .status-scheduled { background: #dbeafe; color: #1d4ed8; }
        .status-in_progress { background: #fef3c7; color: #92400e; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        .record-complete { background: #dcfce7; color: #166534; }
        .record-progress { background: #fef3c7; color: #92400e; }
        .record-empty { background: #fee2e2; color: #b91c1c; }

        .completion-wrap {
            margin-top: 12px;
        }

        .completion-head {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 12px;
            color: #4b5563;
            font-weight: 800;
        }

        .completion-bar {
            height: 12px;
            background: #e8f1ef;
            border-radius: 999px;
            overflow: hidden;
        }

        .completion-fill {
            height: 100%;
            background: linear-gradient(135deg, #0f766e 0%, #2f7f74 100%);
            border-radius: 999px;
        }

        .action-stack {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 14px;
        }

        .record-link,
        .report-link,
        .print-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            padding: 9px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 900;
            white-space: nowrap;
        }

        .record-link {
            background: #0f766e;
            color: #ffffff;
            border: 1px solid #0f766e;
        }

        .report-link {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #cfe0ff;
        }

        .print-link {
            background: #ffffff;
            color: #0f766e;
            border: 1px solid #d7ebe6;
        }

        .empty-state {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 18px;
            padding: 28px;
            color: #64748b;
            text-align: center;
            line-height: 1.7;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid #e8f1ef;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1080px;
        }

        th {
            background: #effaf7;
            color: #0f766e;
            text-align: left;
            padding: 16px;
            font-size: 13px;
            border-bottom: 1px solid #e5efec;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #eef2f1;
            font-size: 14px;
            color: #374151;
            vertical-align: top;
        }

        tr:hover td { background: #fafefd; }

        .notes-box {
            max-width: 260px;
            line-height: 1.7;
            color: #4b5563;
        }

        @media (max-width: 1180px) {
            .hero,
            .grid-2 { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 768px) {
            .page { padding: 16px 14px 32px; }
            .topbar { flex-direction: column; align-items: flex-start; }
            .hero-title { font-size: 30px; }
            .stats-grid { grid-template-columns: 1fr; }
            .hero-main, .hero-side, .section-card, .stat-card { padding: 20px; border-radius: 22px; }
            .brand { font-size: 24px; }
        }
    
        .appointment-card {
            border: 1px solid #dbecea;
            border-radius: 20px;
            background: #ffffff;
            padding: 16px;
            box-shadow: 0 12px 30px rgba(31,79,77,.06);
        }

        .appointment-list {
            display: grid;
            gap: 12px;
        }

        .appointment-row {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 14px;
            align-items: center;
            border: 1px solid #e3efed;
            border-radius: 18px;
            padding: 14px;
            background: #fbfffe;
        }

        .appointment-time {
            color: #1f4f4d;
            font-weight: 950;
            font-size: 14px;
        }

        .appointment-patient {
            color: #20343a;
            font-weight: 950;
            font-size: 14px;
        }

        .appointment-meta {
            margin-top: 4px;
            color: #64748b;
            font-weight: 750;
            font-size: 12px;
            line-height: 1.5;
        }

        .appointment-status {
            display: inline-flex;
            border-radius: 999px;
            padding: 7px 10px;
            background: #eafffb;
            color: #2f7c7a;
            font-size: 11px;
            font-weight: 950;
            text-transform: capitalize;
        }

        @media (max-width: 760px) {
            .appointment-row {
                grid-template-columns: 1fr;
            }
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
@php
    $latestVisit = $visits->first();
@endphp

<div class="page">
    <div class="container">
        <div class="topbar">
            <div class="brand">Khayra Physio Dashboard</div>

            <div class="topbar-actions">
                <a href="/" class="ghost-link">Home</a>
                <form method="POST" action="/therapist/logout" style="margin:0;">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>

        <section class="hero">
            <div class="hero-main">
                <div class="hero-badge">Physiotherapist Workspace</div>
                <h1 class="hero-title">Halo, {{ session('therapist_name') }}. Fokus hari ini: jadwal pasien, medical record, dan report fisioterapi.</h1>
                <p class="hero-text">
                    Dashboard ini membantu physiotherapist melihat jadwal pasien, membuka visit, melengkapi medical record,
                    serta mengakses clinical report dan print PDF dengan cepat.
                </p>

                <div class="hero-tags">
                    <span class="hero-tag">{{ $totalVisits }} total visits</span>
                    <span class="hero-tag">{{ $todayVisits->count() }} visit today</span>
                    <span class="hero-tag">{{ $needCompletionVisits->count() }} need completion</span>
                    <span class="hero-tag">{{ $completedRecordVisits }} complete records</span>
                </div>
            </div>

            <div class="hero-side">
                <h2 class="side-title">Ringkasan Terkini</h2>
                <p class="side-subtitle">Informasi singkat physiotherapist berdasarkan data visit terbaru.</p>

                <div class="mini-grid">
                    <div class="mini-box">
                        <div class="mini-label">Physiotherapist</div>
                        <div class="mini-value">{{ session('therapist_name') }}</div>
                    </div>

                    <div class="mini-box">
                        <div class="mini-label">Latest Visit</div>
                        <div class="mini-value">{{ $latestVisit ? $latestVisit->visit_date : '-' }}</div>
                    </div>

                    <div class="mini-box">
                        <div class="mini-label">Latest Patient</div>
                        <div class="mini-value">{{ $latestVisit && $latestVisit->patient ? $latestVisit->patient->full_name : '-' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Visits</div>
                <div class="stat-value">{{ $totalVisits }}</div>
                <div class="stat-sub">Semua visit yang menjadi tanggung jawab physiotherapist ini.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Visit Today</div>
                <div class="stat-value">{{ $todayVisits->count() }}</div>
                <div class="stat-sub">Visit yang dijadwalkan hari ini.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Need Completion</div>
                <div class="stat-value">{{ $needCompletionVisits->count() }}</div>
                <div class="stat-sub">Rekam medis belum mencapai 90% kelengkapan.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Completed Visits</div>
                <div class="stat-value">{{ $completedVisits }}</div>
                <div class="stat-sub">Visit dengan status completed.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Complete Records</div>
                <div class="stat-value">{{ $completedRecordVisits }}</div>
                <div class="stat-sub">Rekam medis utama sudah lengkap.</div>
            </div>
        </section>

        <div class="grid-2">
            
                <section class="section-card appointment-card">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Clinic Appointment Schedule</h2>
                            <p class="section-subtitle">Jadwal pasien aktif dari admin maupun patient portal. Physiotherapist bisa melihat seluruh jadwal klinik untuk koordinasi operasional.</p>
                        </div>
                    </div>

                    <div class="appointment-list">
                        @forelse($upcomingAppointments as $booking)
                            <div class="appointment-row">
                                <div>
                                    <div class="appointment-time">{{ $booking->booking_date ?: '-' }}</div>
                                    <div class="appointment-meta">{{ $booking->booking_time ? substr($booking->booking_time, 0, 5) : '-' }}</div>
                                </div>

                                <div>
                                    <div class="appointment-patient">{{ optional($booking->patient)->full_name ?: $booking->full_name }}</div>
                                    <div class="appointment-meta">
                                        Booking #{{ $booking->id }} · {{ $booking->service ?: 'Service belum dipilih' }}
                                        <br>Room: {{ $booking->room_name ?: 'Belum ditentukan' }}
                                        @if($booking->complaint)
                                            <br>{{ $booking->complaint }}
                                        @endif
                                    </div>
                                </div>

                                <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                                    <span class="appointment-status">{{ str_replace('_', ' ', $booking->status ?: 'pending') }}</span>

                                    @if($booking->linked_visit && (int) $booking->therapist_id === (int) session('therapist_id'))
                                        <a href="/therapist/visits/{{ $booking->linked_visit->id }}/medical-record" class="record-link">Medical Record</a>
                                    @elseif($booking->linked_visit)
                                        <span class="appointment-status">Visit Created</span>
                                    @else
                                        <span class="appointment-status">Waiting Visit</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-block">Belum ada appointment aktif untuk jadwal klinik saat ini.</div>
                        @endforelse
                    </div>
                </section>


<section class="section-card">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Today Visits</h2>
                        <p class="section-subtitle">Visit hari ini untuk physiotherapist ini.</p>
                    </div>
                </div>

                <div class="visit-card-grid">
                    @forelse($todayVisits as $visit)
                        <div class="visit-card">
                            <div class="visit-top">
                                <div>
                                    <div class="patient-name">{{ optional($visit->patient)->full_name ?: '-' }}</div>
                                    <div class="patient-sub">Visit #{{ $visit->id }} · {{ $visit->visit_date ?: '-' }} · Room: {{ $visit->room_name ?: optional($visit->booking)->room_name ?: 'Belum ditentukan' }}</div>
                                </div>
                                <span class="status-pill status-{{ $visit->status }}">{{ str_replace('_', ' ', $visit->status ?: '-') }}</span>
                            </div>

                            <div class="completion-wrap">
                                <div class="completion-head">
                                    <span>Record completeness</span>
                                    <span>{{ $visit->record_completion }}%</span>
                                </div>
                                <div class="completion-bar">
                                    <div class="completion-fill" style="width: {{ $visit->record_completion }}%;"></div>
                                </div>
                            </div>

                            <div class="action-stack">
                                <a href="/therapist/visits/{{ $visit->id }}/medical-record" class="record-link">Medical Record</a>
                                <a href="/therapist/visits/{{ $visit->id }}/report" class="report-link">Report</a>
                                <a href="/therapist/visits/{{ $visit->id }}/report/print" target="_blank" class="print-link">Print</a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">Belum ada visit untuk hari ini.</div>
                    @endforelse
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Need Record Completion</h2>
                        <p class="section-subtitle">Visit yang rekam medisnya masih perlu dilengkapi.</p>
                    </div>
                </div>

                <div class="visit-card-grid">
                    @forelse($needCompletionVisits as $visit)
                        <div class="visit-card">
                            <div class="visit-top">
                                <div>
                                    <div class="patient-name">{{ optional($visit->patient)->full_name ?: '-' }}</div>
                                    <div class="patient-sub">Visit #{{ $visit->id }} · {{ $visit->visit_date ?: '-' }} · Room: {{ $visit->room_name ?: optional($visit->booking)->room_name ?: 'Belum ditentukan' }}</div>
                                </div>

                                @if($visit->record_completion >= 90)
                                    <span class="status-pill record-complete">Complete</span>
                                @elseif($visit->record_completion > 0)
                                    <span class="status-pill record-progress">In Progress</span>
                                @else
                                    <span class="status-pill record-empty">Not Started</span>
                                @endif
                            </div>

                            <div class="completion-wrap">
                                <div class="completion-head">
                                    <span>{{ $visit->record_completed_fields }} / {{ $visit->record_total_fields }} clinical fields</span>
                                    <span>{{ $visit->record_completion }}%</span>
                                </div>
                                <div class="completion-bar">
                                    <div class="completion-fill" style="width: {{ $visit->record_completion }}%;"></div>
                                </div>
                            </div>

                            <div class="action-stack">
                                <a href="/therapist/visits/{{ $visit->id }}/medical-record" class="record-link">Continue Record</a>
                                <a href="/therapist/visits/{{ $visit->id }}/report" class="report-link">Preview Report</a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">Semua rekam medis utama sudah terlihat lengkap.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="section-card">
            <div class="section-head">
                <div>
                    <h2 class="section-title">All Assigned Visits</h2>
                    <p class="section-subtitle">Daftar lengkap visit yang menjadi tanggung jawab physiotherapist ini.</p>
                </div>
            </div>

            @if($visits->count() > 0)
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Visit Date</th>
                                <th>Visit Status</th>
                                <th>Record</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visits as $visit)
                                <tr>
                                    <td>
                                        <div class="patient-name">{{ optional($visit->patient)->full_name ?: '-' }}</div>
                                        <div class="patient-sub">Visit ID #{{ $visit->id }}</div>
                                    </td>

                                    <td>{{ $visit->visit_date ?: '-' }}</td>

                                    <td>
                                        <span class="status-pill status-{{ $visit->status }}">
                                            {{ str_replace('_', ' ', $visit->status ?: '-') }}
                                        </span>
                                    </td>

                                    <td>
                                        <div style="font-weight:900;color:#0f766e;">{{ $visit->record_completion }}%</div>
                                        <div class="completion-wrap">
                                            <div class="completion-bar">
                                                <div class="completion-fill" style="width: {{ $visit->record_completion }}%;"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="notes-box">{{ $visit->notes ?: '-' }}</div>
                                    </td>

                                    <td>
                                        <div class="action-stack">
                                            <a href="/therapist/visits/{{ $visit->id }}/medical-record" class="record-link">Medical Record</a>
                                            <a href="/therapist/visits/{{ $visit->id }}/report" class="report-link">Report</a>
                                            <a href="/therapist/visits/{{ $visit->id }}/report/print" target="_blank" class="print-link">Print</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    Belum ada visit yang ditugaskan kepada physiotherapist ini.
                </div>
            @endif
        </section>
    </div>
</div>
<script>
(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }

    var deferredInstallPrompt = null;

    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault();
        deferredInstallPrompt = event;

        if (document.getElementById('khayraInstallAppButton')) {
            return;
        }

        var button = document.createElement('button');
        button.id = 'khayraInstallAppButton';
        button.type = 'button';
        button.innerText = 'Install App';
        button.style.position = 'fixed';
        button.style.right = '18px';
        button.style.bottom = '18px';
        button.style.zIndex = '99999';
        button.style.border = '0';
        button.style.borderRadius = '999px';
        button.style.padding = '12px 16px';
        button.style.background = '#2f7c7a';
        button.style.color = '#ffffff';
        button.style.fontWeight = '900';
        button.style.fontFamily = 'Arial, sans-serif';
        button.style.fontSize = '13px';
        button.style.boxShadow = '0 14px 30px rgba(47,124,122,.24)';
        button.style.cursor = 'pointer';

        button.addEventListener('click', function () {
            if (!deferredInstallPrompt) {
                return;
            }

            deferredInstallPrompt.prompt();
            deferredInstallPrompt.userChoice.finally(function () {
                deferredInstallPrompt = null;
                button.remove();
            });
        });

        document.body.appendChild(button);
    });
})();
</script>

</body>
</html>
