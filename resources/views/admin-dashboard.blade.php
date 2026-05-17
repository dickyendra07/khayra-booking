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
    </style>
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
        </div>
    </main>
</div>
</body>
</html>
