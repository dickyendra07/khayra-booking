<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Bookings - Khayra Physio</title>
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

        .side-list {
            margin: 18px 0 0;
            padding-left: 18px;
        }

        .side-list li {
            margin-bottom: 10px;
            font-size: 14px;
            line-height: 1.8;
            color: rgba(255,255,255,.92);
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
            min-width: 1100px;
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

        .action-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .action-link,
        .action-btn,
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
            cursor: pointer;
            font-family: Arial, sans-serif;
            transition: all .18s ease;
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

        .btn-create {
            background: #ecfdf5;
            color: #166534;
            border-color: #ccefd9;
        }

        .btn-status {
            background: #ffffff;
            color: #22343a;
            border-color: #dbe5e3;
        }

        .action-link:hover,
        .action-btn:hover,
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
        }
    </style>
</head>
<body>
@php
    $pendingCount = $bookings->where('status', 'pending')->count();
    $confirmedCount = $bookings->where('status', 'confirmed')->count();
    $completedCount = $bookings->where('status', 'completed')->count();
@endphp

<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'bookings'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div class="brand-wrap">
                    <img src="/images/khayra-logo.png" alt="Khayra Logo">
                    <div class="brand-text">
                        <div class="brand-kicker">Clinic Booking Management</div>
                        <div class="brand-title">Khayra Booking List</div>
                    </div>
                </div>

                <div class="top-actions">
                    <a href="/admin/dashboard" class="ghost-link">Dashboard</a>
                    <a href="/booking" class="ghost-link">Open Booking Form</a>
                </div>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <div class="hero-badge">Booking Operations</div>
                        <h1 class="hero-title">Manage incoming bookings with a clearer and more refined workflow.</h1>
                        <p class="hero-text">
                            Halaman ini membantu admin memantau booking masuk, mengubah status, menghubungkan ke patient,
                            dan melakukan koreksi data booking dengan tampilan yang lebih rapi dan mudah diolah.
                        </p>

                        <div class="hero-tags">
                            <span class="hero-tag">Incoming Requests</span>
                            <span class="hero-tag">Status Update</span>
                            <span class="hero-tag">Linked to Patient</span>
                        </div>
                    </div>

                    <div class="hero-side">
                        <div>
                            <h3>Booking Notes</h3>
                            <p>Ringkasan cepat untuk membantu admin memproses booking tanpa membuka banyak halaman.</p>
                        </div>

                        <ul class="side-list">
                            <li>Pastikan data patient dan WhatsApp sudah valid.</li>
                            <li>Gunakan status booking sesuai progres terbaru.</li>
                            <li>Hubungkan booking ke patient bila data sudah final.</li>
                            <li>Edit data booking jika ada koreksi dari patient.</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Bookings</div>
                    <div class="stat-value">{{ $bookings->count() }}</div>
                    <div class="stat-sub">Semua request booking yang saat ini tercatat di sistem.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Pending</div>
                    <div class="stat-value">{{ $pendingCount }}</div>
                    <div class="stat-sub">Booking yang masih menunggu tindak lanjut admin.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Confirmed</div>
                    <div class="stat-value">{{ $confirmedCount }}</div>
                    <div class="stat-sub">Booking yang sudah dikonfirmasi oleh admin.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Completed</div>
                    <div class="stat-value">{{ $completedCount }}</div>
                    <div class="stat-sub">Booking yang sudah selesai diproses atau dijalankan.</div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Filter Bookings</h2>
                <p class="section-subtitle">Cari booking berdasarkan nama, WhatsApp, layanan, atau status.</p>

                <form method="GET" action="/admin/bookings" class="filter-grid">
                    <div class="field">
                        <label>Search</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama patient, WhatsApp, atau layanan"
                        >
                    </div>

                    <div class="field">
                        <label>Status</label>
                        <select name="status">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="field">
                        <button type="submit" class="filter-btn">Terapkan Filter</button>
                    </div>
                </form>
            </section>

            <section class="section-card">
                <h2 class="section-title">Booking List</h2>
                <p class="section-subtitle">Daftar booking patient beserta status, relasi patient, dan aksi admin.</p>

                @if($bookings->count() > 0)
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>WhatsApp</th>
                                    <th>Service</th>
                                    <th>Schedule</th>
                                    <th>Complaint</th>
                                    <th>Status</th>
                                    <th>Linked Patient</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ $booking->full_name ?: '-' }}</div>
                                            <div class="secondary-text">Booking ID #{{ $booking->id }}</div>
                                        </td>
                                        <td>{{ $booking->whatsapp ?: '-' }}</td>
                                        <td>{{ $booking->service ?: '-' }}</td>
                                        <td>
                                            <div class="primary-text">{{ $booking->booking_date ?: '-' }}</div>
                                            <div class="secondary-text">{{ $booking->booking_time ?: '-' }}</div>
                                        </td>
                                        <td>{{ $booking->complaint ?: '-' }}</td>
                                        <td>
                                            <span class="status-pill status-{{ $booking->status }}">
                                                {{ $booking->status ?: '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($booking->patient)
                                                <div class="primary-text">{{ $booking->patient->full_name }}</div>
                                                <div class="secondary-text">Patient ID #{{ $booking->patient->id }}</div>
                                            @else
                                                <div class="secondary-text">Belum terhubung</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-stack">
                                                <a href="/admin/bookings/{{ $booking->id }}" class="action-link btn-detail">Detail</a>
                                                <a href="/admin/bookings/{{ $booking->id }}/edit" class="action-link btn-edit">Edit</a>

                                                @if(!$booking->patient)
                                                    <a href="/admin/bookings/{{ $booking->id }}/create-patient" class="action-link btn-create">Create Patient</a>
                                                @endif
                                            </div>

                                            <form method="POST" action="/admin/bookings/{{ $booking->id }}/status" class="status-form">
                                                @csrf
                                                <select name="status">
                                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                    <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                        Belum ada data booking yang cocok dengan filter saat ini.
                    </div>
                @endif
            </section>
        </div>
    </main>
</div>
</body>
</html>