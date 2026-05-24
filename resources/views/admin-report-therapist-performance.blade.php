<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Therapist Performance Report - Khayra Physio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(47, 143, 138, .14), transparent 34%),
                radial-gradient(circle at top right, rgba(124, 58, 237, .09), transparent 28%),
                #eef7f5;
            color: #20343a;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .main {
            flex: 1;
            padding: 28px;
            margin-left: 260px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 22px;
        }

        .page-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(47, 143, 138, .10);
            color: #2f7c7a;
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .page-title {
            margin: 0;
            color: #172f35;
            font-size: 34px;
            line-height: 1.08;
            letter-spacing: -.04em;
            font-weight: 950;
        }

        .page-subtitle {
            margin: 10px 0 0;
            color: #60737b;
            line-height: 1.7;
            max-width: 820px;
            font-size: 14px;
            font-weight: 650;
        }

        .top-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .soft-btn {
            border: 1px solid #dbecea;
            background: rgba(255,255,255,.88);
            color: #2f7c7a;
            font-weight: 900;
            font-size: 13px;
            padding: 12px 16px;
            border-radius: 16px;
            box-shadow: 0 10px 28px rgba(31,79,77,.06);
        }

        .hero {
            border: 1px solid rgba(219, 236, 234, .95);
            background:
                linear-gradient(135deg, rgba(255,255,255,.96), rgba(255,255,255,.86)),
                radial-gradient(circle at top right, rgba(47,143,138,.16), transparent 36%);
            border-radius: 30px;
            padding: 24px;
            box-shadow: 0 24px 70px rgba(31,79,77,.10);
            margin-bottom: 20px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .summary-card {
            border: 1px solid #dbecea;
            background: #ffffff;
            border-radius: 22px;
            padding: 18px;
            min-height: 128px;
            box-shadow: 0 12px 30px rgba(31,79,77,.05);
        }

        .summary-label {
            color: #78909a;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: 11px;
            font-weight: 950;
        }

        .summary-value {
            margin-top: 12px;
            color: #172f35;
            font-size: 30px;
            line-height: 1;
            letter-spacing: -.04em;
            font-weight: 950;
        }

        .summary-note {
            margin-top: 10px;
            color: #60737b;
            font-size: 12px;
            line-height: 1.55;
            font-weight: 700;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.25fr .75fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .panel {
            border: 1px solid #dbecea;
            background: rgba(255,255,255,.94);
            border-radius: 26px;
            padding: 20px;
            box-shadow: 0 18px 45px rgba(31,79,77,.08);
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .panel-title {
            margin: 0;
            color: #1f4f4d;
            font-size: 20px;
            font-weight: 950;
            letter-spacing: -.025em;
        }

        .panel-subtitle {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
            font-weight: 750;
        }

        .mini-rank {
            display: grid;
            gap: 12px;
        }

        .rank-row {
            display: grid;
            grid-template-columns: 46px 1fr auto;
            gap: 12px;
            align-items: center;
            padding: 13px;
            border: 1px solid #e3efed;
            border-radius: 18px;
            background: #ffffff;
        }

        .rank-no {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: #2f7c7a;
            background: #eafffb;
            font-weight: 950;
        }

        .rank-name {
            color: #20343a;
            font-weight: 950;
            font-size: 13px;
        }

        .rank-meta {
            margin-top: 3px;
            color: #64748b;
            font-size: 11px;
            font-weight: 750;
        }

        .rank-score {
            color: #172f35;
            font-weight: 950;
            font-size: 13px;
        }

        .table-card {
            border: 1px solid #dbecea;
            background: rgba(255,255,255,.96);
            border-radius: 28px;
            padding: 20px;
            box-shadow: 0 18px 45px rgba(31,79,77,.08);
            overflow: hidden;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1120px;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        th {
            text-align: left;
            color: #78909a;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: 10px;
            font-weight: 950;
            padding: 0 12px 6px;
            white-space: nowrap;
        }

        td {
            background: #ffffff;
            border-top: 1px solid #e1eeec;
            border-bottom: 1px solid #e1eeec;
            padding: 14px 12px;
            vertical-align: middle;
            color: #20343a;
            font-size: 13px;
            font-weight: 750;
        }

        td:first-child {
            border-left: 1px solid #e1eeec;
            border-top-left-radius: 18px;
            border-bottom-left-radius: 18px;
        }

        td:last-child {
            border-right: 1px solid #e1eeec;
            border-top-right-radius: 18px;
            border-bottom-right-radius: 18px;
        }

        .therapist-name {
            color: #172f35;
            font-weight: 950;
            font-size: 14px;
        }

        .therapist-sub {
            margin-top: 3px;
            color: #64748b;
            font-size: 11px;
            font-weight: 750;
        }

        .metric-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 46px;
            padding: 7px 10px;
            border-radius: 999px;
            color: #2f7c7a;
            background: #ecfffb;
            border: 1px solid #c9f1eb;
            font-weight: 950;
            font-size: 12px;
        }

        .money {
            color: #172f35;
            font-weight: 950;
            white-space: nowrap;
        }

        .completion-box {
            min-width: 150px;
        }

        .completion-head {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-size: 11px;
            font-weight: 950;
            color: #20343a;
            margin-bottom: 7px;
        }

        .completion-track {
            height: 10px;
            border-radius: 999px;
            background: #edf3f5;
            overflow: hidden;
        }

        .completion-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #2f8f8a, #22c55e);
        }

        .quality-badge {
            display: inline-flex;
            border-radius: 999px;
            padding: 6px 9px;
            font-size: 11px;
            font-weight: 950;
            border: 1px solid #dbecea;
            background: #f8fffd;
            color: #2f7c7a;
        }

        .quality-badge.warn {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
        }

        .quality-badge.bad {
            background: #fff1f2;
            border-color: #fecdd3;
            color: #be123c;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .main {
                margin-left: 0;
                padding: 0;
            }

            .top-actions,
            aside,
            .sidebar {
                display: none !important;
            }

            .panel,
            .hero,
            .table-card {
                box-shadow: none;
                break-inside: avoid;
            }
        }

        @media (max-width: 1100px) {
            .main {
                margin-left: 0;
                padding: 18px;
            }

            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .topbar {
                display: block;
            }

            .top-actions {
                justify-content: flex-start;
                margin-top: 14px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 28px;
            }
        }
    
        /* 5E Owner Dashboard UI Polish */
        .layout {
            display: block !important;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 260px !important;
            min-width: 260px !important;
            max-width: 260px !important;
            flex: 0 0 260px !important;
            z-index: 20 !important;
            overflow-y: auto !important;
        }

        .main {
            margin-left: 260px !important;
            width: calc(100% - 260px) !important;
            padding: 24px 36px 42px !important;
        }

        .topbar {
            max-width: 1280px;
            margin: 0 auto 22px;
        }

        .hero,
        .dashboard-grid,
        .table-card {
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero {
            padding: 22px;
            border-radius: 28px;
        }

        .summary-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .summary-card {
            min-height: 118px;
            padding: 18px;
        }

        .summary-value {
            font-size: 28px;
        }

        .dashboard-grid {
            grid-template-columns: minmax(0, 1.25fr) minmax(330px, .75fr);
            align-items: stretch;
        }

        .panel {
            padding: 20px;
            border-radius: 26px;
        }

        .rank-row {
            grid-template-columns: 40px minmax(0, 1fr) auto;
        }

        .table-card {
            border-radius: 28px;
            padding: 22px;
        }

        table {
            min-width: 1180px;
            border-spacing: 0 12px;
        }

        th {
            padding: 0 10px 6px;
            font-size: 10px;
        }

        td {
            padding: 16px 10px;
        }

        .therapist-name {
            line-height: 1.15;
        }

        .metric-pill {
            min-width: 44px;
            padding: 7px 12px;
        }

        .completion-box {
            min-width: 170px;
        }

        .completion-track {
            height: 11px;
        }

        .quality-badge {
            white-space: nowrap;
        }

        @media (max-width: 1180px) {
            .sidebar {
                position: relative !important;
                width: 100% !important;
                max-width: none !important;
                min-width: 0 !important;
                height: auto !important;
                bottom: auto !important;
            }

            .main {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 20px !important;
            }

            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .main {
                padding: 16px !important;
            }

            .topbar {
                display: block;
            }

            .top-actions {
                justify-content: flex-start;
                margin-top: 14px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 28px;
            }

            .table-card {
                padding: 16px;
            }
        }


    
        /* 5E Final Top Layout Fix */
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important;
            min-height: 100vh !important;
            overflow-x: hidden !important;
        }

        body {
            display: block !important;
            align-items: unset !important;
            justify-content: unset !important;
        }

        .layout {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            min-height: 100vh !important;
            align-items: unset !important;
            justify-content: unset !important;
        }

        .main {
            margin-left: 260px !important;
            margin-top: 0 !important;
            padding: 24px 36px 42px !important;
            width: calc(100% - 260px) !important;
            min-height: 100vh !important;
            transform: none !important;
            position: relative !important;
            top: auto !important;
        }

        .topbar {
            margin: 0 auto 22px !important;
            padding: 0 !important;
            transform: none !important;
            position: relative !important;
            top: auto !important;
        }

        .page-kicker,
        .page-title,
        .page-subtitle {
            margin-top: 0 !important;
        }

        .hero {
            margin-top: 0 !important;
        }

        @media (max-width: 1180px) {
            .main {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 18px !important;
            }
        }

    
        /* 5E Standard Admin Layout Hard Fix */
        aside,
        .sidebar,
        .admin-sidebar,
        .side-nav,
        .admin-side,
        nav.sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            width: 260px !important;
            min-width: 260px !important;
            max-width: 260px !important;
            height: 100vh !important;
            z-index: 50 !important;
        }

        .main {
            display: block !important;
            position: relative !important;
            top: 0 !important;
            left: auto !important;
            transform: none !important;
            margin: 0 0 0 260px !important;
            padding: 24px 32px 42px !important;
            width: calc(100vw - 260px) !important;
            max-width: none !important;
            min-height: 100vh !important;
        }

        .topbar {
            margin-top: 0 !important;
            transform: none !important;
        }

        .hero,
        .dashboard-grid,
        .table-card,
        .topbar {
            max-width: 1320px !important;
        }

        @media (max-width: 1180px) {
            aside,
            .sidebar,
            .admin-sidebar,
            .side-nav,
            .admin-side,
            nav.sidebar {
                position: relative !important;
                width: 100% !important;
                max-width: none !important;
                min-width: 0 !important;
                height: auto !important;
            }

            .main {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 18px !important;
            }
        }

    </style>
