<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard ERM Premium - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f8f8;
            color: #17232b;
        }

        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1420px; margin: 0 auto; }

        .hero {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 30px;
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
            font-size: 44px;
            line-height: 1.05;
            color: #22343a;
            font-weight: 900;
            max-width: 880px;
        }

        .subtitle {
            margin: 14px 0 0;
            max-width: 860px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.95;
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

        .hero-side > * { position: relative; z-index: 1; }
        .hero-side h3 { margin: 0 0 10px; color: #ffffff; font-size: 26px; line-height: 1.2; }
        .hero-side p { margin: 0; font-size: 13px; line-height: 1.85; color: rgba(255,255,255,.92); }

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
            font-size: 17px;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.45;
            word-break: break-word;
        }

        .action-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
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

        .btn-primary { background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: #ffffff; box-shadow: 0 12px 24px rgba(47,124,122,.16); }
        .btn-soft { color: #2f7c7a; background: #ffffff; border: 1px solid #e6ebea; }
        .btn-blue { background: #eef2ff; color: #3457d5; border: 1px solid #dde5ff; }
        .btn-orange { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }

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

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            align-items: start;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
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

        .workflow-card {
            border: 1px solid #edf1f0;
            border-radius: 22px;
            padding: 20px;
            background: #fbfcfc;
            min-height: 190px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .workflow-step {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            background: #eef7f5;
            color: #2f7c7a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 900;
        }

        .workflow-title {
            margin: 14px 0 8px;
            font-size: 18px;
            color: #22343a;
            font-weight: 900;
            line-height: 1.25;
        }

        .workflow-text {
            margin: 0;
            font-size: 13px;
            line-height: 1.75;
            color: #6b7280;
        }

        .workflow-link {
            margin-top: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 10px 13px;
            border-radius: 13px;
            background: #ffffff;
            border: 1px solid #e5ecea;
            color: #2f7c7a;
            font-size: 13px;
            font-weight: 900;
            width: fit-content;
        }

        .list {
            display: grid;
            gap: 12px;
        }

        .list-item {
            border: 1px solid #edf1f0;
            border-radius: 18px;
            padding: 15px;
            background: #fbfcfc;
        }

        .item-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
        }

        .item-title {
            font-size: 15px;
            font-weight: 900;
            color: #22343a;
            line-height: 1.45;
        }

        .item-meta {
            margin-top: 5px;
            font-size: 12px;
            line-height: 1.65;
            color: #6b7280;
        }

        .mini-link {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: #2f7c7a;
            background: #eef7f5;
            border: 1px solid #d8ebe7;
            padding: 8px 11px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 900;
        }

        .status-pill {
            display: inline-flex;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .pending, .partial, .low { background: #fef3c7; color: #92400e; }
        .confirmed, .scheduled, .in_progress { background: #dbeafe; color: #1d4ed8; }
        .completed, .paid, .safe, .active { background: #dcfce7; color: #166534; }
        .cancelled, .unpaid, .empty, .void { background: #fee2e2; color: #b91c1c; }

        .money { font-weight: 900; color: #22343a; white-space: nowrap; }
        .empty-state { padding: 18px; text-align: center; color: #6b7280; line-height: 1.8; }

        @media (max-width: 1280px) {
            .stats-grid { grid-template-columns: repeat(3, 1fr); }
            .grid-3 { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 980px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-grid, .grid-2, .snapshot-grid, .grid-3 { grid-template-columns: 1fr; }
            .title { font-size: 34px; }
        }

        @media (max-width: 640px) {
            .stats-grid { grid-template-columns: 1fr; }
            .hero, .section-card { padding: 20px; border-radius: 22px; }
        }
    
        .vital-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 14px;
        }

        .vital-card {
            border: 1px solid #dfecea;
            border-radius: 22px;
            padding: 18px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fffd 100%);
            box-shadow: 0 12px 28px rgba(30, 74, 73, .06);
        }

        .vital-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .vital-patient {
            margin: 0;
            color: #22343a;
            font-size: 16px;
            font-weight: 900;
            line-height: 1.35;
        }

        .vital-meta {
            margin-top: 4px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.5;
        }

        .pain-badge {
            flex: 0 0 auto;
            border-radius: 999px;
            padding: 8px 10px;
            background: #f5f3ff;
            color: #7c3aed;
            font-size: 12px;
            font-weight: 900;
        }

        .vital-mini-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 12px;
        }

        .vital-mini {
            border: 1px solid #eef2f1;
            background: #ffffff;
            border-radius: 16px;
            padding: 11px 12px;
        }

        .vital-label {
            color: #94a3b8;
            font-size: 10px;
            letter-spacing: .55px;
            text-transform: uppercase;
            font-weight: 900;
        }

        .vital-value {
            margin-top: 5px;
            color: #22343a;
            font-size: 14px;
            font-weight: 900;
            line-height: 1.35;
        }

        .vital-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 14px;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            background: #2f7c7a;
            color: white;
            text-decoration: none;
            font-size: 12px;
            font-weight: 900;
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
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'dashboard'])

    <main class="main">
        <div class="container">
            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <span class="badge">Khayra ERM Command Center</span>
                        <h1 class="title">Dashboard premium untuk operasional klinik fisioterapi.</h1>
                        <p class="subtitle">
                            Satu halaman untuk membaca appointment, visit, rekam medis, kasir, outstanding invoice, inventory, promo, dan aktivitas terbaru klinik.
                        </p>

                        <div class="hero-tags">
                            <span class="hero-tag">Booking / Appointment</span>
                            <span class="hero-tag">Patient Timeline</span>
                            <span class="hero-tag">Visit & Rekam Medis</span>
                            <span class="hero-tag">Kasir Ledger</span>
                            <span class="hero-tag">Inventory Control</span>
                            <span class="hero-tag">Promo & Void</span>
                        </div>

                        <div class="action-row">
                            <a href="/admin/cashier" class="btn btn-primary">+ Kasir Checkout</a>
                            <a href="/admin/bookings" class="btn btn-soft">Booking</a>
                            <a href="/admin/visits" class="btn btn-soft">Visits</a>
                            <a href="/admin/inventory" class="btn btn-soft">Inventory</a>
                            <a href="/admin/billings" class="btn btn-blue">Kasir Ledger</a>
                        </div>
                    </div>

                    <aside class="hero-side">
                        <h3>Today & This Month</h3>
                        <p>Snapshot operasional untuk admin/owner melihat kondisi klinik dengan cepat.</p>

                        <div class="snapshot-grid">
                            <div class="snapshot-card">
                                <div class="snapshot-label">Booking Hari Ini</div>
                                <div class="snapshot-value">{{ $todayBookings }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Visit Hari Ini</div>
                                <div class="snapshot-value">{{ $todayVisits }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Revenue Bulan Ini</div>
                                <div class="snapshot-value">Rp {{ number_format($monthlyNetRevenue, 0, ',', '.') }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Outstanding</div>
                                <div class="snapshot-value">Rp {{ number_format($monthlyOutstanding, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Net Revenue</div>
                    <div class="stat-value">Rp {{ number_format($monthlyNetRevenue, 0, ',', '.') }}</div>
                    <div class="stat-sub">Total invoice non-void bulan ini.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Paid Amount</div>
                    <div class="stat-value">Rp {{ number_format($monthlyPaidAmount, 0, ',', '.') }}</div>
                    <div class="stat-sub">Pembayaran diterima bulan ini.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Outstanding</div>
                    <div class="stat-value">Rp {{ number_format($monthlyOutstanding, 0, ',', '.') }}</div>
                    <div class="stat-sub">{{ $unpaidBillings }} unpaid · {{ $partialBillings }} partial.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">New Patients</div>
                    <div class="stat-value">{{ $newPatientsThisMonth }}</div>
                    <div class="stat-sub">Pasien baru bulan ini.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Low / Empty Stock</div>
                    <div class="stat-value">{{ $lowStockItems + $emptyStockItems }}</div>
                    <div class="stat-sub">{{ $lowStockItems }} low · {{ $emptyStockItems }} empty.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Active Promos</div>
                    <div class="stat-value">{{ $activePromos }}</div>
                    <div class="stat-sub">{{ $voidBillings }} void invoice total.</div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Clinic Workflow</h2>
                        <p class="section-subtitle">Alur ini membuat sistem terlihat sebagai clinic operating system, bukan sekadar kumpulan halaman.</p>
                    </div>
                </div>

                <div class="grid-3">
                    <div class="workflow-card">
                        <div>
                            <span class="workflow-step">1</span>
                            <h3 class="workflow-title">Booking → Patient</h3>
                            <p class="workflow-text">Appointment masuk, admin konfirmasi, lalu data dihubungkan ke master patient.</p>
                        </div>
                        <a href="/admin/bookings" class="workflow-link">Open Booking</a>
                    </div>

                    <div class="workflow-card">
                        <div>
                            <span class="workflow-step">2</span>
                            <h3 class="workflow-title">Visit → Rekam Medis</h3>
                            <p class="workflow-text">Setiap sesi fisioterapi punya visit, therapist, status, dan medical record.</p>
                        </div>
                        <a href="/admin/visits" class="workflow-link">Open Visits</a>
                    </div>

                    <div class="workflow-card">
                        <div>
                            <span class="workflow-step">3</span>
                            <h3 class="workflow-title">Kasir → Invoice → Void</h3>
                            <p class="workflow-text">Checkout layanan dan produk, promo, payment amount, invoice PDF, serta void stock return.</p>
                        </div>
                        <a href="/admin/billings" class="workflow-link">Open Ledger</a>
                    </div>
                </div>
            </section>


            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Vital Sign Snapshot</h2>
                        <p class="section-subtitle">Latest vital sign dan pain scale dari rekam medis therapist untuk monitoring admin.</p>
                    </div>
                    <a href="/admin/visits" class="btn btn-soft">Open Visits</a>
                </div>

                @if(($latestVitalSigns ?? collect())->count())
                    <div class="vital-grid">
                        @foreach($latestVitalSigns as $record)
                            @php
                                $visit = $record->visit;
                                $patient = optional($visit)->patient;
                                $therapist = optional($visit)->therapistRelation;
                            @endphp

                            <div class="vital-card">
                                <div class="vital-top">
                                    <div>
                                        <h3 class="vital-patient">{{ optional($patient)->full_name ?: 'Patient' }}</h3>
                                        <div class="vital-meta">
                                            Visit: {{ optional($visit)->visit_date ?: '-' }}<br>
                                            Therapist: {{ optional($therapist)->full_name ?: optional($visit)->therapist ?: '-' }}
                                        </div>
                                    </div>

                                    <div class="pain-badge">Pain {{ is_null($record->pain_scale) ? '-' : $record->pain_scale . '/10' }}</div>
                                </div>

                                <div class="vital-mini-grid">
                                    <div class="vital-mini">
                                        <div class="vital-label">Blood Pressure</div>
                                        <div class="vital-value">{{ $record->blood_pressure ?: '-' }}</div>
                                    </div>

                                    <div class="vital-mini">
                                        <div class="vital-label">Heart Rate</div>
                                        <div class="vital-value">{{ $record->heart_rate ?: '-' }}</div>
                                    </div>

                                    <div class="vital-mini">
                                        <div class="vital-label">Temperature</div>
                                        <div class="vital-value">{{ $record->temperature ?: '-' }}</div>
                                    </div>

                                    <div class="vital-mini">
                                        <div class="vital-label">Respiration</div>
                                        <div class="vital-value">{{ $record->respiration_rate ?: '-' }}</div>
                                    </div>

                                    <div class="vital-mini">
                                        <div class="vital-label">Weight / Height</div>
                                        <div class="vital-value">{{ $record->weight ?: '-' }} / {{ $record->height ?: '-' }}</div>
                                    </div>

                                    <div class="vital-mini">
                                        <div class="vital-label">BMI</div>
                                        <div class="vital-value">{{ $record->bmi ?: '-' }}</div>
                                    </div>
                                </div>

                                @if($visit)
                                    <a href="/admin/visits/{{ $visit->id }}/medical-record" class="vital-action">Open Medical Record</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">Belum ada vital sign yang tercatat dari rekam medis.</div>
                @endif
            </section>

            <div class="grid-2">
                <section class="section-card">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Need Action</h2>
                            <p class="section-subtitle">Hal yang perlu dicek admin hari ini.</p>
                        </div>
                    </div>

                    <div class="list">
                        @forelse($needActionBookings as $booking)
                            <div class="list-item">
                                <div class="item-top">
                                    <div>
                                        <div class="item-title">Booking Pending: {{ $booking->full_name }}</div>
                                        <div class="item-meta">{{ $booking->service ?: '-' }} · {{ $booking->booking_date ?: '-' }} {{ $booking->booking_time ?: '' }}</div>
                                    </div>
                                    <span class="status-pill pending">pending</span>
                                </div>
                                <a href="/admin/bookings/{{ $booking->id }}" class="mini-link">Open Booking</a>
                            </div>
                        @empty
                            <div class="empty-state">Tidak ada booking pending.</div>
                        @endforelse

                        @forelse($needActionBillings as $billing)
                            <div class="list-item">
                                <div class="item-top">
                                    <div>
                                        <div class="item-title">Outstanding: {{ optional($billing->patient)->full_name ?: 'Patient' }}</div>
                                        <div class="item-meta">{{ $billing->invoice_number ?: 'Billing #' . $billing->id }} · Remaining Rp {{ number_format($billing->remaining_amount ?: 0, 0, ',', '.') }}</div>
                                    </div>
                                    <span class="status-pill {{ $billing->payment_status }}">{{ $billing->payment_status }}</span>
                                </div>
                                <a href="/admin/billings/{{ $billing->id }}" class="mini-link">Open Billing</a>
                            </div>
                        @empty
                            <div class="empty-state">Tidak ada invoice unpaid / partial.</div>
                        @endforelse
                    </div>
                </section>

                <section class="section-card">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Inventory Alerts</h2>
                            <p class="section-subtitle">Barang low / empty stock yang perlu dicek.</p>
                        </div>
                        <a href="/admin/inventory" class="btn btn-soft">Inventory</a>
                    </div>

                    <div class="list">
                        @forelse($needActionItems as $item)
                            <div class="list-item">
                                <div class="item-top">
                                    <div>
                                        <div class="item-title">{{ $item->name }}</div>
                                        <div class="item-meta">{{ $item->sku ?: '-' }} · Stock {{ $item->stock }} {{ $item->unit }} · Minimum {{ $item->minimum_stock }}</div>
                                    </div>
                                    <span class="status-pill {{ $item->stock_status }}">{{ $item->stock_status_label }}</span>
                                </div>
                                <a href="/admin/inventory/{{ $item->id }}" class="mini-link">Open Item</a>
                            </div>
                        @empty
                            <div class="empty-state">Tidak ada inventory alert.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="grid-2">
                <section class="section-card">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Recent Visits</h2>
                            <p class="section-subtitle">Aktivitas visit dan rekam medis terbaru.</p>
                        </div>
                        <a href="/admin/visits" class="btn btn-soft">All Visits</a>
                    </div>

                    <div class="list">
                        @forelse($recentVisits as $visit)
                            <div class="list-item">
                                <div class="item-top">
                                    <div>
                                        <div class="item-title">Visit #{{ $visit->id }} · {{ optional($visit->patient)->full_name ?: 'Patient' }}</div>
                                        <div class="item-meta">
                                            {{ $visit->visit_date ?: '-' }} · {{ optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: '-' }}
                                            · Rekam medis: {{ $visit->medicalRecord ? 'Available' : 'Not created' }}
                                        </div>
                                    </div>
                                    <span class="status-pill {{ $visit->status }}">{{ str_replace('_', ' ', $visit->status ?: '-') }}</span>
                                </div>
                                <a href="/admin/visits/{{ $visit->id }}/medical-record" class="mini-link">Open Medical Record</a>
                            </div>
                        @empty
                            <div class="empty-state">Belum ada recent visits.</div>
                        @endforelse
                    </div>
                </section>

                <section class="section-card">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Recent Billing</h2>
                            <p class="section-subtitle">Invoice terbaru dari kasir ledger.</p>
                        </div>
                        <a href="/admin/billings" class="btn btn-soft">Kasir Ledger</a>
                    </div>

                    <div class="list">
                        @forelse($recentBillings as $billing)
                            <div class="list-item">
                                <div class="item-top">
                                    <div>
                                        <div class="item-title">{{ $billing->invoice_number ?: 'Billing #' . $billing->id }}</div>
                                        <div class="item-meta">
                                            {{ optional($billing->patient)->full_name ?: 'Patient' }} ·
                                            <span class="money">Rp {{ number_format($billing->amount, 0, ',', '.') }}</span>
                                            · Paid Rp {{ number_format($billing->paid_amount ?: 0, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <span class="status-pill {{ $billing->payment_status }}">{{ $billing->payment_status }}</span>
                                </div>
                                <a href="/admin/billings/{{ $billing->id }}" class="mini-link">Open Invoice</a>
                            </div>
                        @empty
                            <div class="empty-state">Belum ada recent billing.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Core Module Summary</h2>
                        <p class="section-subtitle">Ringkasan total data utama di sistem Khayra.</p>
                    </div>
                </div>

                <div class="stats-grid" style="margin-bottom:0;">
                    <div class="stat-card">
                        <div class="stat-label">Total Bookings</div>
                        <div class="stat-value">{{ $totalBookings }}</div>
                        <div class="stat-sub">{{ $pendingBookings }} pending · {{ $confirmedBookings }} confirmed.</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-label">Total Patients</div>
                        <div class="stat-value">{{ $totalPatients }}</div>
                        <div class="stat-sub">Master data pasien.</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-label">Total Visits</div>
                        <div class="stat-value">{{ $totalVisits }}</div>
                        <div class="stat-sub">{{ $completedVisitsThisMonth }} completed bulan ini.</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-label">Therapists</div>
                        <div class="stat-value">{{ $totalTherapists }}</div>
                        <div class="stat-sub">Tim fisioterapi.</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-label">Total Billings</div>
                        <div class="stat-value">{{ $totalBillings }}</div>
                        <div class="stat-sub">{{ $paidBillings }} paid · {{ $voidBillings }} void.</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-label">Discount Month</div>
                        <div class="stat-value">Rp {{ number_format($monthlyDiscount, 0, ',', '.') }}</div>
                        <div class="stat-sub">Promo / diskon bulan ini.</div>
                    </div>
                </div>
            </section>
            <section class="section-card">
                <div class="section-head" style="align-items: flex-start;">
                    <div>
                        <h2 class="section-title">Appointment Arrival Reminder</h2>
                        <p class="section-subtitle">
                            Reminder kedatangan untuk booking hari ini dan besok. Admin bisa langsung kirim WhatsApp pengingat jadwal terapi.
                        </p>
                    </div>

                    <div style="
                        padding: 10px 14px;
                        border-radius: 999px;
                        background: #eef7f5;
                        color: #2f7c7a;
                        font-size: 12px;
                        font-weight: 900;
                        white-space: nowrap;
                    ">
                        {{ ($arrivalReminderBookings ?? collect())->count() }} Reminder
                    </div>
                </div>

                @if(($arrivalReminderBookings ?? collect())->count())
                    <div style="
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                        gap: 14px;
                        margin-top: 18px;
                    ">
                        @foreach($arrivalReminderBookings as $booking)
                            @php
                                $patient = $booking->patient;
                                $rawWa = $patient->whatsapp ?? $booking->whatsapp ?? null;
                                $waNumber = $rawWa ? preg_replace('/[^0-9]/', '', $rawWa) : null;

                                if ($waNumber && str_starts_with($waNumber, '0')) {
                                    $waNumber = '62' . substr($waNumber, 1);
                                } elseif ($waNumber && str_starts_with($waNumber, '8')) {
                                    $waNumber = '62' . $waNumber;
                                }

                                $bookingDateLabel = \Carbon\Carbon::parse($booking->booking_date)->isToday()
                                    ? 'Hari ini'
                                    : (\Carbon\Carbon::parse($booking->booking_date)->isTomorrow() ? 'Besok' : \Carbon\Carbon::parse($booking->booking_date)->format('d M Y'));

                                $bookingTimeLabel = $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('H:i') : '-';
                                $patientName = $patient->full_name ?? $booking->full_name ?? 'Patient';
                                $serviceName = $booking->service ?? $booking->service_name ?? 'Fisioterapi';
                                $statusLabel = ucfirst(str_replace('_', ' ', $booking->status ?? '-'));
                                $waMessage = 'Halo Kak ' . $patientName . ', Khayra Physio ingin mengingatkan jadwal terapi ' . strtolower($bookingDateLabel) . ' pukul ' . $bookingTimeLabel . '. Mohon hadir tepat waktu ya. Jika ada perubahan jadwal, silakan hubungi admin. Terima kasih.';
                            @endphp

                            <div style="
                                border: 1px solid #edf1f0;
                                border-radius: 20px;
                                background: linear-gradient(145deg, #ffffff 0%, #f7fbfa 100%);
                                padding: 18px;
                                box-shadow: 0 10px 22px rgba(15, 23, 42, 0.035);
                            ">
                                <div style="
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: flex-start;
                                    gap: 12px;
                                    margin-bottom: 14px;
                                ">
                                    <div>
                                        <div style="
                                            font-size: 18px;
                                            line-height: 1.3;
                                            color: #22343a;
                                            font-weight: 900;
                                        ">
                                            {{ $patientName }}
                                        </div>
                                        <div style="
                                            margin-top: 5px;
                                            font-size: 12px;
                                            color: #7b8794;
                                            font-weight: 700;
                                        ">
                                            {{ $serviceName }}
                                        </div>
                                    </div>

                                    <div style="
                                        padding: 8px 11px;
                                        border-radius: 999px;
                                        background: {{ \Carbon\Carbon::parse($booking->booking_date)->isToday() ? '#dcfce7' : '#e0f2fe' }};
                                        color: {{ \Carbon\Carbon::parse($booking->booking_date)->isToday() ? '#15803d' : '#0369a1' }};
                                        font-size: 11px;
                                        font-weight: 900;
                                        white-space: nowrap;
                                    ">
                                        {{ $bookingDateLabel }}
                                    </div>
                                </div>

                                <div style="
                                    display: grid;
                                    grid-template-columns: 1fr 1fr;
                                    gap: 10px;
                                    margin-top: 12px;
                                ">
                                    <div style="border: 1px solid #f1f5f9; border-radius: 14px; padding: 12px; background: #ffffff;">
                                        <div style="font-size: 10px; letter-spacing: .45px; text-transform: uppercase; color: #94a3b8; font-weight: 900;">Jam</div>
                                        <div style="margin-top: 5px; font-size: 15px; color: #22343a; font-weight: 900;">
                                            {{ $bookingTimeLabel }}
                                        </div>
                                    </div>

                                    <div style="border: 1px solid #f1f5f9; border-radius: 14px; padding: 12px; background: #ffffff;">
                                        <div style="font-size: 10px; letter-spacing: .45px; text-transform: uppercase; color: #94a3b8; font-weight: 900;">Status</div>
                                        <div style="margin-top: 5px; font-size: 15px; color: #22343a; font-weight: 900;">
                                            {{ $statusLabel }}
                                        </div>
                                    </div>

                                    <div style="border: 1px solid #f1f5f9; border-radius: 14px; padding: 12px; background: #ffffff;">
                                        <div style="font-size: 10px; letter-spacing: .45px; text-transform: uppercase; color: #94a3b8; font-weight: 900;">WhatsApp</div>
                                        <div style="margin-top: 5px; font-size: 13px; color: #22343a; font-weight: 900;">
                                            {{ $rawWa ?: '-' }}
                                        </div>
                                    </div>

                                    <div style="border: 1px solid #f1f5f9; border-radius: 14px; padding: 12px; background: #ffffff;">
                                        <div style="font-size: 10px; letter-spacing: .45px; text-transform: uppercase; color: #94a3b8; font-weight: 900;">Booking ID</div>
                                        <div style="margin-top: 5px; font-size: 13px; color: #22343a; font-weight: 900;">
                                            #{{ $booking->id }}
                                        </div>
                                    </div>
                                </div>

                                <div style="
                                    display: flex;
                                    justify-content: flex-end;
                                    gap: 8px;
                                    margin-top: 14px;
                                    flex-wrap: wrap;
                                ">
                                    <a href="/admin/bookings/{{ $booking->id }}" style="
                                        display: inline-flex;
                                        align-items: center;
                                        text-decoration: none;
                                        padding: 10px 12px;
                                        border-radius: 12px;
                                        background: #eef7f5;
                                        border: 1px solid #d8ebe7;
                                        color: #2f7c7a;
                                        font-size: 12px;
                                        font-weight: 900;
                                    ">Detail Booking</a>

                                    @if($waNumber)
                                        <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode($waMessage) }}" target="_blank" style="
                                            display: inline-flex;
                                            align-items: center;
                                            text-decoration: none;
                                            padding: 10px 12px;
                                            border-radius: 12px;
                                            background: #2f7c7a;
                                            border: 1px solid #2f7c7a;
                                            color: #ffffff;
                                            font-size: 12px;
                                            font-weight: 900;
                                        ">Kirim Reminder WA</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">Tidak ada booking hari ini atau besok yang perlu reminder.</div>
                @endif
            </section>

            <section class="section-card">
                <div class="section-head" style="align-items: flex-start;">
                    <div>
                        <h2 class="section-title">Birthday Promo Reminder</h2>
                        <p class="section-subtitle">
                            Pasien yang ulang tahun dalam 30 hari ke depan. Bisa dipakai untuk follow-up WhatsApp dan promo khusus ulang tahun.
                        </p>
                    </div>

                    <div style="
                        padding: 10px 14px;
                        border-radius: 999px;
                        background: #fff7ed;
                        color: #c2410c;
                        font-size: 12px;
                        font-weight: 900;
                        white-space: nowrap;
                    ">
                        {{ ($upcomingBirthdayPatients ?? collect())->count() }} Upcoming
                    </div>
                </div>

                @if(($upcomingBirthdayPatients ?? collect())->count())
                    <div style="
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                        gap: 14px;
                        margin-top: 18px;
                    ">
                        @foreach($upcomingBirthdayPatients as $patient)
                            @php
                                $daysLeft = (int) ($patient->birthday_days_left ?? 0);
                                $badgeText = $daysLeft === 0 ? 'Hari ini' : $daysLeft . ' hari lagi';
                            @endphp

                            <div style="
                                border: 1px solid #edf1f0;
                                border-radius: 20px;
                                background: linear-gradient(145deg, #ffffff 0%, #fffaf5 100%);
                                padding: 18px;
                                box-shadow: 0 10px 22px rgba(15, 23, 42, 0.035);
                            ">
                                <div style="
                                    display: flex;
                                    align-items: flex-start;
                                    justify-content: space-between;
                                    gap: 12px;
                                    margin-bottom: 14px;
                                ">
                                    <div>
                                        <div style="
                                            font-size: 18px;
                                            line-height: 1.3;
                                            color: #22343a;
                                            font-weight: 900;
                                        ">
                                            {{ $patient->full_name }}
                                        </div>
                                        <div style="
                                            margin-top: 5px;
                                            font-size: 12px;
                                            color: #7b8794;
                                            font-weight: 700;
                                        ">
                                            MR: {{ $patient->medical_record_number ?: '-' }}
                                        </div>
                                    </div>

                                    <div style="
                                        padding: 8px 11px;
                                        border-radius: 999px;
                                        background: {{ $daysLeft === 0 ? '#dcfce7' : '#ffedd5' }};
                                        color: {{ $daysLeft === 0 ? '#15803d' : '#c2410c' }};
                                        font-size: 11px;
                                        font-weight: 900;
                                        white-space: nowrap;
                                    ">
                                        {{ $badgeText }}
                                    </div>
                                </div>

                                <div style="
                                    display: grid;
                                    grid-template-columns: 1fr 1fr;
                                    gap: 10px;
                                    margin-top: 12px;
                                ">
                                    <div style="border: 1px solid #f1f5f9; border-radius: 14px; padding: 12px; background: #ffffff;">
                                        <div style="font-size: 10px; letter-spacing: .45px; text-transform: uppercase; color: #94a3b8; font-weight: 900;">Tanggal Lahir</div>
                                        <div style="margin-top: 5px; font-size: 13px; color: #22343a; font-weight: 900;">
                                            {{ optional($patient->birth_date)->format('Y-m-d') ?: '-' }}
                                        </div>
                                    </div>

                                    <div style="border: 1px solid #f1f5f9; border-radius: 14px; padding: 12px; background: #ffffff;">
                                        <div style="font-size: 10px; letter-spacing: .45px; text-transform: uppercase; color: #94a3b8; font-weight: 900;">Usia Baru</div>
                                        <div style="margin-top: 5px; font-size: 13px; color: #22343a; font-weight: 900;">
                                            {{ $patient->birthday_age ?? '-' }} tahun
                                        </div>
                                    </div>

                                    <div style="border: 1px solid #f1f5f9; border-radius: 14px; padding: 12px; background: #ffffff;">
                                        <div style="font-size: 10px; letter-spacing: .45px; text-transform: uppercase; color: #94a3b8; font-weight: 900;">WhatsApp</div>
                                        <div style="margin-top: 5px; font-size: 13px; color: #22343a; font-weight: 900;">
                                            {{ $patient->whatsapp ?: '-' }}
                                        </div>
                                    </div>

                                    <div style="border: 1px solid #f1f5f9; border-radius: 14px; padding: 12px; background: #ffffff;">
                                        <div style="font-size: 10px; letter-spacing: .45px; text-transform: uppercase; color: #94a3b8; font-weight: 900;">Source</div>
                                        <div style="margin-top: 5px; font-size: 13px; color: #22343a; font-weight: 900;">
                                            {{ $patient->referral_source ?: '-' }}
                                        </div>
                                    </div>
                                </div>

                                <div style="
                                    display: flex;
                                    justify-content: flex-end;
                                    gap: 8px;
                                    margin-top: 14px;
                                    flex-wrap: wrap;
                                ">
                                    <a href="/admin/patients/{{ $patient->id }}" style="
                                        display: inline-flex;
                                        align-items: center;
                                        text-decoration: none;
                                        padding: 10px 12px;
                                        border-radius: 12px;
                                        background: #eef7f5;
                                        border: 1px solid #d8ebe7;
                                        color: #2f7c7a;
                                        font-size: 12px;
                                        font-weight: 900;
                                    ">Detail Patient</a>

                                    @if($patient->whatsapp)
                                        @php
                                            $waNumber = preg_replace('/[^0-9]/', '', $patient->whatsapp);

                                            if (str_starts_with($waNumber, '0')) {
                                                $waNumber = '62' . substr($waNumber, 1);
                                            } elseif (str_starts_with($waNumber, '8')) {
                                                $waNumber = '62' . $waNumber;
                                            }
                                        @endphp

                                        <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo ' . $patient->full_name . ', Khayra Physio ingin mengucapkan selamat ulang tahun. Ada promo khusus ulang tahun untuk Anda. Silakan hubungi admin untuk info lebih lanjut ya.') }}" target="_blank" style="
                                            display: inline-flex;
                                            align-items: center;
                                            text-decoration: none;
                                            padding: 10px 12px;
                                            border-radius: 12px;
                                            background: #2f7c7a;
                                            border: 1px solid #2f7c7a;
                                            color: #ffffff;
                                            font-size: 12px;
                                            font-weight: 900;
                                        ">Kirim Promo WA</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">Tidak ada pasien yang ulang tahun dalam 30 hari ke depan.</div>
                @endif
            </section>

            <section class="section-card">
                <div class="section-head" style="align-items: flex-start;">
                    <div>
                        <h2 class="section-title">Marketing Source Analytics</h2>
                        <p class="section-subtitle">
                            Ringkasan channel pasien mengetahui Khayra. Data ini membantu owner melihat channel marketing yang paling efektif.
                        </p>
                    </div>

                    <div style="
                        padding: 10px 14px;
                        border-radius: 999px;
                        background: #eef7f5;
                        color: #2f7c7a;
                        font-size: 12px;
                        font-weight: 800;
                        white-space: nowrap;
                    ">
                        {{ ($patientSourceTotal ?? 0) }} Total Patient
                    </div>
                </div>

                @if(($patientSourceStats ?? collect())->count())
                    <div style="
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                        gap: 14px;
                        margin-top: 18px;
                    ">
                        @foreach($patientSourceStats as $source)
                            @php
                                $percent = ($patientSourceTotal ?? 0) > 0 ? round(($source->total / $patientSourceTotal) * 100, 1) : 0;
                                $isEmptySource = $source->source === 'Belum diisi';
                            @endphp

                            <div style="
                                border: 1px solid #edf1f0;
                                border-radius: 20px;
                                background: {{ $isEmptySource ? '#fbfcfc' : 'linear-gradient(145deg, #ffffff 0%, #f7fbfa 100%)' }};
                                padding: 18px;
                                box-shadow: 0 10px 22px rgba(15, 23, 42, 0.035);
                            ">
                                <div style="
                                    display: flex;
                                    align-items: flex-start;
                                    justify-content: space-between;
                                    gap: 12px;
                                    margin-bottom: 16px;
                                ">
                                    <div>
                                        <div style="
                                            font-size: 11px;
                                            letter-spacing: .55px;
                                            text-transform: uppercase;
                                            color: #7b8794;
                                            font-weight: 800;
                                            margin-bottom: 7px;
                                        ">
                                            Sumber Informasi
                                        </div>

                                        <div style="
                                            font-size: 20px;
                                            line-height: 1.25;
                                            color: #22343a;
                                            font-weight: 900;
                                        ">
                                            {{ $source->source }}
                                        </div>
                                    </div>

                                    <div style="
                                        min-width: 54px;
                                        height: 54px;
                                        border-radius: 18px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        background: {{ $isEmptySource ? '#f1f5f9' : '#e8f6f2' }};
                                        color: {{ $isEmptySource ? '#64748b' : '#20766f' }};
                                        font-size: 24px;
                                        font-weight: 900;
                                    ">
                                        {{ $source->total }}
                                    </div>
                                </div>

                                <div style="
                                    display: flex;
                                    align-items: center;
                                    justify-content: space-between;
                                    gap: 12px;
                                    margin-bottom: 8px;
                                ">
                                    <span style="font-size: 12px; color: #64748b; font-weight: 800;">Persentase</span>
                                    <span style="font-size: 13px; color: #22343a; font-weight: 900;">{{ $percent }}%</span>
                                </div>

                                <div style="
                                    height: 10px;
                                    background: #edf5f3;
                                    border-radius: 999px;
                                    overflow: hidden;
                                ">
                                    <div style="
                                        height: 100%;
                                        width: {{ $percent }}%;
                                        background: {{ $isEmptySource ? '#94a3b8' : 'linear-gradient(135deg, #3f8f8c, #286d70)' }};
                                        border-radius: 999px;
                                    "></div>
                                </div>

                                <div style="
                                    margin-top: 12px;
                                    font-size: 12px;
                                    line-height: 1.6;
                                    color: #7b8794;
                                ">
                                    {{ $isEmptySource ? 'Data source belum diisi di biodata patient.' : 'Channel akuisisi patient yang sudah tercatat.' }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="
                        margin-top: 16px;
                        padding: 14px 16px;
                        border-radius: 16px;
                        background: #f8fbfa;
                        border: 1px solid #edf1f0;
                        color: #64748b;
                        font-size: 13px;
                        line-height: 1.7;
                    ">
                        Insight: channel dengan persentase tertinggi bisa dijadikan prioritas evaluasi konten, promo, atau campaign digital marketing.
                    </div>
                @else
                    <div class="empty-state">Belum ada data sumber informasi patient.</div>
                @endif
            </section>
        </div>
</main>
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
