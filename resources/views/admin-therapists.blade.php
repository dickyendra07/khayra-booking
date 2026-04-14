<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapists - Khayra Admin</title>
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
            min-width: 1360px;
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

        .name-main {
            font-weight: 800;
            font-size: 15px;
            color: #111827;
        }

        .name-sub {
            margin-top: 4px;
            font-size: 12px;
            color: #94a3b8;
        }

        .email-main {
            font-weight: 700;
            color: #0f766e;
        }

        .specialty-chip,
        .contact-chip {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .specialty-chip {
            background: #f0fdfa;
            border-color: #cfe8e2;
            color: #0f766e;
        }

        .contact-chip {
            background: #f8fafc;
            border-color: #dbe4ee;
            color: #334155;
        }

        .status-pill {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background: #fee2e2;
            color: #b91c1c;
        }

        .action-stack {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .edit-link,
        .status-btn {
            display: inline-block;
            text-decoration: none;
            border: none;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }

        .edit-link {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #cfe0ff;
        }

        .status-btn.active-btn {
            background: #dcfce7;
            color: #166534;
        }

        .status-btn.inactive-btn {
            background: #fee2e2;
            color: #b91c1c;
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
    @include('partials.admin-sidebar', ['activeMenu' => 'therapists'])

    <main class="main">
        <div class="page-header">
            <div>
                <h1 class="page-title">Therapists</h1>
                <p class="page-subtitle">
                    Kelola therapist aktif dan nonaktif, pantau kontak mereka, dan atur akses operasional
                    therapist dari satu halaman yang lebih rapi.
                </p>
            </div>
            <a href="/admin/therapists/create" class="add-btn">+ Tambah Therapist</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @php
            $totalTherapistsCount = $therapists->count();
            $activeTherapistsCount = $therapists->where('status', 'active')->count();
            $inactiveTherapistsCount = $therapists->where('status', 'inactive')->count();
            $specialtyCount = $therapists->pluck('specialty')->filter()->unique()->count();
        @endphp

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Therapists</div>
                <div class="stat-value">{{ $totalTherapistsCount }}</div>
                <div class="stat-sub">Semua therapist yang terdaftar di sistem.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Active</div>
                <div class="stat-value">{{ $activeTherapistsCount }}</div>
                <div class="stat-sub">Therapist yang sedang aktif dipakai operasional.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Inactive</div>
                <div class="stat-value">{{ $inactiveTherapistsCount }}</div>
                <div class="stat-sub">Therapist yang sedang tidak aktif.</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Specialties</div>
                <div class="stat-value">{{ $specialtyCount }}</div>
                <div class="stat-sub">Jumlah specialty unik yang terdata.</div>
            </div>
        </section>

        <section class="section-card">
            <div class="section-head">
                <h2 class="section-title">Therapist List</h2>
                <p class="section-subtitle">
                    Kelola data therapist, update status aktif, dan buka halaman edit dengan cepat.
                </p>
            </div>

            @if($therapists->count() > 0)
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Therapist</th>
                                <th>Email</th>
                                <th>WhatsApp</th>
                                <th>Specialty</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($therapists as $therapist)
                                <tr>
                                    <td>
                                        <div class="name-main">{{ $therapist->full_name }}</div>
                                        <div class="name-sub">Therapist ID #{{ $therapist->id }}</div>
                                    </td>

                                    <td>
                                        <div class="email-main">{{ $therapist->email }}</div>
                                    </td>

                                    <td>
                                        @if($therapist->whatsapp)
                                            <span class="contact-chip">{{ $therapist->whatsapp }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        @if($therapist->specialty)
                                            <span class="specialty-chip">{{ $therapist->specialty }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        <span class="status-pill status-{{ $therapist->status }}">
                                            {{ $therapist->status }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="action-stack">
                                            <a href="/admin/therapists/{{ $therapist->id }}/edit" class="edit-link">Edit</a>

                                            @if($therapist->status === 'active')
                                                <form method="POST" action="/admin/therapists/{{ $therapist->id }}/status">
                                                    @csrf
                                                    <button type="submit" name="status" value="inactive" class="status-btn inactive-btn">
                                                        Set Inactive
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="/admin/therapists/{{ $therapist->id }}/status">
                                                    @csrf
                                                    <button type="submit" name="status" value="active" class="status-btn active-btn">
                                                        Set Active
                                                    </button>
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
                    Belum ada data therapist di sistem. Tambahkan therapist pertama untuk mulai membagi assignment visit.
                </div>
            @endif
        </section>
    </main>
</div>
</body>
</html>