</head>
<body class="therapist-performance-report-page">
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'reports'])

    <main class="main">
        <div class="topbar">
            <div>
                <div class="page-kicker">Owner Dashboard</div>
                <h1 class="page-title">Therapist Performance Report</h1>
                <p class="page-subtitle">
                    Dashboard owner untuk memantau beban pasien, program terapi, revenue, dan kualitas dokumentasi klinis per fisioterapis.
                </p>
            </div>

            <div class="top-actions">
                <a href="/admin/reports" class="soft-btn">← Back to Reports</a>
                <button type="button" class="soft-btn" onclick="window.print()">Print / Save PDF</button>
            </div>
        </div>

        <section class="hero">
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-label">Therapists</div>
                    <div class="summary-value">{{ $therapistPerformanceSummary->therapist_count }}</div>
                    <div class="summary-note">Total fisioterapis aktif dalam report.</div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Patients Handled</div>
                    <div class="summary-value">{{ $therapistPerformanceSummary->patient_count }}</div>
                    <div class="summary-note">Unique patient dari visit yang terhubung ke therapist.</div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Programs</div>
                    <div class="summary-value">{{ $therapistPerformanceSummary->program_count }}</div>
                    <div class="summary-note">Medical record yang sudah punya program patient.</div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Completion Rate</div>
                    <div class="summary-value">{{ $therapistPerformanceSummary->completion_rate }}%</div>
                    <div class="summary-note">Rekam medis lengkap dibanding total rekam medis.</div>
                </div>
            </div>
        </section>

        <div class="dashboard-grid">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h2 class="panel-title">Top Therapist by Patients</h2>
                        <p class="panel-subtitle">Fisioterapis dengan jumlah pasien terbanyak dari data visit.</p>
                    </div>
                </div>

                <div class="mini-rank">
                    @forelse($topTherapistsByPatients as $index => $row)
                        <div class="rank-row">
                            <div class="rank-no">{{ $index + 1 }}</div>
                            <div>
                                <div class="rank-name">{{ $row->therapist->full_name }}</div>
                                <div class="rank-meta">{{ $row->visit_count }} visits · {{ $row->program_count }} programs</div>
                            </div>
                            <div class="rank-score">{{ $row->patient_count }} patients</div>
                        </div>
                    @empty
                        <div class="rank-row">
                            <div class="rank-no">-</div>
                            <div>
                                <div class="rank-name">Belum ada data</div>
                                <div class="rank-meta">Data visit belum tersedia.</div>
                            </div>
                            <div class="rank-score">-</div>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h2 class="panel-title">Clinical Quality</h2>
                        <p class="panel-subtitle">Completion rate dan rata-rata kelengkapan dokumentasi.</p>
                    </div>
                </div>

                <div class="summary-grid" style="grid-template-columns:1fr;">
                    <div class="summary-card">
                        <div class="summary-label">Average Completeness</div>
                        <div class="summary-value">{{ $therapistPerformanceSummary->average_completeness }}%</div>
                        <div class="summary-note">Rata-rata kelengkapan field klinis utama.</div>
                    </div>

                    <div class="summary-card">
                        <div class="summary-label">Package Patients</div>
                        <div class="summary-value">{{ $therapistPerformanceSummary->package_patient_count }}</div>
                        <div class="summary-note">Akan terisi jika data paket terhubung ke therapist.</div>
                    </div>
                </div>
            </section>
        </div>

        <section class="table-card">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Therapist Performance Detail</h2>
                    <p class="panel-subtitle">
                        Ringkasan operasional per fisioterapis: pasien, visit, program, paket, revenue, dan kualitas rekam medis.
                    </p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Therapist</th>
                            <th>Patients</th>
                            <th>Visits</th>
                            <th>Bookings</th>
                            <th>Programs</th>
                            <th>Package Patients</th>
                            <th>Revenue</th>
                            <th>Paid</th>
                            <th>Outstanding</th>
                            <th>Medical Records</th>
                            <th>Completion</th>
                            <th>Quality</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($therapistPerformanceRows as $row)
                            @php
                                $qualityClass = $row->completion_rate >= 80 ? '' : ($row->completion_rate >= 50 ? 'warn' : 'bad');
                                $qualityText = $row->completion_rate >= 80 ? 'Good' : ($row->completion_rate >= 50 ? 'Needs Review' : 'Incomplete');
                            @endphp

                            <tr>
                                <td>
                                    <div class="therapist-name">{{ $row->therapist->full_name }}</div>
                                    <div class="therapist-sub">{{ $row->therapist->specialty ?? 'Therapist' }}</div>
                                </td>
                                <td><span class="metric-pill">{{ $row->patient_count }}</span></td>
                                <td><span class="metric-pill">{{ $row->visit_count }}</span></td>
                                <td><span class="metric-pill">{{ $row->booking_count }}</span></td>
                                <td><span class="metric-pill">{{ $row->program_count }}</span></td>
                                <td><span class="metric-pill">{{ $row->package_patient_count }}</span></td>
                                <td><div class="money">Rp {{ number_format($row->revenue, 0, ',', '.') }}</div></td>
                                <td><div class="money">Rp {{ number_format($row->paid_amount, 0, ',', '.') }}</div></td>
                                <td><div class="money">Rp {{ number_format($row->outstanding_amount, 0, ',', '.') }}</div></td>
                                <td>{{ $row->completed_record_count }} / {{ $row->medical_record_count }}</td>
                                <td>
                                    <div class="completion-box">
                                        <div class="completion-head">
                                            <span>{{ $row->completion_rate }}%</span>
                                            <span>Avg {{ $row->average_completeness }}%</span>
                                        </div>
                                        <div class="completion-track">
                                            <div class="completion-fill" style="width: {{ min(max((int) $row->completion_rate, 0), 100) }}%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="quality-badge {{ $qualityClass }}">{{ $qualityText }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12">Belum ada data therapist.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<script>
    window.history.scrollRestoration = 'manual';
    window.addEventListener('load', function () {
        window.scrollTo(0, 0);
    });
</script>

</body>
</html>
