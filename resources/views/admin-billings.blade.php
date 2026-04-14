<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Billings - Khayra Physio</title>
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
            line-height: 1.5;
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
            grid-template-columns: 1.02fr .98fr;
            gap: 18px;
            margin-bottom: 18px;
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

        .chart-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 18px;
            align-items: center;
        }

        .donut-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .donut {
            width: 190px;
            height: 190px;
            border-radius: 50%;
            position: relative;
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

        .filter-grid {
            display: grid;
            grid-template-columns: 1.2fr .8fr auto;
            gap: 14px;
            align-items: end;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        input,
        select {
            width: 100%;
            height: 50px;
            padding: 0 14px;
            border: 1px solid #dde5e3;
            border-radius: 14px;
            font-size: 14px;
            background: #ffffff;
            color: #111827;
            transition: .2s ease;
            font-family: Arial, sans-serif;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #176f69;
            box-shadow: 0 0 0 4px rgba(23,111,105,.08);
        }

        .filter-btn {
            height: 50px;
            padding: 0 22px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #0f172a 0%, #1f2d3d 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
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
            min-width: 1150px;
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

        .amount-text {
            font-weight: 800;
            color: #2f7c7a;
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

        .billing-paid { background: #dcfce7; color: #166534; }
        .billing-unpaid { background: #fee2e2; color: #b91c1c; }
        .billing-partial { background: #fef3c7; color: #92400e; }

        .action-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .action-link,
        .mini-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 9px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 800;
            border: 1px solid transparent;
            transition: all .18s ease;
            font-family: Arial, sans-serif;
            cursor: pointer;
        }

        .btn-detail {
            background: #eef7f5;
            color: #2f7c7a;
            border-color: #d8ebe7;
        }

        .btn-edit {
            background: #eef2ff;
            color: #3457d5;
            border-color: #dde5ff;
        }

        .btn-print {
            background: #f5f3ff;
            color: #6d28d9;
            border-color: #e9e3ff;
        }

        .btn-delete {
            background: #fff1f2;
            color: #be123c;
            border-color: #ffe0e6;
        }

        .action-link:hover,
        .mini-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
        }

        .status-form {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .status-form select {
            width: 150px;
            height: 40px;
            padding: 0 12px;
            border-radius: 12px;
            font-size: 12px;
        }

        .mini-submit {
            height: 40px;
            padding: 0 14px;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            border: none;
        }

        .empty-state {
            padding: 28px;
            border-radius: 18px;
            border: 1px dashed #d9e2e1;
            background: #fafcfc;
            text-align: center;
            color: #7b8794;
            font-size: 13px;
            line-height: 1.8;
        }

        @media (max-width: 1180px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 1080px) {
            .hero-grid,
            .dashboard-grid,
            .chart-grid,
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 820px) {
            .layout {
                display: block;
            }

            .main {
                padding: 16px;
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

            .stats-grid {
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
    $totalBillings = $billings->count();
    $paidCount = $billings->where('payment_status', 'paid')->count();
    $unpaidCount = $billings->where('payment_status', 'unpaid')->count();
    $partialCount = $billings->where('payment_status', 'partial')->count();

    $safeTotalBillings = max($totalBillings, 1);
    $paidPct = round(($paidCount / $safeTotalBillings) * 100, 1);
    $unpaidPct = round(($unpaidCount / $safeTotalBillings) * 100, 1);
    $partialPct = round(($partialCount / $safeTotalBillings) * 100, 1);

    $donutStyle = "background: conic-gradient(
        #3f8b89 0% {$paidPct}%,
        #eab308 {$paidPct}% " . ($paidPct + $partialPct) . "%,
        #ef4444 " . ($paidPct + $partialPct) . "% 100%
    );";

    $totalAmount = $billings->sum('amount');
    $paidAmount = $billings->where('payment_status', 'paid')->sum('amount');
    $partialAmount = $billings->where('payment_status', 'partial')->sum('amount');
    $unpaidAmount = $billings->where('payment_status', 'unpaid')->sum('amount');

    $sparkValues = [
        max($totalBillings, 1),
        max($paidCount, 1),
        max($partialCount, 1),
        max($unpaidCount, 1),
        max((int) round($paidAmount / 100000), 1),
        max((int) round($totalAmount / 100000), 1),
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
    @include('partials.admin-sidebar', ['activeMenu' => 'billings'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div class="brand-wrap">
                    <img src="/images/khayra-logo.png" alt="Khayra Logo">
                    <div class="brand-text">
                        <div class="brand-kicker">Clinic Billing Management</div>
                        <div class="brand-title">Khayra Billings List</div>
                    </div>
                </div>

                <div class="top-actions">
                    <a href="/admin/dashboard" class="ghost-link">Dashboard</a>
                    <a href="/admin/patients" class="ghost-link">Patients</a>
                </div>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <div class="hero-badge">Billing Overview</div>
                        <h1 class="hero-title">Monitor invoices and payment status with a clearer and more premium financial view.</h1>
                        <p class="hero-text">
                            Halaman ini membantu admin memantau invoice patient, melihat status pembayaran,
                            memperbarui data billing, dan mencetak invoice dengan tampilan yang lebih rapi,
                            profesional, dan mudah dibaca.
                        </p>

                        <div class="hero-tags">
                            <span class="hero-tag">Invoice Monitoring</span>
                            <span class="hero-tag">Payment Status</span>
                            <span class="hero-tag">Print Ready</span>
                            <span class="hero-tag">Admin Workflow</span>
                        </div>
                    </div>

                    <div class="hero-side">
                        <div>
                            <h3>Billing Snapshot</h3>
                            <p>Ringkasan cepat agar admin bisa membaca kondisi pembayaran sistem secara instan.</p>
                        </div>

                        <div class="snapshot-grid">
                            <div class="snapshot-card">
                                <div class="snapshot-label">Latest Invoice</div>
                                <div class="snapshot-value">{{ optional($billings->first())->invoice_number ?: 'No invoice yet' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Paid Amount</div>
                                <div class="snapshot-value">Rp {{ number_format($paidAmount, 0, ',', '.') }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Total Amount</div>
                                <div class="snapshot-value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Billings</div>
                    <div class="stat-value">{{ $totalBillings }}</div>
                    <div class="stat-sub">Semua invoice yang tercatat di sistem saat ini.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Paid</div>
                    <div class="stat-value">{{ $paidCount }}</div>
                    <div class="stat-sub">Invoice yang sudah tercatat lunas.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Partial</div>
                    <div class="stat-value">{{ $partialCount }}</div>
                    <div class="stat-sub">Invoice yang sudah dibayar sebagian.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Unpaid</div>
                    <div class="stat-value">{{ $unpaidCount }}</div>
                    <div class="stat-sub">Invoice yang masih menunggu pembayaran.</div>
                </div>
            </section>

            <section class="dashboard-grid">
                <section class="section-card">
                    <h2 class="section-title">Payment Distribution</h2>
                    <p class="section-subtitle">Diagram ringkas untuk membaca proporsi status pembayaran invoice.</p>

                    <div class="chart-grid">
                        <div class="donut-wrap">
                            <div class="donut" style="{{ $donutStyle }}">
                                <div class="donut-center">
                                    <div class="donut-total">{{ $totalBillings }}</div>
                                    <div class="donut-label">Total Invoices</div>
                                </div>
                            </div>
                        </div>

                        <div class="legend">
                            <div class="legend-item">
                                <div class="legend-left">
                                    <span class="legend-dot" style="background:#3f8b89;"></span>
                                    <span class="legend-name">Paid</span>
                                </div>
                                <span class="legend-value">{{ $paidCount }} ({{ $paidPct }}%)</span>
                            </div>

                            <div class="legend-item">
                                <div class="legend-left">
                                    <span class="legend-dot" style="background:#eab308;"></span>
                                    <span class="legend-name">Partial</span>
                                </div>
                                <span class="legend-value">{{ $partialCount }} ({{ $partialPct }}%)</span>
                            </div>

                            <div class="legend-item">
                                <div class="legend-left">
                                    <span class="legend-dot" style="background:#ef4444;"></span>
                                    <span class="legend-name">Unpaid</span>
                                </div>
                                <span class="legend-value">{{ $unpaidCount }} ({{ $unpaidPct }}%)</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="section-card">
                    <h2 class="section-title">Billing Value Trend</h2>
                    <p class="section-subtitle">Grafik ringkas untuk melihat komposisi jumlah dan nilai billing saat ini.</p>

                    <div class="spark-card">
                        <div class="spark-top">
                            <div>
                                <div class="spark-title">Current Billing Composition</div>
                                <div class="spark-note">Visual ini membantu membaca distribusi volume billing dan nilai pembayaran secara cepat.</div>
                            </div>
                            <div class="spark-total">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                        </div>

                        <svg class="spark-svg" viewBox="0 0 520 180" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="areaFillBillings" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="rgba(63,139,137,0.28)"/>
                                    <stop offset="100%" stop-color="rgba(63,139,137,0.02)"/>
                                </linearGradient>
                                <linearGradient id="lineStrokeBillings" x1="0" x2="1" y1="0" y2="0">
                                    <stop offset="0%" stop-color="#4a8b8a"/>
                                    <stop offset="100%" stop-color="#2f7c7a"/>
                                </linearGradient>
                            </defs>

                            <line x1="20" y1="160" x2="500" y2="160" stroke="#e8efee" stroke-width="1"/>
                            <line x1="20" y1="120" x2="500" y2="120" stroke="#f2f5f5" stroke-width="1"/>
                            <line x1="20" y1="80" x2="500" y2="80" stroke="#f2f5f5" stroke-width="1"/>
                            <line x1="20" y1="40" x2="500" y2="40" stroke="#f2f5f5" stroke-width="1"/>

                            <polygon points="{{ $sparkArea }}" fill="url(#areaFillBillings)"></polygon>
                            <polyline points="{{ $sparkPolyline }}" fill="none" stroke="url(#lineStrokeBillings)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></polyline>

                            @foreach($sparkPoints as $point)
                                @php [$cx, $cy] = explode(',', $point); @endphp
                                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="5" fill="#ffffff" stroke="#2f7c7a" stroke-width="3"></circle>
                            @endforeach
                        </svg>
                    </div>
                </section>
            </section>

            <section class="section-card">
                <h2 class="section-title">Filter Billings</h2>
                <p class="section-subtitle">Cari invoice berdasarkan nomor invoice, nama patient, atau status pembayaran.</p>

                <form method="GET" action="/admin/billings" class="filter-grid">
                    <div class="field">
                        <label>Search</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari invoice number atau nama patient"
                        >
                    </div>

                    <div class="field">
                        <label>Status</label>
                        <select name="status">
                            <option value="">Semua Status</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        </select>
                    </div>

                    <div class="field">
                        <button type="submit" class="filter-btn">Terapkan Filter</button>
                    </div>
                </form>
            </section>

            <section class="section-card">
                <h2 class="section-title">Billing List</h2>
                <p class="section-subtitle">Daftar invoice patient beserta status pembayaran, nominal, dan aksi admin.</p>

                @if($billings->count() > 0)
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Patient</th>
                                    <th>Visit</th>
                                    <th>Invoice Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment Method</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($billings as $billing)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ $billing->invoice_number ?: '-' }}</div>
                                            <div class="secondary-text">Billing ID #{{ $billing->id }}</div>
                                        </td>
                                        <td>
                                            <div class="primary-text">{{ optional($billing->patient)->full_name ?: '-' }}</div>
                                        </td>
                                        <td>
                                            @if($billing->visit)
                                                <div class="primary-text">#{{ $billing->visit->id }}</div>
                                                <div class="secondary-text">{{ $billing->visit->visit_date ?: 'No visit date' }}</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $billing->invoice_date ?: '-' }}</td>
                                        <td>
                                            <div class="amount-text">Rp {{ number_format($billing->amount, 0, ',', '.') }}</div>
                                        </td>
                                        <td>
                                            <span class="status-pill billing-{{ $billing->payment_status }}">
                                                {{ $billing->payment_status ?: '-' }}
                                            </span>
                                        </td>
                                        <td>{{ $billing->payment_method ?: '-' }}</td>
                                        <td>
                                            <div class="action-stack">
                                                <a href="/admin/billings/{{ $billing->id }}" class="action-link btn-detail">Detail</a>
                                                <a href="/admin/billings/{{ $billing->id }}/edit" class="action-link btn-edit">Edit</a>
                                                <a href="/admin/billings/{{ $billing->id }}/print" class="action-link btn-print" target="_blank">Print</a>

                                                <form method="POST" action="/admin/billings/{{ $billing->id }}/delete" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="action-link btn-delete" onclick="return confirm('Hapus billing ini?')" style="background:#fff1f2;">Delete</button>
                                                </form>
                                            </div>

                                            <form method="POST" action="/admin/billings/{{ $billing->id }}/status" class="status-form">
                                                @csrf
                                                <select name="payment_status">
                                                    <option value="paid" {{ $billing->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                                    <option value="unpaid" {{ $billing->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                                    <option value="partial" {{ $billing->payment_status == 'partial' ? 'selected' : '' }}>Partial</option>
                                                </select>
                                                <button type="submit" class="mini-submit">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        Belum ada data billing yang cocok dengan filter saat ini.
                    </div>
                @endif
            </section>
        </div>
    </main>
</div>
</body>
</html>