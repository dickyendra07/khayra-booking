<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapist Dashboard - Khayra Physio</title>
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
            max-width: 1280px;
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
            font-weight: 800;
            color: #0f766e;
        }

        .topbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ghost-link,
        .logout-btn {
            display: inline-block;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
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
        }

        .hero {
            display: grid;
            grid-template-columns: 1.35fr .85fr;
            gap: 18px;
            margin-bottom: 20px;
        }

        .hero-main,
        .hero-side {
            border-radius: 28px;
            padding: 26px;
            box-shadow: 0 18px 42px rgba(15,118,110,.08);
        }

        .hero-main {
            background: linear-gradient(135deg, #0f766e 0%, #2f7f74 100%);
            color: white;
        }

        .hero-side {
            background: white;
            border: 1px solid #e5f1ee;
        }

        .hero-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.16);
            color: white;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .hero-title {
            margin: 0;
            font-size: 40px;
            line-height: 1.08;
            font-weight: 800;
        }

        .hero-text {
            margin: 14px 0 0;
            line-height: 1.8;
            font-size: 15px;
            color: rgba(255,255,255,.92);
            max-width: 720px;
        }

        .hero-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .hero-tag {
            display: inline-block;
            padding: 9px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.18);
            color: white;
            font-size: 13px;
            font-weight: 700;
        }

        .side-title {
            margin: 0;
            font-size: 22px;
            color: #0f766e;
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
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 6px;
        }

        .mini-value {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            line-height: 1.6;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0,1fr));
            gap: 18px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 14px 35px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .stat-label {
            font-size: 13px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .stat-value {
            font-size: 34px;
            font-weight: 800;
            color: #0f766e;
            margin-top: 10px;
        }

        .stat-sub {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 8px;
            line-height: 1.6;
        }

        .section-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .section-title {
            margin: 0;
            font-size: 24px;
            color: #0f766e;
        }

        .section-subtitle {
            margin: 8px 0 18px;
            color: #6b7280;
            font-size: 14px;
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
            min-width: 1280px;
        }

        th {
            background: #effaf7;
            color: #0f766e;
            text-align: left;
            padding: 16px;
            font-size: 14px;
            border-bottom: 1px solid #e5efec;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #eef2f1;
            font-size: 14px;
            color: #374151;
            vertical-align: top;
        }

        tr:hover td {
            background: #fafefd;
        }

        .patient-name {
            font-weight: 800;
            font-size: 15px;
            color: #111827;
        }

        .patient-sub {
            margin-top: 4px;
            font-size: 12px;
            color: #94a3b8;
        }

        .date-main {
            font-weight: 800;
            color: #111827;
        }

        .notes-box {
            max-width: 240px;
            line-height: 1.7;
            color: #4b5563;
        }

        .status-pill {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            text-transform: capitalize;
        }

        .status-scheduled { background: #dbeafe; color: #1d4ed8; }
        .status-in_progress { background: #fef3c7; color: #92400e; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        .action-stack {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .record-link,
        .report-link {
            display: inline-block;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .record-link {
            background: #f0fdfa;
            color: #0f766e;
            border: 1px solid #cfe8e2;
        }

        .report-link {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #cfe0ff;
        }

        .empty-state {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            padding: 28px;
            color: #64748b;
            text-align: center;
            line-height: 1.7;
        }

        @media (max-width: 1180px) {
            .hero {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 980px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .page {
                padding: 16px 14px 32px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero-title {
                font-size: 30px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .hero-main,
            .hero-side,
            .section-card,
            .stat-card {
                padding: 20px;
                border-radius: 22px;
            }

            .brand {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="container">
            <div class="topbar">
                <div class="brand">Khayra Therapist Dashboard</div>

                <div class="topbar-actions">
                    <a href="/" class="ghost-link">Home</a>
                    <form method="POST" action="/therapist/logout" style="margin:0;">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>

            @php
                $latestVisit = $visits->first();
            @endphp

            <section class="hero">
                <div class="hero-main">
                    <div class="hero-badge">Therapist Overview</div>
                    <h1 class="hero-title">Halo, {{ session('therapist_name') }}. Dashboard visit Anda siap dipakai.</h1>
                    <p class="hero-text">
                        Gunakan halaman ini untuk memantau visit yang menjadi tanggung jawab Anda,
                        membuka medical record, dan melihat progress visit harian dengan lebih cepat.
                    </p>

                    <div class="hero-tags">
                        <span class="hero-tag">{{ $totalVisits }} total visits</span>
                        <span class="hero-tag">{{ $scheduledVisits }} scheduled</span>
                        <span class="hero-tag">{{ $inProgressVisits }} in progress</span>
                        <span class="hero-tag">{{ $completedVisits }} completed</span>
                    </div>
                </div>

                <div class="hero-side">
                    <h2 class="side-title">Ringkasan Terkini</h2>
                    <p class="side-subtitle">Informasi singkat therapist berdasarkan data visit terbaru.</p>

                    <div class="mini-grid">
                        <div class="mini-box">
                            <div class="mini-label">Therapist</div>
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
                    <div class="stat-sub">Semua visit yang menjadi tanggung jawab therapist ini.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Scheduled</div>
                    <div class="stat-value">{{ $scheduledVisits }}</div>
                    <div class="stat-sub">Visit yang sudah dijadwalkan.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">In Progress</div>
                    <div class="stat-value">{{ $inProgressVisits }}</div>
                    <div class="stat-sub">Visit yang sedang berjalan.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Completed</div>
                    <div class="stat-value">{{ $completedVisits }}</div>
                    <div class="stat-sub">Visit yang sudah selesai.</div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">My Visit List</h2>
                <p class="section-subtitle">
                    Buka medical record atau report dari tabel di bawah untuk melihat atau mengisi dokumentasi terapi.
                </p>

                @if($visits->count() > 0)
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Visit Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Medical Record</th>
                                    <th>Report</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($visits as $visit)
                                    <tr>
                                        <td>
                                            <div class="patient-name">{{ $visit->patient->full_name ?? '-' }}</div>
                                            <div class="patient-sub">Visit ID #{{ $visit->id }}</div>
                                        </td>

                                        <td>
                                            <div class="date-main">{{ $visit->visit_date ?: '-' }}</div>
                                        </td>

                                        <td>
                                            <span class="status-pill status-{{ $visit->status }}">
                                                {{ str_replace('_', ' ', $visit->status) }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="notes-box">{{ $visit->notes ?: '-' }}</div>
                                        </td>

                                        <td>
                                            <div class="action-stack">
                                                <a href="/therapist/visits/{{ $visit->id }}/medical-record" class="record-link">
                                                    {{ $visit->medicalRecord ? 'View / Edit Record' : 'Add Record' }}
                                                </a>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="action-stack">
                                                <a href="/therapist/visits/{{ $visit->id }}/report" class="report-link">Open Report</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        Belum ada visit yang di-assign ke Anda. Nanti visit akan muncul di dashboard ini setelah admin melakukan assignment.
                    </div>
                @endif
            </section>
        </div>
    </div>
</body>
</html>