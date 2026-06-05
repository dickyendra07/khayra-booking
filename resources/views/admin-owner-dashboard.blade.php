<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(47,124,122,.10), transparent 28%),
                linear-gradient(180deg, #f6fbfa 0%, #eef7f5 100%);
            color: #17232b;
        }

        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1480px; margin: 0 auto; }

        .hero {
            background: #ffffff;
            border: 1px solid #e6eeee;
            border-radius: 30px;
            padding: 28px;
            box-shadow: 0 16px 42px rgba(15, 23, 42, .05);
            margin-bottom: 18px;
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 18px;
            align-items: stretch;
        }

        .badge {
            display: inline-flex;
            padding: 8px 13px;
            border-radius: 999px;
            background: #eef5f4;
            color: #2f7c7a;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 14px;
        }

        .title {
            margin: 0;
            font-size: 44px;
            line-height: 1.05;
            color: #22343a;
            font-weight: 900;
            letter-spacing: -1px;
        }

        .subtitle {
            margin: 14px 0 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.9;
            max-width: 820px;
        }

        .hero-panel {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            border-radius: 24px;
            padding: 24px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .hero-panel::before {
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

        .hero-panel > * { position: relative; z-index: 1; }
        .hero-panel h2 { margin: 0 0 10px; font-size: 25px; line-height: 1.2; }
        .hero-panel p { margin: 0; font-size: 13px; line-height: 1.85; color: rgba(255,255,255,.92); }

        .period-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        input[type="month"] {
            min-height: 44px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.26);
            background: rgba(255,255,255,.12);
            color: #ffffff;
            padding: 0 13px;
            font-weight: 800;
        }

        input[type="month"]::-webkit-calendar-picker-indicator { filter: invert(1); }

        .btn {
            min-height: 44px;
            border: 0;
            border-radius: 14px;
            padding: 0 16px;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            white-space: nowrap;
        }

        .btn-white { background: #ffffff; color: #2f7c7a; }
        .btn-soft { background: #ffffff; color: #2f7c7a; border: 1px solid #d8ebe7; }
        .btn-dark { background: #111827; color: #ffffff; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e6eeee;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .04);
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #7b8794;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 32px;
            line-height: 1.05;
            color: #22343a;
            font-weight: 900;
            word-break: break-word;
        }

        .stat-sub {
            margin-top: 8px;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.65;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            align-items: start;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #e6eeee;
            border-radius: 26px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .04);
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
            font-size: 24px;
            color: #22343a;
            font-weight: 900;
            line-height: 1.2;
        }

        .section-subtitle {
            margin: 8px 0 0;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.8;
        }

        .mini-list {
            display: grid;
            gap: 12px;
        }

        .mini-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            padding: 15px;
            border: 1px solid #edf1f0;
            border-radius: 18px;
            background: #fbfcfc;
            align-items: center;
        }

        .mini-title {
            font-size: 14px;
            font-weight: 900;
            color: #22343a;
            line-height: 1.45;
        }

        .mini-meta {
            margin-top: 4px;
            font-size: 12px;
            color: #7b8794;
            line-height: 1.6;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
        }

        .pill-green { background: #dcfce7; color: #166534; }
        .pill-blue { background: #dbeafe; color: #1d4ed8; }
        .pill-orange { background: #fff7ed; color: #c2410c; }
        .pill-red { background: #fee2e2; color: #b91c1c; }
        .pill-slate { background: #f1f5f9; color: #475569; }

        .money-positive { color: #0f766e; }
        .money-warning { color: #c2410c; }

        .quick-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .empty {
            padding: 20px;
            border-radius: 18px;
            background: #fbfcfc;
            border: 1px dashed #d8e5e3;
            color: #7b8794;
            font-size: 13px;
            line-height: 1.7;
        }

        @media (max-width: 1180px) {
            .layout { display: block; }
            .main { padding: 18px; }
            .hero, .grid-2 { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .title { font-size: 34px; }
        }

        @media (max-width: 720px) {
            .stats-grid { grid-template-columns: 1fr; }
            .mini-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'owner-dashboard'])

    <main class="main">
        <div class="container">
            <section class="hero">
                <div>
                    <span class="badge">Owner / Management Dashboard</span>
                    <h1 class="title">Executive snapshot untuk performa klinik.</h1>
                    <p class="subtitle">
                        Dashboard ini merangkum revenue, outstanding, patient growth, appointment, visit, billing, dan staff leave request
                        agar owner/manajemen bisa membaca kondisi operasional tanpa masuk ke banyak menu.
                    </p>

                    <div class="quick-links" style="margin-top:18px;">
                        <a href="/admin/reports/monthly-clinic?month={{ $month }}" class="btn btn-soft">Monthly Clinic Report</a>
                        <a href="/admin/reports/revenue" class="btn btn-soft">Revenue Report</a>
                        <a href="/admin/staff-leaves" class="btn btn-soft">Staff Leave Approval</a>
                    </div>
                </div>

                <div class="hero-panel">
                    <h2>Period: {{ $monthLabel }}</h2>
                    <p>
                        Gunakan filter bulan untuk melihat ringkasan performa owner. Data revenue mengecualikan invoice void.
                    </p>

                    <form method="GET" action="/admin/owner-dashboard" class="period-form">
                        <input type="month" name="month" value="{{ $month }}">
                        <button type="submit" class="btn btn-white">Update Period</button>
                    </form>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Revenue Paid</div>
                    <div class="stat-value money-positive">Rp {{ number_format($summary['paid_amount'], 0, ',', '.') }}</div>
                    <div class="stat-sub">Total pembayaran masuk bulan ini.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Outstanding</div>
                    <div class="stat-value money-warning">Rp {{ number_format($summary['outstanding'], 0, ',', '.') }}</div>
                    <div class="stat-sub">Estimasi tagihan belum lunas.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Net Revenue</div>
                    <div class="stat-value">Rp {{ number_format($summary['net_revenue'], 0, ',', '.') }}</div>
                    <div class="stat-sub">Nilai invoice valid tanpa void.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Discount</div>
                    <div class="stat-value">Rp {{ number_format($summary['discount'], 0, ',', '.') }}</div>
                    <div class="stat-sub">Total diskon pada invoice valid.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Bookings</div>
                    <div class="stat-value">{{ $summary['bookings'] }}</div>
                    <div class="stat-sub">{{ $summary['booking_pending'] }} pending · {{ $summary['booking_confirmed'] }} confirmed.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Visits</div>
                    <div class="stat-value">{{ $summary['visits'] }}</div>
                    <div class="stat-sub">{{ $summary['completed_visits'] }} completed visits.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">New Patients</div>
                    <div class="stat-value">{{ $summary['new_patients'] }}</div>
                    <div class="stat-sub">Pasien baru pada bulan ini.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Pending Leave</div>
                    <div class="stat-value">{{ $summary['pending_leave'] }}</div>
                    <div class="stat-sub">{{ $summary['active_staff'] }} active staff tercatat.</div>
                </div>
            </section>

            <section class="grid-2">
                <div class="section-card">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Recent Revenue / Billing</h2>
                            <p class="section-subtitle">Invoice terbaru pada periode ini untuk monitoring penjualan dan pembayaran.</p>
                        </div>
                        <a href="/admin/billings" class="btn btn-soft">Open Billing</a>
                    </div>

                    <div class="mini-list">
                        @forelse($recentBillings as $billing)
                            <div class="mini-row">
                                <div>
                                    <div class="mini-title">
                                        {{ $billing->invoice_number ?: 'Invoice #' . $billing->id }}
                                    </div>
                                    <div class="mini-meta">
                                        {{ optional($billing->patient)->full_name ?: 'Patient belum terhubung' }}
                                        · {{ $billing->invoice_date ?: '-' }}
                                        · Rp {{ number_format($billing->amount ?? $billing->grand_total ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                                <span class="pill {{ $billing->payment_status === 'paid' ? 'pill-green' : ($billing->payment_status === 'void' ? 'pill-red' : 'pill-orange') }}">
                                    {{ str_replace('_', ' ', $billing->payment_status ?: 'unpaid') }}
                                </span>
                            </div>
                        @empty
                            <div class="empty">Belum ada invoice pada periode ini.</div>
                        @endforelse
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Staff Leave Approval</h2>
                            <p class="section-subtitle">Request cuti/izin fisioterapis terbaru yang perlu dipantau manajemen.</p>
                        </div>
                        <a href="/admin/staff-leaves" class="btn btn-soft">Review All</a>
                    </div>

                    <div class="mini-list">
                        @forelse($leaveRequests as $leave)
                            <div class="mini-row">
                                <div>
                                    <div class="mini-title">{{ optional($leave->therapist)->full_name ?: 'Staff' }}</div>
                                    <div class="mini-meta">
                                        {{ $leave->start_date ? $leave->start_date->format('Y-m-d') : '-' }}
                                        sampai
                                        {{ $leave->end_date ? $leave->end_date->format('Y-m-d') : '-' }}
                                        · {{ $leave->leave_type ?: 'Leave request' }}
                                    </div>
                                </div>
                                <span class="pill {{ $leave->status === 'approved' ? 'pill-green' : ($leave->status === 'rejected' ? 'pill-red' : 'pill-orange') }}">
                                    {{ $leave->status }}
                                </span>
                            </div>
                        @empty
                            <div class="empty">Belum ada request cuti staff.</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="grid-2">
                <div class="section-card">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Appointment Movement</h2>
                            <p class="section-subtitle">Appointment terbaru dalam periode ini, termasuk status operasionalnya.</p>
                        </div>
                        <a href="/admin/bookings" class="btn btn-soft">Open Appointment</a>
                    </div>

                    <div class="mini-list">
                        @forelse($recentBookings as $booking)
                            <div class="mini-row">
                                <div>
                                    <div class="mini-title">{{ $booking->full_name ?: '-' }}</div>
                                    <div class="mini-meta">
                                        {{ $booking->booking_date ?: '-' }}
                                        {{ $booking->booking_time ? substr((string) $booking->booking_time, 0, 5) : '' }}
                                        · {{ $booking->service ?: '-' }}
                                        · Room: {{ $booking->room_name ?: 'Belum ditentukan' }}
                                    </div>
                                </div>
                                <span class="pill pill-blue">{{ str_replace('_', ' ', $booking->status ?: '-') }}</span>
                            </div>
                        @empty
                            <div class="empty">Belum ada appointment pada periode ini.</div>
                        @endforelse
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Visit & Clinical Flow</h2>
                            <p class="section-subtitle">Visit terbaru untuk memantau progress layanan dan clinical record flow.</p>
                        </div>
                        <a href="/admin/visits" class="btn btn-soft">Open Visits</a>
                    </div>

                    <div class="mini-list">
                        @forelse($recentVisits as $visit)
                            <div class="mini-row">
                                <div>
                                    <div class="mini-title">{{ optional($visit->patient)->full_name ?: '-' }}</div>
                                    <div class="mini-meta">
                                        Visit #{{ $visit->id }}
                                        · {{ $visit->visit_date ?: '-' }}
                                        · {{ optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: 'Fisioterapis belum dipilih' }}
                                        · Room: {{ $visit->room_name ?: optional($visit->booking)->room_name ?: 'Belum ditentukan' }}
                                    </div>
                                </div>
                                <span class="pill pill-slate">{{ str_replace('_', ' ', $visit->status ?: '-') }}</span>
                            </div>
                        @empty
                            <div class="empty">Belum ada visit pada periode ini.</div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
