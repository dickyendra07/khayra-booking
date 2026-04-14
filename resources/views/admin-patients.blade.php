<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Patients - Khayra Physio</title>
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

        .snapshot-grid {
            display: grid;
            gap: 12px;
            margin-top: 20px;
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
            word-break: break-word;
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
            grid-template-columns: 1.2fr .8fr .8fr auto;
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
            min-width: 1200px;
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

        .tag-pill {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
            background: #eef7f5;
            color: #2f7c7a;
        }

        .action-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .action-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 9px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 800;
            border: 1px solid transparent;
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

        .action-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
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
    $totalPatients = $patients->count();
    $patientsWithMRN = $patients->filter(fn($patient) => !empty($patient->medical_record_number))->count();
    $patientsWithBirthDate = $patients->filter(fn($patient) => !empty($patient->birth_date))->count();
    $patientsWithVisits = $patients->filter(fn($patient) => ($patient->visits->count() ?? 0) > 0)->count();
@endphp

<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'patients'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div class="brand-wrap">
                    <img src="/images/khayra-logo.png" alt="Khayra Logo">
                    <div class="brand-text">
                        <div class="brand-kicker">Clinic Patient Management</div>
                        <div class="brand-title">Khayra Patients List</div>
                    </div>
                </div>

                <div class="top-actions">
                    <a href="/admin/dashboard" class="ghost-link">Dashboard</a>
                    <a href="/admin/patients/create" class="ghost-link">+ Create Patient</a>
                </div>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <div class="hero-badge">Patient Overview</div>
                        <h1 class="hero-title">Manage patient identity and medical record numbers in one clean workspace.</h1>
                        <p class="hero-text">
                            Halaman ini membantu admin melihat data patient, nomor rekam medis, identitas dasar, dan relasi visit serta billing dengan tampilan yang lebih rapi dan mudah dibaca.
                        </p>

                        <div class="hero-tags">
                            <span class="hero-tag">Medical Record Number</span>
                            <span class="hero-tag">Patient Identity</span>
                            <span class="hero-tag">Birth Date Search</span>
                        </div>
                    </div>

                    <div class="hero-side">
                        <div>
                            <h3>Patient Snapshot</h3>
                            <p>Ringkasan cepat untuk membaca kondisi data patient dan kelengkapan identitasnya.</p>
                        </div>

                        <div class="snapshot-grid">
                            <div class="snapshot-card">
                                <div class="snapshot-label">Latest Patient</div>
                                <div class="snapshot-value">{{ optional($patients->first())->full_name ?: 'No patient yet' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Latest MR Number</div>
                                <div class="snapshot-value">{{ optional($patients->first())->medical_record_number ?: 'Belum ada' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Search Birth Date</div>
                                <div class="snapshot-value">{{ $birthDate ?: 'Belum dipakai' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Patients</div>
                    <div class="stat-value">{{ $totalPatients }}</div>
                    <div class="stat-sub">Semua patient yang tercatat di sistem.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">With MR Number</div>
                    <div class="stat-value">{{ $patientsWithMRN }}</div>
                    <div class="stat-sub">Patient yang sudah punya nomor rekam medis.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Birth Date Filled</div>
                    <div class="stat-value">{{ $patientsWithBirthDate }}</div>
                    <div class="stat-sub">Patient dengan tanggal lahir yang sudah terisi.</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">With Visits</div>
                    <div class="stat-value">{{ $patientsWithVisits }}</div>
                    <div class="stat-sub">Patient yang sudah punya riwayat visit.</div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Filter Patients</h2>
                <p class="section-subtitle">Cari patient berdasarkan nama, NIK, MR number, gender, atau tanggal lahir.</p>

                <form method="GET" action="/admin/patients" class="filter-grid">
                    <div class="field">
                        <label>Search</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Cari nama, WhatsApp, NIK, alamat, agama, MR number"
                        >
                    </div>

                    <div class="field">
                        <label>Gender</label>
                        <select name="gender">
                            <option value="">Semua Gender</option>
                            <option value="female" {{ ($gender ?? '') == 'female' ? 'selected' : '' }}>Perempuan</option>
                            <option value="male" {{ ($gender ?? '') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ $birthDate ?? '' }}">
                    </div>

                    <div class="field">
                        <button type="submit" class="filter-btn">Terapkan Filter</button>
                    </div>
                </form>
            </section>

            <section class="section-card">
                <h2 class="section-title">Patient List</h2>
                <p class="section-subtitle">Daftar patient lengkap beserta nomor rekam medis, identitas, visit, dan billing.</p>

                @if($patients->count() > 0)
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>MR Number</th>
                                    <th>Gender</th>
                                    <th>Birth Date</th>
                                    <th>NIK</th>
                                    <th>WhatsApp</th>
                                    <th>Visits</th>
                                    <th>Billings</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patients as $patient)
                                    <tr>
                                        <td>
                                            <div class="primary-text">{{ $patient->full_name ?: '-' }}</div>
                                            <div class="secondary-text">Patient ID #{{ $patient->id }}</div>
                                        </td>
                                        <td>
                                            <div class="primary-text">{{ $patient->medical_record_number ?: '-' }}</div>
                                            <div class="secondary-text">{{ $patient->education ?: 'Education belum diisi' }}</div>
                                        </td>
                                        <td>
                                            @if($patient->gender)
                                                <span class="tag-pill">{{ ucfirst($patient->gender) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $patient->nik ?: '-' }}</td>
                                        <td>{{ $patient->whatsapp ?: '-' }}</td>
                                        <td>
                                            <div class="primary-text">{{ $patient->visits->count() }}</div>
                                            <div class="secondary-text">visit linked</div>
                                        </td>
                                        <td>
                                            <div class="primary-text">{{ $patient->billings->count() }}</div>
                                            <div class="secondary-text">billing linked</div>
                                        </td>
                                        <td>
                                            <div class="action-stack">
                                                <a href="/admin/patients/{{ $patient->id }}" class="action-link btn-detail">Detail</a>
                                                <a href="/admin/patients/{{ $patient->id }}/edit" class="action-link btn-edit">Edit</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        Belum ada data patient yang cocok dengan filter saat ini.
                    </div>
                @endif
            </section>
        </div>
    </main>
</div>
</body>
</html>