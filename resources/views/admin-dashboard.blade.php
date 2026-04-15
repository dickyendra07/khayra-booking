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
            max-width: 1320px;
            margin: 0 auto;
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
            font-weight: 800;
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
            color: rgba(255,255,255,.88);
            margin-bottom: 6px;
            font-weight: 700;
        }

        .snapshot-value {
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
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
            font-size: 36px;
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

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
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
            font-size: 22px;
            color: #22343a;
            font-weight: 800;
        }

        .section-subtitle {
            margin: 8px 0 18px;
            font-size: 13px;
            line-height: 1.8;
            color: #6b7280;
        }

        .list {
            display: grid;
            gap: 12px;
        }

        .list-item {
            border: 1px solid #edf1f0;
            border-radius: 18px;
            padding: 16px;
            background: #fbfcfc;
        }

        .item-title {
            font-size: 15px;
            font-weight: 800;
            color: #22343a;
            line-height: 1.5;
        }

        .item-meta {
            margin-top: 6px;
            font-size: 12px;
            line-height: 1.7;
            color: #6b7280;
        }

        .status-pill {
            display: inline-block;
            margin-top: 8px;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
        }

        .pending { background: #fef3c7; color: #92400e; }
        .confirmed { background: #dbeafe; color: #1d4ed8; }
        .completed { background: #dcfce7; color: #166534; }
        .cancelled { background: #fee2e2; color: #b91c1c; }
        .paid { background: #dcfce7; color: #166534; }
        .unpaid { background: #fee2e2; color: #b91c1c; }
        .partial { background: #fef3c7; color: #92400e; }

        @media (max-width: 1180px) {
            .stats-grid { grid-template-columns: 1fr 1fr 1fr; }
        }

        @media (max-width: 980px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-grid, .grid-2 { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 640px) {
            .stats-grid { grid-template-columns: 1fr; }
            .hero-title { font-size: 32px; }
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
                        <div class="hero-badge">Admin Dashboard</div>
                        <h1 class="hero-title">A cleaner clinic workspace for patients, visits, billing, and physiotherapy operations.</h1>
                        <p class="hero-text">
                            Dashboard ini merangkum aktivitas utama Khayra Physio mulai dari booking, patient, physiotherapy visits, billing, hingga performa operasional harian dengan tampilan yang lebih premium dan rapi.
                        </p>

                        <div class="hero-tags">
                            <span class="hero-tag">Patient Management</span>
                            <span class="hero-tag">Physiotherapy Visits</span>
                            <span class="hero-tag">Billing & Invoice</span>
                        </div>
                    </div>

                    <div class="hero-side">
                        <h3>Operational Snapshot</h3>
                        <p>Ringkasan cepat performa klinik untuk membantu admin membaca status operasional hari ini.</p>

                        <div class="snapshot-grid">
                            <div class="snapshot-card">
                                <div class="snapshot-label">Active Overview</div>
                                <div class="snapshot-value">{{ $totalPatients }} Patients</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Physiotherapy Team</div>
                                <div class="snapshot-value">{{ $totalTherapists }} Team Members</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Visits Logged</div>
                                <div class="snapshot-value">{{ $totalVisits }} Visits</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Bookings</div>
                    <div class="stat-value">{{ $totalBookings }}</div>
                    <div class="stat-sub">Total booking yang masuk ke sistem.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Patients</div>
                    <div class="stat-value">{{ $totalPatients }}</div>
                    <div class="stat-sub">Total patient aktif yang sudah tercatat.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Physiotherapy Visits</div>
                    <div class="stat-value">{{ $totalVisits }}</div>
                    <div class="stat-sub">Jumlah visit layanan fisioterapi.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Physiotherapy Team</div>
                    <div class="stat-value">{{ $totalTherapists }}</div>
                    <div class="stat-sub">Jumlah staf physiotherapy yang terdaftar.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Billings</div>
                    <div class="stat-value">{{ $totalBillings }}</div>
                    <div class="stat-sub">Total invoice dan billing yang sudah dibuat.</div>
                </div>
            </section>

            <section class="grid-2">
                <div class="section-card">
                    <h2 class="section-title">Recent Bookings</h2>
                    <p class="section-subtitle">Booking terbaru yang masuk dan perlu dimonitor admin.</p>

                    <div class="list">
                        @forelse($recentBookings as $booking)
                            <div class="list-item">
                                <div class="item-title">{{ $booking->full_name }}</div>
                                <div class="item-meta">
                                    {{ $booking->service }} • {{ $booking->booking_date }} {{ $booking->booking_time ? '• ' . $booking->booking_time : '' }}
                                </div>
                                <span class="status-pill {{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                            </div>
                        @empty
                            <div class="item-meta">Belum ada booking terbaru.</div>
                        @endforelse
                    </div>
                </div>

                <div class="section-card">
                    <h2 class="section-title">Recent Physiotherapy Visits</h2>
                    <p class="section-subtitle">Visit terbaru yang sudah terhubung ke patient dan physiotherapy staff.</p>

                    <div class="list">
                        @forelse($recentVisits as $visit)
                            <div class="list-item">
                                <div class="item-title">{{ optional($visit->patient)->full_name ?: 'Patient' }}</div>
                                <div class="item-meta">
                                    {{ $visit->visit_date ?: '-' }} • {{ optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: 'Physiotherapy Team' }}
                                </div>
                                <span class="status-pill {{ $visit->status }}">{{ ucfirst(str_replace('_', ' ', $visit->status)) }}</span>
                            </div>
                        @empty
                            <div class="item-meta">Belum ada physiotherapy visit terbaru.</div>
                        @endforelse
                    </div>
                </div>

                <div class="section-card">
                    <h2 class="section-title">Recent Billings</h2>
                    <p class="section-subtitle">Invoice terbaru beserta status pembayarannya.</p>

                    <div class="list">
                        @forelse($recentBillings as $billing)
                            <div class="list-item">
                                <div class="item-title">{{ $billing->invoice_number }}</div>
                                <div class="item-meta">
                                    {{ optional($billing->patient)->full_name ?: 'Patient' }} • Rp {{ number_format($billing->amount, 0, ',', '.') }}
                                </div>
                                <span class="status-pill {{ $billing->payment_status }}">{{ ucfirst($billing->payment_status) }}</span>
                            </div>
                        @empty
                            <div class="item-meta">Belum ada billing terbaru.</div>
                        @endforelse
                    </div>
                </div>

                <div class="section-card">
                    <h2 class="section-title">Billing Breakdown</h2>
                    <p class="section-subtitle">Ringkasan status invoice untuk membantu follow-up pembayaran.</p>

                    <div class="list">
                        <div class="list-item">
                            <div class="item-title">Paid Billings</div>
                            <div class="item-meta">Invoice yang sudah lunas.</div>
                            <span class="status-pill paid">{{ $paidBillings }}</span>
                        </div>

                        <div class="list-item">
                            <div class="item-title">Unpaid Billings</div>
                            <div class="item-meta">Invoice yang belum dibayar.</div>
                            <span class="status-pill unpaid">{{ $unpaidBillings }}</span>
                        </div>

                        <div class="list-item">
                            <div class="item-title">Partial Billings</div>
                            <div class="item-meta">Invoice yang baru dibayar sebagian.</div>
                            <span class="status-pill partial">{{ $partialBillings }}</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>