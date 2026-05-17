<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Satu Sehat Readiness - Khayra Physio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;background:#f6f8f8;color:#17232b}
        .layout{min-height:100vh;display:flex}
        .main{flex:1;min-width:0;padding:28px}
        .container{max-width:1380px;margin:0 auto}
        .topbar{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;flex-wrap:wrap;margin-bottom:18px}
        .badge{display:inline-flex;padding:8px 13px;border-radius:999px;background:#eef7f5;color:#2f7c7a;font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px}
        .title{margin:0;font-size:42px;line-height:1.05;color:#22343a;font-weight:900;letter-spacing:-.8px}
        .subtitle{margin:12px 0 0;color:#64748b;font-size:14px;line-height:1.9;max-width:850px}
        .btn{min-height:42px;border:0;padding:0 16px;border-radius:14px;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;cursor:pointer}
        .btn-primary{background:linear-gradient(135deg,#3d8a89,#2f7c7a);color:#fff;box-shadow:0 12px 24px rgba(47,124,122,.16)}
        .btn-soft{background:#fff;color:#2f7c7a;border:1px solid #dfe8e6}
        .hero{background:#fff;border:1px solid #ecefef;border-radius:28px;padding:28px;box-shadow:0 14px 34px rgba(15,23,42,.05);margin-bottom:18px}
        .hero-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:18px}
        .hero-main{background:linear-gradient(135deg,#fff 0%,#f7fbfa 58%,#eef7f5 100%);border:1px solid #dfeae8;border-radius:24px;padding:24px}
        .hero-side{background:linear-gradient(145deg,#467f83,#244f55);color:#fff;border-radius:24px;padding:24px;position:relative;overflow:hidden}
        .hero-side:before{content:"";position:absolute;inset:0;background:linear-gradient(rgba(255,255,255,.055) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.055) 1px,transparent 1px);background-size:56px 56px}
        .hero-side>*{position:relative;z-index:1}
        .score{font-size:64px;font-weight:900;line-height:1;margin:8px 0}
        .score-sub{color:rgba(255,255,255,.86);font-size:13px;line-height:1.8}
        .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:18px}
        .stat-card{background:#fff;border:1px solid #ecefef;border-radius:22px;padding:20px;box-shadow:0 10px 26px rgba(15,23,42,.04)}
        .stat-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:900;margin-bottom:12px}
        .stat-value{font-size:31px;font-weight:900;color:#22343a;line-height:1}
        .stat-sub{margin-top:9px;color:#94a3b8;font-size:12px;line-height:1.65}
        .green .stat-value{color:#166534}.blue .stat-value{color:#1d4ed8}.orange .stat-value{color:#b45309}.violet .stat-value{color:#6d28d9}
        .section-card{background:#fff;border:1px solid #ecefef;border-radius:26px;padding:24px;box-shadow:0 10px 26px rgba(15,23,42,.04);margin-bottom:18px}
        .section-title{margin:0 0 8px;font-size:26px;font-weight:900;color:#22343a}
        .section-subtitle{margin:0 0 18px;color:#64748b;font-size:13px;line-height:1.8}
        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
        .table-wrap{overflow-x:auto;border:1px solid #edf1f0;border-radius:20px;background:#fff}
        table{width:100%;min-width:980px;border-collapse:collapse}
        th,td{padding:14px;border-bottom:1px solid #edf1f0;text-align:left;vertical-align:top;font-size:13px}
        th{background:#f7faf9;color:#486168;text-transform:uppercase;font-size:11px;font-weight:900;letter-spacing:.05em}
        tr:last-child td{border-bottom:0}
        .primary{font-weight:900;color:#22343a;line-height:1.45}
        .secondary{margin-top:4px;color:#94a3b8;font-size:11px;line-height:1.55}
        .pill{display:inline-flex;padding:7px 11px;border-radius:999px;font-size:11px;font-weight:900;text-transform:uppercase;white-space:nowrap}
        .pill-green{background:#dcfce7;color:#166534}
        .pill-orange{background:#fef3c7;color:#92400e}
        .pill-red{background:#fee2e2;color:#b91c1c}
        .pill-blue{background:#dbeafe;color:#1d4ed8}
        .pill-violet{background:#ede9fe;color:#6d28d9}
        .missing{color:#b45309;font-weight:800;line-height:1.6}
        .ready-box{border:1px solid #dbeafe;background:#eff6ff;color:#1e40af;border-radius:18px;padding:16px;line-height:1.8;font-size:13px;font-weight:750}
        @media(max-width:1100px){.hero-grid,.stats-grid,.grid-2{grid-template-columns:1fr 1fr}}
        @media(max-width:760px){.layout{display:block}.main{padding:16px}.hero-grid,.stats-grid,.grid-2{grid-template-columns:1fr}.title{font-size:32px}.btn{width:100%}}
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'satu-sehat'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Satu Sehat Ready</span>
                    <h1 class="title">Satu Sehat Readiness Page</h1>
                    <p class="subtitle">
                        Halaman ini belum melakukan integrasi API penuh, tapi menyiapkan sistem agar data pasien, visit, practitioner, diagnosis,
                        treatment, dan mapping FHIR terlihat siap menuju integrasi Satu Sehat.
                    </p>
                </div>
                <div>
                    <a href="/admin/dashboard" class="btn btn-soft">Dashboard</a>
                </div>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div class="hero-main">
                        <span class="badge">High-ticket package</span>
                        <h2 class="section-title">Kesiapan data klinik untuk menuju integrasi nasional.</h2>
                        <p class="section-subtitle">
                            Fokus tahap ini adalah data completeness checker dan mapping internal. API credential, OAuth, production bridging,
                            dan compliance bisa dijadikan paket lanjutan.
                        </p>
                        <div class="ready-box">
                            Status: <strong>Readiness mode</strong><br>
                            Sistem mengecek data yang sudah tersedia, menandai field yang belum lengkap, dan menampilkan rencana mapping FHIR.
                        </div>
                    </div>

                    <aside class="hero-side">
                        <div class="badge" style="background:rgba(255,255,255,.14);color:#fff;border:1px solid rgba(255,255,255,.2)">Overall Score</div>
                        <div class="score">{{ $summary['overall_score'] }}%</div>
                        <div class="score-sub">
                            Score dihitung dari kesiapan data patient, visit/encounter, dan therapist/practitioner.
                            Semakin lengkap data dasar, semakin siap untuk mapping Satu Sehat/FHIR.
                        </div>
                    </aside>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-label">Patient Ready</div>
                    <div class="stat-value">{{ $summary['patient_ready'] }}/{{ $summary['patients'] }}</div>
                    <div class="stat-sub">NIK, birth date, gender, alamat, WhatsApp, MR.</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-label">Visit Ready</div>
                    <div class="stat-value">{{ $summary['visit_ready'] }}/{{ $summary['visits'] }}</div>
                    <div class="stat-sub">Visit, diagnosis, treatment, medical record.</div>
                </div>
                <div class="stat-card violet">
                    <div class="stat-label">Practitioner Ready</div>
                    <div class="stat-value">{{ $summary['therapist_ready'] }}/{{ $summary['therapists'] }}</div>
                    <div class="stat-sub">Therapist profile untuk mapping practitioner.</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-label">Mode</div>
                    <div class="stat-value">FHIR</div>
                    <div class="stat-sub">Mapping plan, belum API production.</div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">FHIR Mapping Plan</h2>
                <p class="section-subtitle">Rencana mapping internal data Khayra Physio ke resource Satu Sehat/FHIR.</p>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>FHIR Resource</th>
                                <th>Local Data</th>
                                <th>Mapped Fields</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fhirMapping as $map)
                                <tr>
                                    <td><div class="primary">{{ $map['resource'] }}</div></td>
                                    <td>{{ $map['local_data'] }}</td>
                                    <td>{{ $map['fields'] }}</td>
                                    <td><span class="pill pill-blue">{{ $map['status'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="grid-2">
                <section class="section-card">
                    <h2 class="section-title">Data Completeness — Patient</h2>
                    <p class="section-subtitle">Warning jika data identitas pasien belum lengkap.</p>

                    <div class="table-wrap">
                        <table style="min-width:760px">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Score</th>
                                    <th>Missing Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patientRows->take(20) as $row)
                                    <tr>
                                        <td>
                                            <div class="primary">{{ $row['patient']->full_name }}</div>
                                            <div class="secondary">{{ $row['patient']->medical_record_number ?: 'MR belum tersedia' }}</div>
                                        </td>
                                        <td>
                                            <span class="pill {{ $row['score'] >= 80 ? 'pill-green' : ($row['score'] >= 50 ? 'pill-orange' : 'pill-red') }}">
                                                {{ $row['score'] }}%
                                            </span>
                                        </td>
                                        <td>
                                            @if(count($row['missing']))
                                                <div class="missing">{{ implode(', ', $row['missing']) }}</div>
                                            @else
                                                <span class="pill pill-green">Complete</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">Belum ada data patient.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="section-card">
                    <h2 class="section-title">Data Completeness — Practitioner</h2>
                    <p class="section-subtitle">Therapist akan disiapkan sebagai calon Practitioner mapping.</p>

                    <div class="table-wrap">
                        <table style="min-width:760px">
                            <thead>
                                <tr>
                                    <th>Therapist</th>
                                    <th>Score</th>
                                    <th>Missing Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($therapistRows as $row)
                                    <tr>
                                        <td>
                                            <div class="primary">{{ $row['therapist']->full_name }}</div>
                                            <div class="secondary">{{ $row['therapist']->specialty ?: '-' }}</div>
                                        </td>
                                        <td>
                                            <span class="pill {{ $row['score'] >= 80 ? 'pill-green' : ($row['score'] >= 50 ? 'pill-orange' : 'pill-red') }}">
                                                {{ $row['score'] }}%
                                            </span>
                                        </td>
                                        <td>
                                            @if(count($row['missing']))
                                                <div class="missing">{{ implode(', ', $row['missing']) }}</div>
                                            @else
                                                <span class="pill pill-green">Complete</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">Belum ada data therapist.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <section class="section-card">
                <h2 class="section-title">Data Completeness — Visit / Encounter</h2>
                <p class="section-subtitle">Cek readiness visit, medical record, diagnosis, treatment, dan program pasien.</p>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Visit</th>
                                <th>Patient</th>
                                <th>Therapist</th>
                                <th>Score</th>
                                <th>Missing Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visitRows->take(30) as $row)
                                @php $visit = $row['visit']; @endphp
                                <tr>
                                    <td>
                                        <div class="primary">Visit #{{ $visit->id }}</div>
                                        <div class="secondary">{{ $visit->visit_date ?: '-' }} · {{ $visit->status ?: '-' }}</div>
                                    </td>
                                    <td>{{ optional($visit->patient)->full_name ?: '-' }}</td>
                                    <td>{{ optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: '-' }}</td>
                                    <td>
                                        <span class="pill {{ $row['score'] >= 80 ? 'pill-green' : ($row['score'] >= 50 ? 'pill-orange' : 'pill-red') }}">
                                            {{ $row['score'] }}%
                                        </span>
                                    </td>
                                    <td>
                                        @if(count($row['missing']))
                                            <div class="missing">{{ implode(', ', $row['missing']) }}</div>
                                        @else
                                            <span class="pill pill-green">Complete</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5">Belum ada data visit.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Actual API Integration — Next Package</h2>
                <p class="section-subtitle">Bagian ini sengaja ditandai sebagai tahap lanjutan supaya scope tetap aman.</p>
                <div class="ready-box">
                    Integrasi API penuh biasanya butuh credential, OAuth/token flow, environment SATUSEHAT, mapping identifier resmi,
                    testing payload, error handling, dan compliance. Halaman ini membuat sistem terlihat siap tanpa memaksa koneksi API production.
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
