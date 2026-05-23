<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Calendar - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1540px; margin: 0 auto; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
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
            font-size: 42px;
            line-height: 1.05;
            color: #22343a;
            font-weight: 900;
            letter-spacing: -1px;
        }
        .subtitle {
            margin: 10px 0 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.8;
            max-width: 860px;
        }

        .btn {
            min-height: 42px;
            border: 0;
            cursor: pointer;
            padding: 0 16px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            white-space: nowrap;
            transition: .18s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary {
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #fff;
            box-shadow: 0 12px 24px rgba(47,124,122,.16);
        }
        .btn-soft {
            color: #2f7c7a;
            background: #fff;
            border: 1px solid #d8ebe7;
        }

        .toolbar, .stats, .calendar-card {
            background: #fff;
            border: 1px solid #ecefef;
            border-radius: 28px;
            padding: 18px;
            box-shadow: 0 14px 34px rgba(15,23,42,.05);
            margin-bottom: 18px;
        }
        .toolbar {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .toolbar form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin: 0;
        }
        .date-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 900;
            color: #22343a;
        }
        .today-pill {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            background: #ecfdf5;
            color: #047857;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        input[type="date"] {
            min-height: 42px;
            border: 1px solid #dde5e3;
            border-radius: 14px;
            padding: 0 14px;
            font-weight: 800;
            color: #17232b;
            background: #ffffff;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
        }
        .stat {
            background: #fbfcfc;
            border: 1px solid #edf1f0;
            border-radius: 20px;
            padding: 16px;
        }
        .stat-label {
            font-size: 11px;
            font-weight: 900;
            color: #7b8794;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: 900;
            color: #22343a;
        }

        .calendar-card { padding: 16px; }
        .calendar-scroll {
            overflow: auto;
            border: 1px solid #edf1f0;
            border-radius: 22px;
            max-height: 73vh;
            background:
                linear-gradient(#ffffff 30%, rgba(255,255,255,0)) center top,
                linear-gradient(rgba(255,255,255,0), #ffffff 70%) center bottom,
                radial-gradient(farthest-side at 50% 0, rgba(15,23,42,.12), rgba(15,23,42,0)) center top,
                radial-gradient(farthest-side at 50% 100%, rgba(15,23,42,.10), rgba(15,23,42,0)) center bottom;
            background-repeat: no-repeat;
            background-size: 100% 24px, 100% 24px, 100% 10px, 100% 10px;
            background-attachment: local, local, scroll, scroll;
        }
        .calendar-grid {
            min-width: 1120px;
            display: grid;
            grid-template-columns: 86px repeat({{ max($therapists->count(), 1) + 1 }}, minmax(215px, 1fr));
        }
        .cell {
            border-right: 1px solid #edf1f0;
            border-bottom: 1px solid #edf1f0;
            min-height: 72px;
            padding: 8px;
            background: #fff;
            position: relative;
        }
        .head-cell {
            position: sticky;
            top: 0;
            z-index: 4;
            background: #f8fbfa;
            min-height: 64px;
            display: flex;
            align-items: center;
            font-size: 12px;
            font-weight: 900;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .time-cell {
            position: sticky;
            left: 0;
            z-index: 3;
            background: #f8fbfa;
            font-size: 12px;
            font-weight: 900;
            color: #64748b;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 12px;
        }
        .corner {
            left: 0;
            z-index: 5;
        }
        .therapist-name {
            color: #22343a;
            font-size: 13px;
            line-height: 1.25;
            text-transform: none;
            letter-spacing: 0;
            display: flex;
            align-items: center;
            gap: 9px;
        }
        .therapist-avatar {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background: linear-gradient(135deg, #3d8a89, #2f7c7a);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 900;
            flex: 0 0 auto;
        }

        .booking-card {
            display: block;
            text-decoration: none;
            border-radius: 16px;
            padding: 10px 10px 9px;
            margin-bottom: 7px;
            border-left: 5px solid #2f7c7a;
            background: #eef7f5;
            color: #17232b;
            box-shadow: 0 6px 16px rgba(15,23,42,.05);
            transition: .18s ease;
        }
        .booking-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(15,23,42,.09);
        }
        .booking-card strong {
            display: block;
            font-size: 13px;
            line-height: 1.25;
            margin-bottom: 5px;
        }
        .booking-card span {
            display: block;
            font-size: 12px;
            color: #52616b;
            line-height: 1.35;
        }
        .card-meta {
            display: flex !important;
            justify-content: space-between;
            gap: 8px;
            align-items: center;
            margin-top: 7px;
        }
        .mini-status {
            display: inline-flex !important;
            width: auto;
            padding: 4px 7px;
            border-radius: 999px;
            background: rgba(255,255,255,.65);
            font-size: 10px !important;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #334155 !important;
        }
        .mini-time {
            font-size: 11px !important;
            font-weight: 900;
            color: #334155 !important;
        }

        .booking-card.pending { background: #fff7ed; border-left-color: #f59e0b; }
        .booking-card.confirmed { background: #eff6ff; border-left-color: #2563eb; }
        .booking-card.arrived { background: #ecfdf5; border-left-color: #16a34a; }
        .booking-card.in_treatment { background: #f5f3ff; border-left-color: #7c3aed; }
        .booking-card.completed { background: #dcfce7; border-left-color: #15803d; }
        .booking-card.cancelled { background: #fff1f2; border-left-color: #be123c; opacity: .72; }
        .booking-card.no_show { background: #f8fafc; border-left-color: #64748b; opacity: .8; }

        .empty-slot {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: transparent;
            border-radius: 14px;
            min-height: 42px;
            border: 1px dashed transparent;
            font-size: 12px;
            font-weight: 900;
            transition: .18s ease;
        }
        .cell:hover .empty-slot {
            color: #2f7c7a;
            border-color: #bddbd6;
            background: #f7fbfb;
        }
        .cell.has-booking .empty-slot {
            min-height: 28px;
        }

        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }
        .legend span {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 10px;
            border-radius: 999px;
            background: #f8fafc;
            color: #52616b;
            font-size: 12px;
            font-weight: 800;
        }
        .dot { width: 9px; height: 9px; border-radius: 99px; background: #2f7c7a; }
        .dot.pending { background: #f59e0b; }
        .dot.confirmed { background: #2563eb; }
        .dot.arrived { background: #16a34a; }
        .dot.in_treatment { background: #7c3aed; }
        .dot.completed { background: #15803d; }
        .dot.cancelled { background: #be123c; }
        .dot.no_show { background: #64748b; }

        @media (max-width: 900px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .title { font-size: 32px; }
            .stats { grid-template-columns: 1fr 1fr; }
            .toolbar form, .toolbar .btn, .toolbar input[type="date"] { width: 100%; }
            .toolbar .btn { justify-content: center; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'bookings'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Appointment Scheduler</span>
                    <h1 class="title">Booking Calendar</h1>
                    <p class="subtitle">Tampilan jadwal harian per fisioterapis agar admin mudah melihat slot kosong, jadwal pasien, dan status appointment.</p>
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a href="/admin/bookings" class="btn btn-soft">List View</a>
                    <a href="/admin/bookings/create?date={{ $selectedDate }}" class="btn btn-primary">+ Buat Appointment</a>
                </div>
            </div>

            <section class="toolbar">
                <form method="GET" action="/admin/bookings/calendar">
                    <a class="btn btn-soft" href="/admin/bookings/calendar?date={{ $date->copy()->subDay()->toDateString() }}">← Previous</a>
                    <input type="date" name="date" value="{{ $selectedDate }}">
                    <button class="btn btn-primary" type="submit">Select Date</button>
                    <a class="btn btn-soft" href="/admin/bookings/calendar?date={{ now()->toDateString() }}">Today</a>
                    <a class="btn btn-soft" href="/admin/bookings/calendar?date={{ $date->copy()->addDay()->toDateString() }}">Next →</a>
                </form>
                <div class="date-title">
                    <strong>{{ $date->translatedFormat('l, d F Y') }}</strong>
                    @if($selectedDate === now()->toDateString())
                        <span class="today-pill">Today</span>
                    @endif
                </div>
            </section>

            <section class="stats">
                <div class="stat"><div class="stat-label">Total</div><div class="stat-value">{{ $calendarStats['total'] }}</div></div>
                <div class="stat"><div class="stat-label">Confirmed</div><div class="stat-value">{{ $calendarStats['confirmed'] }}</div></div>
                <div class="stat"><div class="stat-label">Arrived</div><div class="stat-value">{{ $calendarStats['arrived'] }}</div></div>
                <div class="stat"><div class="stat-label">In Treatment</div><div class="stat-value">{{ $calendarStats['in_treatment'] }}</div></div>
                <div class="stat"><div class="stat-label">Completed</div><div class="stat-value">{{ $calendarStats['completed'] }}</div></div>
            </section>

            <section class="calendar-card">
                <div class="calendar-scroll">
                    <div class="calendar-grid">
                        <div class="cell head-cell corner">Time</div>
                        <div class="cell head-cell"><div class="therapist-name">Unassigned</div></div>
                        @foreach($therapists as $therapist)
                            <div class="cell head-cell">
                                <div class="therapist-name">
                                    <span class="therapist-avatar">{{ strtoupper(substr($therapist->full_name ?? $therapist->name ?? 'T', 0, 1)) }}</span>
                                    <span>{{ $therapist->full_name ?? $therapist->name ?? ('Therapist #' . $therapist->id) }}</span>
                                </div>
                            </div>
                        @endforeach

                        @foreach($timeSlots as $slot)
                            <div class="cell time-cell">{{ $slot }}</div>

                            @php
                                $unassignedBookings = $bookings->filter(function ($booking) use ($slot) {
                                    return blank($booking->therapist_id) && substr((string) $booking->booking_time, 0, 5) === $slot;
                                });
                            @endphp
                            <div class="cell {{ $unassignedBookings->count() ? 'has-booking' : '' }}">
                                @foreach($unassignedBookings as $booking)
                                    <a href="/admin/bookings/{{ $booking->id }}" class="booking-card {{ $booking->status }}">
                                        <strong>{{ $booking->full_name }}</strong>
                                        <span>{{ $booking->service ?: '-' }}</span>
                                        <span class="card-meta">
                                            <span class="mini-status">{{ $statusLabels[$booking->status] ?? $booking->status }}</span>
                                            <span class="mini-time">{{ $booking->booking_time ? substr((string) $booking->booking_time, 0, 5) : '-' }}</span>
                                        </span>
                                    </a>
                                @endforeach
                                <a class="empty-slot" href="/admin/bookings/create?date={{ $selectedDate }}&time={{ $slot }}">+ Add</a>
                            </div>

                            @foreach($therapists as $therapist)
                                @php
                                    $slotBookings = $bookings->filter(function ($booking) use ($slot, $therapist) {
                                        return (int) $booking->therapist_id === (int) $therapist->id
                                            && substr((string) $booking->booking_time, 0, 5) === $slot;
                                    });
                                @endphp
                                <div class="cell {{ $slotBookings->count() ? 'has-booking' : '' }}">
                                    @foreach($slotBookings as $booking)
                                        <a href="/admin/bookings/{{ $booking->id }}" class="booking-card {{ $booking->status }}">
                                            <strong>{{ $booking->full_name }}</strong>
                                            <span>{{ $booking->service ?: '-' }}</span>
                                            <span>{{ $statusLabels[$booking->status] ?? $booking->status }}</span>
                                        </a>
                                    @endforeach
                                    <a class="empty-slot" href="/admin/bookings/create?date={{ $selectedDate }}&time={{ $slot }}&therapist_id={{ $therapist->id }}">+ Add</a>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                <div class="legend">
                    @foreach($statusLabels as $key => $label)
                        <span><i class="dot {{ $key }}"></i>{{ $label }}</span>
                    @endforeach
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
