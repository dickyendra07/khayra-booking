<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visits - Khayra Admin</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #1f2937;
            background:
                radial-gradient(circle at top left, rgba(15,118,110,.10), transparent 30%),
                linear-gradient(180deg, #f6fbfa 0%, #eef7f5 100%);
        }

        .layout {
            min-height: 100vh;
            display: flex;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 32px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
        }

        .page-title {
            margin: 0;
            font-size: 38px;
            color: #0f766e;
            line-height: 1.1;
        }

        .page-subtitle {
            margin: 10px 0 0;
            color: #6b7280;
            font-size: 15px;
            line-height: 1.7;
            max-width: 760px;
        }

        .add-btn {
            display: inline-block;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 14px;
            background: #0f766e;
            color: white;
            font-weight: 700;
            box-shadow: 0 12px 26px rgba(15,118,110,.16);
            white-space: nowrap;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            font-size: 14px;
            line-height: 1.7;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0,1fr));
            gap: 18px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: white;
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 14px 35px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
        }

        .stat-value {
            font-size: 34px;
            font-weight: 800;
            color: #0f766e;
            margin-top: 10px;
        }

        .stat-sub {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 8px;
            line-height: 1.6;
        }

        .section-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .section-head {
            margin-bottom: 18px;
        }

        .section-title {
            margin: 0;
            font-size: 22px;
            color: #0f766e;
        }

        .section-subtitle {
            margin: 8px 0 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid #e8f1ef;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1500px;
        }

        th {
            background: #effaf7;
            color: #0f766e;
            text-align: left;
            padding: 16px;
            font-size: 14px;
            border-bottom: 1px solid #e5efec;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #eef2f1;
            font-size: 14px;
            color: #374151;
            vertical-align: top;
        }

        tr:hover td {
            background: #fafefd;
        }

        .patient-name {
            font-weight: 700;
            font-size: 15px;
            color: #111827;
        }

        .patient-sub {
            margin-top: 4px;
            font-size: 12px;
            color: #94a3b8;
        }

        .therapist-name {
            font-weight: 700;
            color: #0f766e;
        }

        .date-main {
            font-weight: 700;
            color: #111827;
        }

        .booking-chip {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #dbe4ee;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
        }

        .status-pill {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            text-transform: capitalize;
        }

        .status-scheduled {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-in_progress {
            background: #fef3c7;
            color: #92400e;
        }

        .status-completed {
            background: #dcfce7;
            color: #166534;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #b91c1c;
        }

        .notes-box {
            max-width: 220px;
            line-height: 1.7;
            color: #4b5563;
        }

        .action-stack {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .record-link,
        .billing-link,
        .edit-link {
            display: inline-block;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .record-link {
            background: #f0fdfa;
            color: #0f766e;
            border-color: #cfe8e2;
        }

        .billing-link {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #cfe0ff;
        }

        .edit-link {
            background: #f8fafc;
            color: #334155;
            border-color: #dbe4ee;
        }

        .empty-state {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            padding: 28px;
            color: #64748b;
            text-align: center;
            line-height: 1.7;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 1024px) {
            .layout {
                display: block;
            }

            .main {
                padding: 20px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'visits'])

    <main class="main">
        <div class="page-header">
            <div>
                <h1 class="page-title">Visits</h1>
                <p class="page-subtitle">
                    Pantau seluruh kunjungan pasien, assignment therapist, status visit, dan alur lanjutan
                    ke medical record maupun billing dari satu halaman.
                </p>
            </div>
            <a href="/admin/visits/create" class="add-btn">+ Tambah Visit</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @php
            $totalVisits = $visits->count();
            $scheduledCount = $visits->where('status', 'scheduled')->count();
            $inProgressCount = $visits->where('status', 'in_progress')->count();
            $completedCount = $visits->where('status', 'completed')->count();
        @endphp

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Visits</div>
                <div class="stat-value">{{ $totalVisits }}</div>
                <div class="stat-sub">Semua visit yang tercatat di sistem.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Scheduled</div>
                <div class="stat-value">{{ $scheduledCount }}</div>
                <div class="stat-sub">Visit yang sudah dijadwalkan.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">In Progress</div>
                <div class="stat-value">{{ $inProgressCount }}</div>
                <div class="stat-sub">Visit yang sedang berjalan.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Completed</div>
                <div class="stat-value">{{ $completedCount }}</div>
                <div class="stat-sub">Visit yang sudah selesai.</div>
            </div>
        </section>

        <section class="section-card">
            <div class="section-head">
                <h2 class="section-title">Visit List</h2>
                <p class="section-subtitle">
                    Gunakan halaman ini untuk melihat progress visit dan membuka aksi lanjutan dengan cepat.
                </p>
            </div>

            @if($visits->count() > 0)
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Booking</th>
                                <th>Visit Date</th>
                                <th>Therapist</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Medical Record</th>
                                <th>Billing</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visits as $visit)
                                <tr>
                                    <td>
                                        <div class="patient-name">{{ $visit->patient->full_name ?? '-' }}</div>
                                        <div class="patient-sub">Visit ID #{{ $visit->id }}</div>
                                    </td>

                                    <td>
                                        <span class="booking-chip">
                                            {{ $visit->booking_id ? '#' . $visit->booking_id : 'No Booking' }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="date-main">{{ $visit->visit_date ?: '-' }}</div>
                                    </td>

                                    <td>
                                        <div class="therapist-name">
                                            {{ $visit->therapistRelation->full_name ?? $visit->therapist ?? '-' }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="status-pill status-{{ $visit->status }}">
                                            {{ str_replace('_', ' ', $visit->status) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="notes-box">{{ $visit->notes ?: '-' }}</div>
                                    </td>

                                    <td>
                                        <div class="action-stack">
                                            <a href="/admin/visits/{{ $visit->id }}/medical-record" class="record-link">
                                                {{ $visit->medicalRecord ? 'View Record' : 'Add Record' }}
                                            </a>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="action-stack">
                                            <a href="/admin/billings/create?visit_id={{ $visit->id }}" class="billing-link">
                                                Create Billing
                                            </a>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="action-stack">
                                            <a href="/admin/visits/{{ $visit->id }}/edit" class="edit-link">Edit Visit</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    Belum ada data visit di sistem. Tambahkan visit pertama untuk mulai mencatat alur terapi pasien.
                </div>
            @endif
        </section>
    </main>
</div>
</body>
</html>