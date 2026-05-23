<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Scheduler - Khayra Physio ERM</title>
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
            max-width: 1360px;
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

        .brand-kicker {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: #7b8794;
        }

        .brand-title {
            margin-top: 4px;
            font-size: 18px;
            font-weight: 800;
            color: #22343a;
        }

        .top-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ghost-link,
        .primary-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 800;
        }

        .ghost-link {
            background: #ffffff;
            border: 1px solid #e6ebea;
            color: #2c5b5a;
        }

        .primary-link {
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            border: 1px solid #2f7c7a;
            color: #ffffff;
            box-shadow: 0 12px 22px rgba(47,124,122,.14);
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
            grid-template-columns: 1.12fr .88fr;
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
            max-width: 820px;
            font-weight: 800;
        }

        .hero-text {
            margin: 16px 0 0;
            font-size: 14px;
            line-height: 1.95;
            color: #6b7280;
            max-width: 760px;
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
            font-size: 25px;
            line-height: 1.2;
        }

        .hero-side p {
            margin: 0;
            font-size: 13px;
            line-height: 1.85;
            color: rgba(255,255,255,.94);
        }

        .pipeline {
            display: grid;
            gap: 10px;
            margin-top: 18px;
        }

        .pipeline-step {
            padding: 13px 14px;
            border-radius: 16px;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.16);
            font-size: 13px;
            line-height: 1.55;
            font-weight: 700;
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
            font-weight: 800;
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

        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
            margin-bottom: 18px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .section-title {
            margin: 0;
            font-size: 24px;
            color: #22343a;
            line-height: 1.2;
            font-weight: 800;
        }

        .section-subtitle {
            margin: 8px 0 0;
            font-size: 13px;
            line-height: 1.8;
            color: #6b7280;
        }

        .date-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: end;
        }

        .date-form input {
            width: 190px;
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
            font-weight: 800;
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

        .preview-grid {
            display: grid;
            grid-template-columns: .8fr 1.2fr;
            gap: 18px;
            margin-top: 18px;
        }

        .preview-card {
            background: #fbfcfc;
            border: 1px solid #edf1f0;
            border-radius: 22px;
            padding: 18px;
        }

        .preview-title {
            margin: 0;
            font-size: 19px;
            color: #22343a;
            font-weight: 900;
        }

        .preview-subtitle {
            margin: 7px 0 16px;
            font-size: 12px;
            line-height: 1.7;
            color: #7b8794;
        }

        .weekly-bars {
            display: grid;
            gap: 12px;
        }

        .weekly-row {
            display: grid;
            grid-template-columns: 74px 1fr 72px;
            gap: 12px;
            align-items: center;
            text-decoration: none;
            color: inherit;
        }

        .weekly-label {
            font-size: 12px;
            font-weight: 900;
            color: #486168;
            text-transform: uppercase;
        }

        .weekly-date {
            display: block;
            margin-top: 3px;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 800;
        }

        .weekly-track {
            height: 12px;
            border-radius: 999px;
            background: #e8f1ef;
            overflow: hidden;
        }

        .weekly-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
        }

        .weekly-count {
            font-size: 12px;
            font-weight: 900;
            color: #2f7c7a;
            text-align: right;
        }

        .month-head {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 8px;
        }

        .month-head div {
            font-size: 11px;
            color: #7b8794;
            font-weight: 900;
            text-transform: uppercase;
            text-align: center;
            padding: 6px 0;
        }

        .month-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }

        .month-day {
            min-height: 82px;
            border-radius: 16px;
            border: 1px solid #edf1f0;
            background: #ffffff;
            padding: 10px;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: .18s ease;
        }

        .month-day:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
        }

        .month-day.muted {
            opacity: .42;
            background: #f8fafc;
        }

        .month-day.selected {
            border-color: #2f7c7a;
            background: #eef7f5;
            box-shadow: inset 0 0 0 1px rgba(47,124,122,.18);
        }

        .month-number {
            font-size: 15px;
            font-weight: 900;
            color: #22343a;
        }

        .month-booking-count {
            font-size: 11px;
            font-weight: 900;
            color: #2f7c7a;
            padding: 5px 7px;
            border-radius: 999px;
            background: #eef7f5;
            width: fit-content;
        }

        .month-dot-row {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .month-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: #2f7c7a;
        }

        .month-dot.pending { background: #f59e0b; }
        .month-dot.confirmed { background: #2563eb; }
        .month-dot.completed { background: #16a34a; }

        .scheduler-grid {
            display: grid;
            grid-template-columns: .72fr 1.28fr;
            gap: 18px;
            align-items: start;
        }

        .week-grid {
            display: grid;
            gap: 10px;
        }

        .day-card {
            display: grid;
            grid-template-columns: 54px 1fr auto;
            gap: 12px;
            align-items: center;
            text-decoration: none;
            color: inherit;
            padding: 14px;
            border-radius: 18px;
            background: #fbfcfc;
            border: 1px solid #edf1f0;
        }

        .day-card.active {
            background: #eef7f5;
            border-color: #b8ded8;
        }

        .day-number {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border: 1px solid #e4ebea;
            color: #2f7c7a;
            font-weight: 900;
            font-size: 20px;
        }

        .day-label {
            font-size: 13px;
            color: #7b8794;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .day-date {
            font-size: 14px;
            font-weight: 900;
            color: #22343a;
        }

        .day-count {
            padding: 7px 10px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #e4ebea;
            color: #2f7c7a;
            font-size: 12px;
            font-weight: 900;
        }

        .agenda-list {
            display: grid;
            gap: 12px;
        }

        .agenda-item {
            display: grid;
            grid-template-columns: 100px 1fr;
            gap: 14px;
            padding: 16px;
            border-radius: 20px;
            background: #fbfcfc;
            border: 1px solid #edf1f0;
        }

        .agenda-time {
            font-size: 21px;
            font-weight: 900;
            color: #2f7c7a;
            line-height: 1.2;
        }

        .agenda-time-sub {
            margin-top: 4px;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .agenda-body-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .agenda-name {
            font-size: 17px;
            font-weight: 900;
            color: #22343a;
            line-height: 1.35;
        }

        .agenda-meta {
            margin-top: 5px;
            font-size: 12px;
            color: #7b8794;
            line-height: 1.65;
        }

        .agenda-complaint {
            margin-top: 10px;
            font-size: 13px;
            color: #52616b;
            line-height: 1.7;
        }

        .agenda-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
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
            min-width: 1160px;
        }

        th {
            text-align: left;
            padding: 15px 16px;
            background: #f7faf9;
            color: #486168;
            font-size: 12px;
            font-weight: 800;
            border-bottom: 1px solid #edf1f0;
            white-space: nowrap;
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

        .complaint-preview {
            max-width: 260px;
            line-height: 1.7;
            color: #52616b;
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
        .status-arrived { background: #e0f2fe; color: #0369a1; }
        .status-in_treatment { background: #f5f3ff; color: #6d28d9; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }
        .status-no_show { background: #f3f4f6; color: #374151; }

        .linked-pill {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            background: #ecfdf5;
            color: #166534;
        }

        .unlinked-pill {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            background: #f8fafc;
            color: #64748b;
        }

        .action-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .action-link,
        button.action-link,
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
            font-family: Arial, sans-serif;
        }

        button.action-link {
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

        .btn-create {
            background: #ecfdf5;
            color: #166534;
            border-color: #ccefd9;
        }

        .btn-wa {
            background: #f0fdf4;
            color: #15803d;
            border-color: #bbf7d0;
        }

        .btn-visit {
            background: #fff7ed;
            color: #c2410c;
            border-color: #fed7aa;
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
            width: 170px;
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


        .quick-action-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .quick-action-btn {
            border: none;
            min-height: 34px;
            padding: 0 11px;
            border-radius: 999px;
            background: #f7faf9;
            color: #2f7c7a;
            border: 1px solid #dcebe8;
            font-size: 11px;
            font-weight: 900;
            cursor: pointer;
            font-family: Arial, sans-serif;
        }

        .quick-action-btn.dark {
            background: #17232b;
            border-color: #17232b;
            color: #ffffff;
        }

        .quick-action-btn.warn {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
        }

        .quick-action-btn.danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
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
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .scheduler-grid,
            .preview-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 1080px) {
            .hero-grid,
            .filter-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 820px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .agenda-item {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .hero,
            .section-card,
            .stat-card {
                padding: 20px;
                border-radius: 22px;
            }

            .hero-title { font-size: 32px; }
            .stats-grid { grid-template-columns: 1fr; }
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
    @include('partials.admin-sidebar', ['activeMenu' => 'bookings'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div class="brand-wrap">
                    <img src="/images/khayra-logo.png" alt="Khayra Logo">
                    <div>
                        <div class="brand-kicker">Appointment Management</div>
                        <div class="brand-title">Booking Scheduler / Jadwal Appointment Fisio</div>
                    </div>
                </div>

                <div class="top-actions">
                    <a href="/admin/dashboard" class="ghost-link">Dashboard ERM</a>
                    <a href="/booking" class="primary-link">Open Public Booking</a>
                </div>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <div class="hero-badge">Booking Pro</div>
                        <h1 class="hero-title">Scheduler appointment yang lebih rapi untuk alur booking, visit, dan rekam medis.</h1>
                        <p class="hero-text">
                            Admin dapat membaca jadwal per tanggal, melihat agenda harian, mengubah status appointment,
                            membuat patient dari booking, lalu melanjutkan appointment menjadi visit fisioterapi.
                        </p>

                        <div class="hero-tags">
                            <span class="hero-tag">Agenda Harian</span>
                            <span class="hero-tag">Appointment Workflow</span>
                            <span class="hero-tag">Create Patient</span>
                            <span class="hero-tag">Create Visit</span>
                        </div>
                    </div>

                    <div class="hero-side">
                        <h3>Booking Flow</h3>
                        <p>Flow ideal untuk operasional appointment Khayra.</p>

                        <div class="pipeline">
                            <div class="pipeline-step">1. Request booking masuk dari pasien</div>
                            <div class="pipeline-step">2. Admin validasi kontak, keluhan, dan jadwal</div>
                            <div class="pipeline-step">3. Status: pending → confirmed → arrived → in treatment</div>
                            <div class="pipeline-step">4. Appointment lanjut ke visit, rekam medis, dan billing</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Today Booking</div>
                    <div class="stat-value">{{ $todayBookings->count() }}</div>
                    <div class="stat-sub">Appointment yang terjadwal hari ini.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Tomorrow</div>
                    <div class="stat-value">{{ $tomorrowBookings->count() }}</div>
                    <div class="stat-sub">Appointment yang masuk untuk besok.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Pending / Confirmed</div>
                    <div class="stat-value">{{ $pendingCount }} / {{ $confirmedCount }}</div>
                    <div class="stat-sub">Butuh follow-up dan yang sudah dikonfirmasi.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Completed / Cancelled</div>
                    <div class="stat-value">{{ $completedCount }} / {{ $cancelledCount }}</div>
                    <div class="stat-sub">Appointment selesai atau dibatalkan.</div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Booking Calendar / Scheduler</h2>
                        <p class="section-subtitle">Pilih tanggal untuk melihat agenda appointment pada hari tersebut.</p>
                    </div>

                    <form method="GET" action="/admin/bookings" class="date-form">
                        <div class="field">
                            <label>Pilih Tanggal</label>
                            <input type="date" name="date" value="{{ $selectedDate }}">
                        </div>
                        <button type="submit" class="filter-btn">Lihat Jadwal</button>
                    </form>
                </div>

                <div class="scheduler-grid">
                    <div>
                        <h3 class="section-title" style="font-size:20px;margin-bottom:12px;">This Week</h3>

                        <div class="week-grid">
                            @foreach($weekDays as $day)
                                <a href="/admin/bookings?date={{ $day['date'] }}" class="day-card {{ $selectedDate === $day['date'] ? 'active' : '' }}">
                                    <div class="day-number">{{ $day['day'] }}</div>
                                    <div>
                                        <div class="day-label">{{ $day['label'] }}</div>
                                        <div class="day-date">{{ $day['date'] }}</div>
                                    </div>
                                    <div class="day-count">{{ $day['count'] }} booking</div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h3 class="section-title" style="font-size:20px;margin-bottom:12px;">Agenda {{ $selectedDate }}</h3>

                        <div class="agenda-list">
                            @forelse($selectedDateBookings as $booking)
                                <div class="agenda-item">
                                    <div>
                                        <div class="agenda-time">{{ $booking->booking_time ? substr($booking->booking_time, 0, 5) : '-' }}</div>
                                        <div class="agenda-time-sub">Appointment</div>
                                    </div>

                                    <div>
                                        <div class="agenda-body-top">
                                            <div>
                                                <div class="agenda-name">{{ $booking->full_name ?: '-' }}</div>
                                                <div class="agenda-meta">
                                                    {{ $booking->service ?: '-' }} · WA {{ $booking->whatsapp ?: '-' }}
                                                    @if($booking->patient)
                                                        · Linked Patient #{{ $booking->patient->id }}
                                                    @endif
                                                </div>
                                            </div>

                                            <span class="status-pill status-{{ $booking->status }}">{{ str_replace('_', ' ', $booking->status ?: '-') }}</span>
                                        </div>

                                        <div class="agenda-complaint">
                                            {{ $booking->complaint ?: 'Belum ada keluhan awal.' }}
                                        </div>

                                        <div class="agenda-actions">
                                            <a href="/admin/bookings/{{ $booking->id }}" class="action-link btn-detail">Detail</a>
                                            <a href="/admin/bookings/{{ $booking->id }}/edit" class="action-link btn-edit">Edit</a>

                                            @if(!$booking->patient)
                                                <form method="POST" action="/admin/bookings/{{ $booking->id }}/create-patient" style="margin:0;">
                                                        @csrf
                                                        <button type="submit" class="action-link btn-create">Create Patient</button>
                                                    </form>
                                            @else
                                                <a href="/admin/visits/create?booking_id={{ $booking->id }}" class="action-link btn-visit">Create Visit</a>
                                            @endif
                                        </div>

                                        <form method="POST" action="/admin/bookings/{{ $booking->id }}/status" class="status-form">
                                            @csrf
                                            <select name="status">
                                                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                <option value="arrived" {{ $booking->status == 'arrived' ? 'selected' : '' }}>Arrived</option>
                                                <option value="in_treatment" {{ $booking->status == 'in_treatment' ? 'selected' : '' }}>In Treatment</option>
                                                <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                <option value="no_show" {{ $booking->status == 'no_show' ? 'selected' : '' }}>No Show</option>
                                            </select>
                                            <button type="submit" class="mini-submit">Update</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    Belum ada appointment pada tanggal ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="preview-grid">
                    <div class="preview-card">
                        <h3 class="preview-title">Weekly Preview</h3>
                        <p class="preview-subtitle">
                            Ringkasan appointment minggu ini berdasarkan tanggal yang sedang dipilih.
                            Total minggu ini: <strong>{{ $weeklyTotal }}</strong> booking.
                        </p>

                        <div class="weekly-bars">
                            @php
                                $maxWeeklyCount = max($weekDays->max('count'), 1);
                            @endphp

                            @foreach($weekDays as $day)
                                @php
                                    $barWidth = max(6, round(($day['count'] / $maxWeeklyCount) * 100));
                                @endphp

                                <a href="/admin/bookings?date={{ $day['date'] }}" class="weekly-row">
                                    <div class="weekly-label">
                                        {{ $day['label'] }}
                                        <span class="weekly-date">{{ $day['day'] }}</span>
                                    </div>

                                    <div class="weekly-track">
                                        <div class="weekly-fill" style="width: {{ $barWidth }}%;"></div>
                                    </div>

                                    <div class="weekly-count">{{ $day['count'] }} booking</div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="preview-card">
                        <h3 class="preview-title">Monthly Preview — {{ $monthLabel }}</h3>
                        <p class="preview-subtitle">
                            Kalender bulan berjalan. Klik tanggal untuk melihat agenda harian.
                            Total bulan ini: <strong>{{ $monthlyTotal }}</strong> booking.
                        </p>

                        <div class="month-head">
                            <div>Mon</div>
                            <div>Tue</div>
                            <div>Wed</div>
                            <div>Thu</div>
                            <div>Fri</div>
                            <div>Sat</div>
                            <div>Sun</div>
                        </div>

                        <div class="month-grid">
                            @foreach($monthDays as $day)
                                <a
                                    href="/admin/bookings?date={{ $day['date'] }}"
                                    class="month-day {{ !$day['is_current_month'] ? 'muted' : '' }} {{ $day['is_selected'] ? 'selected' : '' }}"
                                >
                                    <div>
                                        <div class="month-number">{{ $day['day'] }}</div>

                                        <div class="month-dot-row">
                                            @for($i = 0; $i < min($day['pending'], 3); $i++)
                                                <span class="month-dot pending"></span>
                                            @endfor

                                            @for($i = 0; $i < min($day['confirmed'], 3); $i++)
                                                <span class="month-dot confirmed"></span>
                                            @endfor

                                            @for($i = 0; $i < min($day['completed'], 3); $i++)
                                                <span class="month-dot completed"></span>
                                            @endfor
                                        </div>
                                    </div>

                                    @if($day['count'] > 0)
                                        <div class="month-booking-count">{{ $day['count'] }}</div>
                                    @else
                                        <div class="month-booking-count" style="color:#94a3b8;background:#f8fafc;">0</div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Filter Appointment</h2>
                <p class="section-subtitle">Cari booking berdasarkan nama pasien, WhatsApp, layanan, atau status appointment.</p>

                <form method="GET" action="/admin/bookings" class="filter-grid">
                    <div class="field">
                        <label>Search</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama pasien, WhatsApp, atau layanan"
                        >
                    </div>

                    <div class="field">
                        <label>Status</label>
                        <select name="status">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="arrived" {{ request('status') == 'arrived' ? 'selected' : '' }}>Arrived</option>
                            <option value="in_treatment" {{ request('status') == 'in_treatment' ? 'selected' : '' }}>In Treatment</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                        </select>
                    </div>

                    <div class="field">
                        <button type="submit" class="filter-btn">Terapkan Filter</button>
                    </div>
                </form>
            </section>

            <section class="section-card">
                <h2 class="section-title">Appointment List</h2>
                <p class="section-subtitle">Daftar booking pasien, status appointment, relasi pasien, dan aksi operasional admin.</p>

                @if($bookings->count() > 0)
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Pasien</th>
                                    <th>Kontak</th>
                                    <th>Layanan</th>
                                    <th>Jadwal</th>
                                    <th>Keluhan Awal</th>
                                    <th>Status</th>
                                    <th>Relasi Pasien</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ $booking->full_name ?: '-' }}</div>
                                            <div class="secondary-text">Booking ID #{{ $booking->id }}</div>
                                        </td>

                                        <td>
                                            <div class="primary-text">{{ $booking->whatsapp ?: '-' }}</div>
                                            <div class="secondary-text">WhatsApp pasien</div>
                                        </td>

                                        <td>
                                            <div class="primary-text">{{ $booking->service ?: '-' }}</div>
                                            <div class="secondary-text">Jenis layanan / treatment</div>
                                        </td>

                                        <td>
                                            <div class="primary-text">{{ $booking->booking_date ?: '-' }}</div>
                                            <div class="secondary-text">{{ $booking->booking_time ?: '-' }}</div>
                                        </td>

                                        <td>
                                            <div class="complaint-preview">
                                                {{ $booking->complaint ?: 'Belum ada keluhan awal.' }}
                                            </div>
                                        </td>

                                        <td>
                                            <span class="status-pill status-{{ $booking->status }}">
                                                {{ str_replace('_', ' ', $booking->status ?: '-') }}
                                            </span>
                                        </td>

                                        <td>
                                            @if($booking->patient)
                                                <span class="linked-pill">Linked</span>
                                                <div class="secondary-text" style="margin-top:8px;">
                                                    {{ $booking->patient->full_name }} · Patient ID #{{ $booking->patient->id }}
                                                </div>
                                            @else
                                                <span class="unlinked-pill">Belum linked</span>
                                                <div class="secondary-text" style="margin-top:8px;">
                                                    Buat biodata pasien agar appointment bisa lanjut ke visit.
                                                </div>
                                            @endif
                                        </td>

                                        <td>
                                            @php
                                                $cleanWhatsapp = preg_replace('/[^0-9]/', '', (string) $booking->whatsapp);

                                                if (str_starts_with($cleanWhatsapp, '0')) {
                                                    $cleanWhatsapp = '62' . substr($cleanWhatsapp, 1);
                                                }

                                                $bookingDateText = $booking->booking_date ?: '-';
                                                $bookingTimeText = $booking->booking_time ? substr((string) $booking->booking_time, 0, 5) : '-';
                                                $patientNameText = $booking->full_name ?: 'Bapak/Ibu';
                                                $serviceText = $booking->service ?: 'layanan fisioterapi';

                                                $waMessage = "Halo {$patientNameText}, kami dari Khayra Physio ingin menindaklanjuti booking Anda untuk {$serviceText} pada {$bookingDateText} pukul {$bookingTimeText}.";

                                                if ($booking->status === 'confirmed') {
                                                    $waMessage = "Halo {$patientNameText}, booking Anda di Khayra Physio sudah dikonfirmasi untuk {$serviceText} pada {$bookingDateText} pukul {$bookingTimeText}. Mohon hadir sesuai jadwal. Terima kasih.";
                                                } elseif ($booking->status === 'arrived') {
                                                    $waMessage = "Halo {$patientNameText}, terima kasih sudah hadir di Khayra Physio. Tim kami akan membantu proses layanan Anda.";
                                                } elseif ($booking->status === 'in_treatment') {
                                                    $waMessage = "Halo {$patientNameText}, sesi treatment Anda sedang berlangsung. Semoga proses terapi berjalan nyaman dan lancar.";
                                                } elseif ($booking->status === 'completed') {
                                                    $waMessage = "Halo {$patientNameText}, terima kasih sudah melakukan sesi terapi di Khayra Physio. Jika ada keluhan lanjutan atau ingin menjadwalkan kontrol berikutnya, silakan hubungi kami kembali.";
                                                } elseif ($booking->status === 'cancelled') {
                                                    $waMessage = "Halo {$patientNameText}, booking Anda untuk {$serviceText} pada {$bookingDateText} pukul {$bookingTimeText} telah dibatalkan. Silakan hubungi kami bila ingin menjadwalkan ulang.";
                                                } elseif ($booking->status === 'no_show') {
                                                    $waMessage = "Halo {$patientNameText}, kami mencatat Anda belum hadir pada jadwal booking {$serviceText} tanggal {$bookingDateText} pukul {$bookingTimeText}. Silakan hubungi kami jika ingin reschedule.";
                                                }

                                                $waLink = $cleanWhatsapp
                                                    ? 'https://wa.me/' . $cleanWhatsapp . '?text=' . rawurlencode($waMessage)
                                                    : null;
                                            @endphp

                                            <div class="action-stack">
                                                @if($waLink)
                                                    <a href="{{ $waLink }}" target="_blank" class="action-link btn-wa">Reminder WA</a>
                                                @endif
                                                <a href="/admin/bookings/{{ $booking->id }}" class="action-link btn-detail">Detail</a>
                                                <a href="/admin/bookings/{{ $booking->id }}/edit" class="action-link btn-edit">Edit</a>

                                                @if(!$booking->patient)
                                                    <form method="POST" action="/admin/bookings/{{ $booking->id }}/create-patient" style="margin:0;">
                                                        @csrf
                                                        <button type="submit" class="action-link btn-create">Create Patient</button>
                                                    </form>
                                                @else
                                                    <a href="/admin/visits/create?booking_id={{ $booking->id }}" class="action-link btn-visit">Create Visit</a>
                                                @endif
                                            </div>

                                            <form method="POST" action="/admin/bookings/{{ $booking->id }}/status" class="status-form">
                                                @csrf
                                                <select name="status">
                                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                    <option value="arrived" {{ $booking->status == 'arrived' ? 'selected' : '' }}>Arrived</option>
                                                    <option value="in_treatment" {{ $booking->status == 'in_treatment' ? 'selected' : '' }}>In Treatment</option>
                                                    <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    <option value="no_show" {{ $booking->status == 'no_show' ? 'selected' : '' }}>No Show</option>
                                                </select>
                                                <button type="submit" class="mini-submit">Update</button>
                                            </form>
                                            <div class="quick-action-row">
                                                @if($booking->status === 'pending')
                                                    <form method="POST" action="/admin/bookings/{{ $booking->id }}/status" style="margin:0;">
                                                        @csrf
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="quick-action-btn">Confirm</button>
                                                    </form>
                                                @endif

                                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                                    <form method="POST" action="/admin/bookings/{{ $booking->id }}/status" style="margin:0;">
                                                        @csrf
                                                        <input type="hidden" name="status" value="arrived">
                                                        <button type="submit" class="quick-action-btn">Arrived</button>
                                                    </form>
                                                @endif

                                                @if(in_array($booking->status, ['confirmed', 'arrived']))
                                                    <form method="POST" action="/admin/bookings/{{ $booking->id }}/status" style="margin:0;">
                                                        @csrf
                                                        <input type="hidden" name="status" value="in_treatment">
                                                        <button type="submit" class="quick-action-btn dark">Start Treatment</button>
                                                    </form>
                                                @endif

                                                @if(in_array($booking->status, ['arrived', 'in_treatment']))
                                                    <form method="POST" action="/admin/bookings/{{ $booking->id }}/status" style="margin:0;">
                                                        @csrf
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" class="quick-action-btn">Complete</button>
                                                    </form>
                                                @endif

                                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                                    <form method="POST" action="/admin/bookings/{{ $booking->id }}/status" style="margin:0;">
                                                        @csrf
                                                        <input type="hidden" name="status" value="no_show">
                                                        <button type="submit" class="quick-action-btn warn">No Show</button>
                                                    </form>
                                                @endif

                                                @if(!in_array($booking->status, ['completed', 'cancelled']))
                                                    <form method="POST" action="/admin/bookings/{{ $booking->id }}/status" style="margin:0;">
                                                        @csrf
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="quick-action-btn danger">Cancel</button>
                                                    </form>
                                                @endif
                                            </div>

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
