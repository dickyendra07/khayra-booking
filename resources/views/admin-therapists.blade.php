<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Physiotherapy Team - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #f7faf9 0%, #f3f7f6 100%);
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
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .title {
            margin: 0;
            font-size: 38px;
            line-height: 1.08;
            font-weight: 800;
            color: #17756f;
        }

        .subtitle {
            margin: 10px 0 0;
            max-width: 760px;
            font-size: 14px;
            line-height: 1.8;
            color: #5f6b76;
        }

        .primary-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 14px 18px;
            border-radius: 16px;
            background: linear-gradient(135deg, #17857e 0%, #136b66 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 12px 24px rgba(23, 117, 111, 0.18);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e8efed;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }

        .stat-label {
            font-size: 13px;
            color: #6d7784;
            margin-bottom: 12px;
        }

        .stat-value {
            font-size: 30px;
            line-height: 1;
            font-weight: 800;
            color: #17756f;
            margin-bottom: 10px;
        }

        .stat-desc {
            font-size: 12px;
            line-height: 1.7;
            color: #8a97a6;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #e8efed;
            border-radius: 26px;
            padding: 22px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.05);
        }

        .section-title {
            margin: 0;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 800;
            color: #17756f;
        }

        .section-subtitle {
            margin: 10px 0 0;
            font-size: 14px;
            line-height: 1.8;
            color: #667381;
        }

        .table-wrap {
            margin-top: 22px;
            border: 1px solid #e7edeb;
            border-radius: 20px;
            overflow: hidden;
            background: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #eef6f4;
            color: #2e7a75;
            text-align: left;
            font-size: 14px;
            font-weight: 800;
            padding: 16px 16px;
            border-bottom: 1px solid #e3ece9;
        }

        tbody td {
            padding: 16px;
            border-bottom: 1px solid #edf2f1;
            vertical-align: middle;
            font-size: 14px;
            color: #314150;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .staff-name {
            font-size: 16px;
            font-weight: 800;
            color: #1f2f3d;
            margin-bottom: 4px;
        }

        .staff-sub {
            font-size: 12px;
            color: #8d9aab;
        }

        .email-link {
            color: #1f7b75;
            text-decoration: none;
            font-weight: 700;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .pill-specialty {
            background: #edf7f4;
            color: #2d7a75;
            border-color: #d9ebe6;
        }

        .pill-active {
            background: #dcfce7;
            color: #166534;
        }

        .pill-inactive {
            background: #fee2e2;
            color: #b91c1c;
        }

        .action-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
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
            background: #ffffff;
        }

        .btn-edit {
            background: #eef2ff;
            color: #3859d6;
            border-color: #dbe4ff;
        }

        .btn-deactivate {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fecdd3;
        }

        .btn-activate {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0;
        }

        .empty-state {
            padding: 26px;
            text-align: center;
            color: #7b8794;
            font-size: 14px;
        }

        @media (max-width: 1100px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .table-wrap {
                overflow-x: auto;
            }

            table {
                min-width: 980px;
            }
        }

        @media (max-width: 820px) {
            .layout {
                display: block;
            }

            .main {
                padding: 16px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .title {
                font-size: 30px;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'therapists'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <h1 class="title">Physiotherapy Team</h1>
                    <p class="subtitle">
                        Kelola staff physiotherapy aktif dan nonaktif, pantau kontak mereka, dan atur akses operasional tim dari satu halaman yang lebih rapi.
                    </p>
                </div>

                <a href="/admin/therapists/create" class="primary-link">+ Tambah Staff</a>
            </div>

            @php
                $totalStaff = $therapists->count();
                $activeStaff = $therapists->where('status', 'active')->count();
                $inactiveStaff = $therapists->where('status', 'inactive')->count();
                $specialtiesCount = $therapists->pluck('specialty')->filter()->unique()->count();
            @endphp

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Physiotherapy Team</div>
                    <div class="stat-value">{{ $totalStaff }}</div>
                    <div class="stat-desc">Semua staff physiotherapy yang terdaftar di sistem.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Active</div>
                    <div class="stat-value">{{ $activeStaff }}</div>
                    <div class="stat-desc">Staff physiotherapy yang sedang aktif dipakai operasional.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Inactive</div>
                    <div class="stat-value">{{ $inactiveStaff }}</div>
                    <div class="stat-desc">Staff physiotherapy yang sedang tidak aktif.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Specialties</div>
                    <div class="stat-value">{{ $specialtiesCount }}</div>
                    <div class="stat-desc">Jumlah specialty unik yang terdata.</div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Physiotherapy Team List</h2>
                <p class="section-subtitle">
                    Kelola data staff physiotherapy, update status aktif, dan buka halaman edit dengan cepat.
                </p>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Physiotherapy Staff</th>
                                <th>Email</th>
                                <th>WhatsApp</th>
                                <th>Specialty</th>
                                <th>Status</th>
                                <th style="width: 190px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($therapists as $therapist)
                                <tr>
                                    <td>
                                        <div class="staff-name">{{ $therapist->full_name }}</div>
                                        <div class="staff-sub">Staff ID #{{ $therapist->id }}</div>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $therapist->email }}" class="email-link">{{ $therapist->email }}</a>
                                    </td>
                                    <td>
                                        {{ $therapist->whatsapp ?: '-' }}
                                    </td>
                                    <td>
                                        <span class="pill pill-specialty">{{ $therapist->specialty ?: 'General' }}</span>
                                    </td>
                                    <td>
                                        @if($therapist->status === 'active')
                                            <span class="pill pill-active">Active</span>
                                        @else
                                            <span class="pill pill-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-row">
                                            <a href="/admin/therapists/{{ $therapist->id }}/edit" class="action-btn btn-edit">Edit</a>

                                            <form method="POST" action="/admin/therapists/{{ $therapist->id }}/status" style="margin: 0;">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $therapist->status === 'active' ? 'inactive' : 'active' }}">
                                                <button type="submit" class="action-btn {{ $therapist->status === 'active' ? 'btn-deactivate' : 'btn-activate' }}">
                                                    {{ $therapist->status === 'active' ? 'Set Inactive' : 'Set Active' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">Belum ada data staff physiotherapy yang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>