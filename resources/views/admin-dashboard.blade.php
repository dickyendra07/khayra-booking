<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Khayra Physio</title>
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

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-wrap img {
            width: 46px;
            height: 46px;
            object-fit: contain;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #e6ebea;
            padding: 4px;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .brand-kicker {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: #7b8794;
        }

        .brand-title {
            font-size: 18px;
            font-weight: 800;
            color: #22343a;
        }

        .top-actions {
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
            grid-template-columns: 1.18fr .82fr;
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
            font-size: 44px;
            line-height: 1.05;
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
            box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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
            backdrop-filter: blur(4px);
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
            padding: 22px;
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
            font-size: 38px;
            line-height: 1;
            font-weight: 800;
            color: #22343a;
        }

        .stat-sub {
            margin-top: 8px;
            font-size: 12px;
            line-height: 1.75;
            color: #94a3b8;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
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

        .chart-grid {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 18px;
            align-items: center;
        }

        .donut-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
        }

        .donut {
            width: 190px;
            height: 190px;
            border-radius: 50%;
            position: relative;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.4);
        }

        .donut::after {
            content: "";
            position: absolute;
            inset: 28px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #edf1f0;
        }

        .donut-center {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
            flex-direction: column;
            text-align: center;
        }

        .donut-total {
            font-size: 34px;
            font-weight: 800;
            color: #22343a;
            line-height: 1;
        }

        .donut-label {
            margin-top: 6px;
            font-size: 12px;
            color: #7b8794;
            font-weight: 700;
        }

        .legend {
            display: grid;
            gap: 10px;
            width: 100%;
        }

        .legend-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            border: 1px solid #edf1f0;
            border-radius: 14px;
            background: #fafcfc;
        }

        .legend-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            flex: 0 0 auto;
        }

        .legend-name {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        .legend-value {
            font-size: 13px;
            font-weight: 800;
            color: #22343a;
        }

        .activity-stack {
            display: grid;
            gap: 16px;
        }

        .activity-item {
            display: grid;
            grid-template-columns: 110px 1fr 48px;
            gap: 12px;
            align-items: center;
        }

        .activity-name {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        .bar-track {
            height: 12px;
            border-radius: 999px;
            background: #eef3f2;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(135deg, #4a8b8a 0%, #2f7c7a 100%);
        }

        .activity-value {
            text-align: right;
            font-size: 13px;
            font-weight: 800;
            color: #22343a;
        }

        .spark-card {
            background: linear-gradient(180deg, #fbfcfc 0%, #f7faf9 100%);
            border: 1px solid #edf1f0;
            border-radius: 20px;
            padding: 18px;
        }

        .spark-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }

        .spark-title {
            font-size: 15px;
            font-weight: 800;
            color: #294047;
        }

        .spark-note {
            margin-top: 6px;
            font-size: 12px;
            color: #7b8794;
            line-height: 1.7;
        }

        .spark-total {
            font-size: 34px;
            font-weight: 800;
            color: #2f7c7a;
            line-height: 1;
        }

        .spark-svg {
            width: 100%;
            height: 180px;
            display: block;
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
        }

        .quick-link {
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-decoration: none;
            padding: 20px 18px;
            border-radius: 18px;
            border: 1px solid #ecefef;
            background: #ffffff;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
            color: #22343a;
            transition: all .18s ease;
        }

        .quick-link:hover {
            transform: translateY(-3px);
            border-color: #d8e7e4;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
        }

        .quick-kicker {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .45px;
            color: #7b8794;
            font-weight: 700;
        }

        .quick-title {
            font-size: 18px;
            font-weight: 800;
            color: #294047;
        }

        .tables-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
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
            min-width: 420px;
        }

        th {
            text-align: left;
            padding: 14px 15px;
            background: #f7faf9;
            color: #486168;
            font-size: 12px;
            font-weight: 800;
            border-bottom: 1px solid #edf1f0;
        }

        td {
            padding: 14px 15px;
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

        .status-pill {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #dbeafe; color: #1d4ed8; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        .status-scheduled { background: #dbeafe; color: #1d4ed8; }
        .status-in_progress { background: #fef3c7; color: #92400e; }

        .billing-paid { background: #dcfce7; color: #166534; }
        .billing-unpaid { background: #fee2e2; color: #b91c1c; }
        .billing-partial { background: #fef3c7; color: #92400e; }

        @media (max-width: 1280px) {
            .quick-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 1080px) {
            .hero-grid,
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .chart-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid,
            .tables-grid {
                grid-template-columns: 1fr 1fr;
            }

            .quick-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 820px) {
            .layout {
                display: block;
            }

            .main {
                padding: 16px;
            }

            .quick-grid,
            .stats-grid,
            .tables-grid {
                grid-template-columns: 1fr 1fr;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 640px) {
            .hero,
            .section-card,
            .stat-card {
                padding: 20px;
                border-radius: 22px;
            }

            .hero-title {
                font-size: 32px;
            }

            .stats-grid,
            .quick-grid,
            .tables-grid {
                grid-template-columns: 1fr;
            }

            .brand-title {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
@php
    $safeTotalBillings = max($totalBillings, 1);
    $paidPct = round(($paidBillings / $safeTotalBillings) * 100, 1);
    $unpaidPct = round(($unpaidBillings / $safeTotalBillings) * 100, 1);
    $partialPct = round(($partialBillings / $safeTotalBillings) * 100, 1);

    $donutStyle = "background: conic-gradient(
        #3f8b89 0% {$paidPct}%,
        #eab308 {$paidPct}% " . ($paidPct + $partialPct) . "%,
        #ef4444 " . ($paidPct + $partialPct) . "% 100%
    );";

    $activityData = [
        ['label' => 'Bookings', 'value' => $totalBookings],
        ['label' => 'Patients', 'value' => $totalPatients],
        ['label' => 'Visits', 'value' => $totalVisits],
        ['label' => 'Therapists', 'value' => $totalTherapists],
        ['label' => 'Billings', 'value' => $totalBillings],
    ];

    $activityMax = max(array_column($activityData, 'value'));
    $activityMax = $activityMax > 0 ? $activityMax : 1;

    $sparkValues = [
        max($totalBookings, 1),
        max($totalPatients, 1),
        max($totalVisits, 1),
        max($totalTherapists, 1),
        max($totalBillings, 1),
        max($paidBillings, 1),
    ];

    $sparkMax = max($sparkValues);
    $sparkPoints = [];
    $pointCount = count($sparkValues);
    $chartWidth = 520;
    $chartHeight = 180;
    $paddingX = 20;
    $paddingY = 20;

    foreach ($sparkValues as $i => $value) {
        $x = $paddingX + (($chartWidth - ($paddingX * 2)) / max($pointCount - 1, 1)) * $i;
        $normalized = $value / max($sparkMax, 1);
        $y = $chartHeight - $paddingY - (($chartHeight - ($paddingY * 2)) * $normalized);
        $sparkPoints[] = round($x, 2) . ',' . round($y, 2);
    }

    $sparkPolyline = implode(' ', $sparkPoints);
    $sparkArea = '20,160 ' . $sparkPolyline . ' 500,160';
@endphp

<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'dashboard'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div class="brand-wrap">
                    <img src="/images/khayra-logo.png" alt="Khayra Logo">
                    <div class="brand-text">
                        <div class="brand-kicker">Clinic Admin Workspace</div>
                        <div class="brand-title">Khayra Physio Dashboard</div>
                    </div>
                </div>

                <div class="top-actions">
                    <a href="/admin/bookings" class="ghost-link">Bookings</a>
                    <a href="/admin/patients" class="ghost-link">Patients</a>
                    <a href="/admin/billings" class="ghost-link">Billings</a>
                </div>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <div class="hero-badge">Live Clinic Overview</div>
                        <h1 class="hero-title">Khayra Physio internal operations in one refined dashboard.</h1>
                        <p class="hero-text">
                            Pantau booking, patient, visit, therapist, dan billing dari satu tampilan yang lebih rapi,
                            premium, dan mudah dibaca. Dashboard ini dirancang untuk membantu admin melihat kondisi
                            operasional klinik tanpa harus membuka semua halaman satu per satu.
                        </p>

                        <div class="hero-tags">
                            <span class="hero-tag">Bookings</span>
                            <span class="hero-tag">Patients</span>
                            <span class="hero-tag">Visits</span>
                            <span class="hero-tag">Therapists</span>
                            <span class="hero-tag">Billings</span>
                        </div>
                    </div>

                    <div class="hero-side">
                        <div>
                            <h3>System Snapshot</h3>
                            <p>Ringkasan cepat agar admin bisa membaca kondisi sistem secara instan.</p>
                        </div>

                        <div class="snapshot-grid">
                            <div class="snapshot-card">
                                <div class="snapshot-label">Latest Booking</div>
                                <div class="snapshot-value">{{ optional($recentBookings->first())->full_name ?: 'No booking yet' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Latest Visit</div>
                                <div class="snapshot-value">{{ optional($recentVisits->first())->visit_date ?: 'No visit yet' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Billing Status</div>
                                <div class="snapshot-value">{{ $paidBillings }} paid • {{ $unpaidBillings }} unpaid • {{ $partialBillings }} partial</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Bookings</div>
                    <div class="stat-value">{{ $totalBookings }}</div>
                    <div class="stat-sub">Semua request booking yang sudah masuk ke sistem.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total Patients</div>
                    <div class="stat-value">{{ $totalPatients }}</div>
                    <div class="stat-sub">Patient yang sudah tersimpan pada database klinik.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total Visits</div>
                    <div class="stat-value">{{ $totalVisits }}</div>
                    <div class="stat-sub">Visit terapi yang sudah tercatat sampai saat ini.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total Therapists</div>
                    <div class="stat-value">{{ $totalTherapists }}</div>
                    <div class="stat-sub">Therapist yang tercatat pada sistem admin.</div>
                </div>
            </section>

            <section class="dashboard-grid">
                <section class="section-card">
                    <h2 class="section-title">Billing Distribution</h2>
                    <p class="section-subtitle">Diagram ringkas untuk melihat proporsi status billing saat ini.</p>

                    <div class="chart-grid">
                        <div class="donut-wrap">
                            <div class="donut" style="{{ $donutStyle }}">
                                <div class="donut-center">
                                    <div class="donut-total">{{ $totalBillings }}</div>
                                    <div class="donut-label">Total Billings</div>
                                </div>
                            </div>
                        </div>

                        <div class="legend">
                            <div class="legend-item">
                                <div class="legend-left">
                                    <span class="legend-dot" style="background:#3f8b89;"></span>
                                    <span class="legend-name">Paid</span>
                                </div>
                                <span class="legend-value">{{ $paidBillings }} ({{ $paidPct }}%)</span>
                            </div>

                            <div class="legend-item">
                                <div class="legend-left">
                                    <span class="legend-dot" style="background:#eab308;"></span>
                                    <span class="legend-name">Partial</span>
                                </div>
                                <span class="legend-value">{{ $partialBillings }} ({{ $partialPct }}%)</span>
                            </div>

                            <div class="legend-item">
                                <div class="legend-left">
                                    <span class="legend-dot" style="background:#ef4444;"></span>
                                    <span class="legend-name">Unpaid</span>
                                </div>
                                <span class="legend-value">{{ $unpaidBillings }} ({{ $unpaidPct }}%)</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="section-card">
                    <h2 class="section-title">Quick Access</h2>
                    <p class="section-subtitle">Akses cepat ke modul yang paling sering dipakai admin.</p>

                    <div class="quick-grid">
                        <a href="/admin/bookings" class="quick-link">
                            <div class="quick-kicker">Module</div>
                            <div class="quick-title">Bookings</div>
                        </a>

                        <a href="/admin/patients" class="quick-link">
                            <div class="quick-kicker">Module</div>
                            <div class="quick-title">Patients</div>
                        </a>

                        <a href="/admin/visits" class="quick-link">
                            <div class="quick-kicker">Module</div>
                            <div class="quick-title">Visits</div>
                        </a>

                        <a href="/admin/therapists" class="quick-link">
                            <div class="quick-kicker">Module</div>
                            <div class="quick-title">Therapists</div>
                        </a>

                        <a href="/admin/billings" class="quick-link">
                            <div class="quick-kicker">Module</div>
                            <div class="quick-title">Billings</div>
                        </a>
                    </div>
                </section>
            </section>

            <section class="dashboard-grid">
                <section class="section-card">
                    <h2 class="section-title">Operations Trend</h2>
                    <p class="section-subtitle">Grafik ringkas berdasarkan total data utama yang tersedia saat ini.</p>

                    <div class="spark-card">
                        <div class="spark-top">
                            <div>
                                <div class="spark-title">Current System Composition</div>
                                <div class="spark-note">Visual ini membantu membaca distribusi volume data secara cepat.</div>
                            </div>
                            <div class="spark-total">{{ array_sum($sparkValues) }}</div>
                        </div>

                        <svg class="spark-svg" viewBox="0 0 520 180" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="areaFill" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="rgba(63,139,137,0.28)"/>
                                    <stop offset="100%" stop-color="rgba(63,139,137,0.02)"/>
                                </linearGradient>
                                <linearGradient id="lineStroke" x1="0" x2="1" y1="0" y2="0">
                                    <stop offset="0%" stop-color="#4a8b8a"/>
                                    <stop offset="100%" stop-color="#2f7c7a"/>
                                </linearGradient>
                            </defs>

                            <line x1="20" y1="160" x2="500" y2="160" stroke="#e8efee" stroke-width="1"/>
                            <line x1="20" y1="120" x2="500" y2="120" stroke="#f2f5f5" stroke-width="1"/>
                            <line x1="20" y1="80" x2="500" y2="80" stroke="#f2f5f5" stroke-width="1"/>
                            <line x1="20" y1="40" x2="500" y2="40" stroke="#f2f5f5" stroke-width="1"/>

                            <polygon points="{{ $sparkArea }}" fill="url(#areaFill)"></polygon>
                            <polyline points="{{ $sparkPolyline }}" fill="none" stroke="url(#lineStroke)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></polyline>

                            @foreach($sparkPoints as $point)
                                @php [$cx, $cy] = explode(',', $point); @endphp
                                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="5" fill="#ffffff" stroke="#2f7c7a" stroke-width="3"></circle>
                            @endforeach
                        </svg>
                    </div>
                </section>

                <section class="section-card">
                    <h2 class="section-title">Recent Bookings</h2>
                    <p class="section-subtitle">Booking terbaru yang masuk ke sistem.</p>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBookings as $booking)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ $booking->full_name ?: '-' }}</div>
                                            <div class="secondary-text">{{ $booking->whatsapp ?: '-' }}</div>
                                        </td>
                                        <td>{{ $booking->service ?: '-' }}</td>
                                        <td>
                                            <span class="status-pill status-{{ $booking->status }}">
                                                {{ $booking->status ?: '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">Belum ada data booking.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>

            <section class="tables-grid">
                <section class="section-card">
                    <h2 class="section-title">Recent Visits</h2>
                    <p class="section-subtitle">Visit terbaru yang tercatat.</p>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentVisits as $visit)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ optional($visit->patient)->full_name ?: '-' }}</div>
                                            <div class="secondary-text">{{ $visit->therapist ?: '-' }}</div>
                                        </td>
                                        <td>{{ $visit->visit_date ?: '-' }}</td>
                                        <td>
                                            <span class="status-pill status-{{ $visit->status }}">
                                                {{ str_replace('_', ' ', $visit->status ?: '-') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">Belum ada data visit.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="section-card">
                    <h2 class="section-title">Recent Billings</h2>
                    <p class="section-subtitle">Invoice terbaru dan status pembayarannya.</p>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Patient</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBillings as $billing)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ $billing->invoice_number ?: '-' }}</div>
                                            <div class="secondary-text">Rp {{ number_format($billing->amount, 0, ',', '.') }}</div>
                                        </td>
                                        <td>{{ optional($billing->patient)->full_name ?: '-' }}</td>
                                        <td>
                                            <span class="status-pill billing-{{ $billing->payment_status }}">
                                                {{ $billing->payment_status ?: '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">Belum ada data billing.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>
        </div>
    </main>
</div>
</body>
</html>