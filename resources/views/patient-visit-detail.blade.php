<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visit Detail - Khayra Physio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at 12% 0%, rgba(47,124,122,.12), transparent 28%),
                linear-gradient(180deg, #eef5f4 0%, #f7faf9 42%, #ffffff 100%);
            color: #17232b;
        }

        .page { min-height: 100vh; padding: 34px; }
        .container { max-width: 1280px; margin: 0 auto; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
        .brand img {
            width: 46px;
            height: 46px;
            object-fit: contain;
            border-radius: 14px;
            background: rgba(255,255,255,.75);
            border: 1px solid rgba(47,124,122,.14);
            padding: 4px;
        }

        .brand-name { font-size: 17px; font-weight: 900; color: #22343a; }
        .muted { color: #94a3b8; font-size: 12px; line-height: 1.6; }

        .badge {
            display: inline-flex;
            padding: 8px 13px;
            border-radius: 999px;
            background: #eef7f5;
            color: #2f7c7a;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 10px;
        }

        .title {
            margin: 0;
            font-size: 44px;
            line-height: 1.04;
            letter-spacing: -.8px;
            color: #22343a;
            font-weight: 900;
        }

        .subtitle {
            margin: 12px 0 0;
            max-width: 780px;
            font-size: 14px;
            line-height: 1.9;
            color: #64748b;
        }

        .actions { display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }

        .btn {
            min-height: 44px;
            padding: 0 18px;
            border-radius: 15px;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            font-size: 13px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            white-space: nowrap;
        }

        .btn-primary {
            color: #ffffff;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            box-shadow: 0 12px 24px rgba(47,124,122,.18);
        }

        .btn-soft {
            color: #2f7c7a;
            background: rgba(255,255,255,.9);
            border: 1px solid #dfeae8;
        }

        .hero {
            background: #ffffff;
            border: 1px solid #e6eeee;
            border-radius: 34px;
            box-shadow: 0 24px 54px rgba(15,23,42,.08);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.08fr .92fr;
            min-height: 330px;
        }

        .hero-main {
            padding: 30px;
            background: linear-gradient(135deg, #ffffff 0%, #f9fcfb 52%, #eef7f5 100%);
        }

        .hero-side {
            padding: 30px;
            color: #ffffff;
            background:
                radial-gradient(circle at 80% 18%, rgba(255,255,255,.15), transparent 28%),
                linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-side::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px);
            background-size: 56px 56px;
            pointer-events: none;
        }

        .hero-side > * { position: relative; z-index: 1; }

        .visit-title {
            margin: 0 0 10px;
            font-size: 36px;
            line-height: 1.08;
            color: #22343a;
            font-weight: 900;
        }

        .profile-card {
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.20);
            border-radius: 24px;
            padding: 20px;
            margin-bottom: 14px;
        }

        .profile-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.78);
            font-weight: 900;
            margin-bottom: 7px;
        }

        .profile-value {
            font-size: 24px;
            line-height: 1.25;
            font-weight: 900;
            color: #ffffff;
        }

        .profile-muted {
            margin-top: 7px;
            color: rgba(255,255,255,.82);
            font-size: 13px;
            line-height: 1.7;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-top: 22px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e8eeee;
            border-radius: 22px;
            padding: 18px;
            box-shadow: 0 12px 28px rgba(15,23,42,.045);
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 900;
            color: #22343a;
            white-space: nowrap;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1.08fr .92fr;
            gap: 20px;
            align-items: start;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #e8eeee;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 14px 34px rgba(15,23,42,.055);
            margin-bottom: 20px;
        }

        .section-title {
            margin: 0;
            color: #22343a;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -.4px;
        }

        .section-subtitle {
            margin: 8px 0 20px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }

        .record-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .record-card {
            border: 1px solid #edf1f0;
            border-radius: 20px;
            padding: 16px;
            background: #fbfcfc;
        }

        .record-card.full { grid-column: 1 / -1; }

        .record-label {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #7b8794;
            margin-bottom: 8px;
        }

        .record-value {
            font-size: 14px;
            line-height: 1.75;
            color: #22343a;
            font-weight: 750;
            white-space: pre-line;
        }

        .list { display: grid; gap: 12px; }

        .list-item {
            border: 1px solid #edf1f0;
            border-radius: 20px;
            padding: 16px;
            background: #fbfcfc;
        }

        .list-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
        }

        .item-title {
            font-size: 16px;
            font-weight: 900;
            color: #22343a;
            margin-bottom: 5px;
        }

        .item-meta {
            font-size: 12px;
            line-height: 1.7;
            color: #64748b;
        }

        .pill {
            display: inline-flex;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .pill-green { background: #dcfce7; color: #166534; }
        .pill-orange { background: #fef3c7; color: #92400e; }
        .pill-red { background: #fee2e2; color: #b91c1c; }
        .pill-gray { background: #e5e7eb; color: #374151; }
        .pill-blue { background: #dbeafe; color: #1d4ed8; }
        .pill-violet { background: #ede9fe; color: #6d28d9; }

        .pain-scale {
            display: flex;
            gap: 6px;
            align-items: center;
            margin-top: 10px;
        }

        .pain-dot {
            width: 20px;
            height: 9px;
            border-radius: 999px;
            background: #e5e7eb;
        }

        .pain-dot.active { background: #2f7c7a; }

        .empty-state {
            padding: 18px;
            background: #fff7ed;
            color: #9a3412;
            border: 1px solid #fed7aa;
            border-radius: 18px;
            font-weight: 800;
            line-height: 1.7;
        }

        @media (max-width: 1080px) {
            .hero-grid, .main-grid, .record-grid, .stats-grid { grid-template-columns: 1fr; }
            .record-card.full { grid-column: auto; }
        }

        @media (max-width: 760px) {
            .page { padding: 18px; }
            .title { font-size: 34px; }
            .actions, .btn { width: 100%; }
        }

        @media print {
            body { background: #ffffff; }
            .actions, .btn-soft { display: none; }
            .page { padding: 0; }
            .hero, .section-card { box-shadow: none; border-color: #ddd; }
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
@php
    $mr = $visit->medicalRecord;

    $statusClass = match($visit->status) {
        'completed' => 'pill-green',
        'in_progress' => 'pill-orange',
        'scheduled' => 'pill-blue',
        'cancelled' => 'pill-gray',
        default => 'pill-blue',
    };
@endphp

<div class="page">
    <div class="container">
        <div class="topbar">
            <div>
                <div class="brand">
                    <img src="/images/khayra-logo.png" alt="Khayra Logo">
                    <div>
                        <div class="brand-name">Khayra Physio</div>
                        <div class="muted">Patient Portal</div>
                    </div>
                </div>

                <span class="badge">Visit Detail</span>
                <h1 class="title">Visit {{ $visit->visit_date ?: '-' }}</h1>
                <p class="subtitle">Ringkasan visit, rekam medis yang aman untuk pasien, home exercise, dan progress terapi.</p>
            </div>

            <div class="actions">
                <a href="/patient/dashboard" class="btn btn-soft">← Dashboard</a>
                <button onclick="window.print()" class="btn btn-primary">Print / Save PDF</button>
            </div>
        </div>

        <section class="hero">
            <div class="hero-grid">
                <div class="hero-main">
                    <span class="badge">Therapy Snapshot</span>
                    <h2 class="visit-title">{{ optional($visit->patient)->full_name ?: 'Patient' }}</h2>
                    <p class="subtitle">
                        Therapist: <strong>{{ optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: '-' }}</strong>
                        · Status: <strong>{{ strtoupper($visit->status ?: '-') }}</strong>
                    </p>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-label">Visit Date</div>
                            <div class="stat-value">{{ $visit->visit_date ?: '-' }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Status</div>
                            <div class="stat-value"><span class="pill {{ $statusClass }}">{{ $visit->status ?: '-' }}</span></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Medical Record</div>
                            <div class="stat-value">{{ $mr ? 'Available' : 'Missing' }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Exercise</div>
                            <div class="stat-value">{{ $mr && $mr->homeExercises ? $mr->homeExercises->count() : 0 }}</div>
                        </div>
                    </div>
                </div>

                <aside class="hero-side">
                    <div class="profile-card">
                        <div class="profile-label">Patient</div>
                        <div class="profile-value">{{ optional($visit->patient)->full_name ?: '-' }}</div>
                        <div class="profile-muted">
                            MR: {{ optional($visit->patient)->medical_record_number ?: '-' }}<br>
                            WhatsApp: {{ optional($visit->patient)->whatsapp ?: '-' }}
                        </div>
                    </div>

                    <div class="profile-card">
                        <div class="profile-label">Next Control</div>
                        <div class="profile-value">
                            {{ $mr && $mr->date_of_control ? $mr->date_of_control->format('Y-m-d') : 'Belum dicatat' }}
                        </div>
                        <div class="profile-muted">
                            Frequency: {{ $mr && $mr->frequency_per_week ? $mr->frequency_per_week : '-' }}<br>
                            Total Session: {{ $mr && $mr->total_session ? $mr->total_session : '-' }}
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <div class="main-grid">
            <div>
                <section class="section-card">
                    <h2 class="section-title">Medical Record Summary</h2>
                    <p class="section-subtitle">Ringkasan rekam medis yang dapat dipahami pasien.</p>

                    @if($mr)
                        <div class="record-grid">
                            <div class="record-card">
                                <div class="record-label">Keluhan</div>
                                <div class="record-value">{{ $mr->complaint ?: $mr->condition_felt ?: 'Belum dicatat.' }}</div>
                            </div>

                            <div class="record-card">
                                <div class="record-label">Pain Scale</div>
                                <div class="record-value">
                                    {{ $mr->pain_scale !== null ? $mr->pain_scale . ' / 10' : 'Belum dicatat' }}
                                    <div class="pain-scale">
                                        @for($i = 1; $i <= 10; $i++)
                                            <span class="pain-dot {{ $mr->pain_scale && $i <= $mr->pain_scale ? 'active' : '' }}"></span>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <div class="record-card">
                                <div class="record-label">Assessment</div>
                                <div class="record-value">{{ $mr->assessment ?: $mr->physiotherapy_diagnosis ?: 'Belum dicatat.' }}</div>
                            </div>

                            <div class="record-card">
                                <div class="record-label">Treatment</div>
                                <div class="record-value">{{ $mr->treatment_given ?: $mr->treatment ?: 'Belum dicatat.' }}</div>
                            </div>

                            <div class="record-card">
                                <div class="record-label">Response To Treatment</div>
                                <div class="record-value">{{ $mr->response_to_treatment ?: $mr->progress_note ?: 'Belum dicatat.' }}</div>
                            </div>

                            <div class="record-card">
                                <div class="record-label">Next Plan</div>
                                <div class="record-value">{{ $mr->next_session_plan ?: $mr->recommendation ?: 'Belum dicatat.' }}</div>
                            </div>

                            <div class="record-card">
                                <div class="record-label">Patient Goal</div>
                                <div class="record-value">{{ $mr->patient_goal ?: 'Belum dicatat.' }}</div>
                            </div>

                            <div class="record-card">
                                <div class="record-label">Program Patient</div>
                                <div class="record-value">{{ $mr->program_patient ?: 'Belum dicatat.' }}</div>
                            </div>

                            <div class="record-card full">
                                <div class="record-label">Visit Notes</div>
                                <div class="record-value">{{ $visit->notes ?: 'Belum ada catatan visit.' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">Belum ada rekam medis untuk visit ini.</div>
                    @endif
                </section>

                <section class="section-card">
                    <h2 class="section-title">Home Exercise</h2>
                    <p class="section-subtitle">Latihan rumah dari therapist untuk visit ini.</p>

                    @if($mr && $mr->homeExercises && $mr->homeExercises->count())
                        <div class="list">
                            @foreach($mr->homeExercises as $exercise)
                                <div class="list-item">
                                    <div class="list-top">
                                        <div>
                                            <div class="item-title">{{ $exercise->exercise ?: 'Exercise' }}</div>
                                            <div class="item-meta">Dosage: {{ $exercise->dosage ?: '-' }}</div>
                                        </div>
                                        <span class="pill pill-green">Exercise</span>
                                    </div>
                                    @if($exercise->note_caution)
                                        <div class="item-meta" style="margin-top:10px;">
                                            Catatan: {{ $exercise->note_caution }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">Belum ada home exercise untuk visit ini.</div>
                    @endif
                </section>
            </div>

            <div>
                <section class="section-card">
                    <h2 class="section-title">Vital & Objective</h2>
                    <p class="section-subtitle">Data pemeriksaan ringkas yang tersedia pada rekam medis.</p>

                    @if($mr)
                        <div class="record-grid">
                            <div class="record-card">
                                <div class="record-label">Objective Exam</div>
                                <div class="record-value">{{ $mr->objective_examination ?: 'Belum dicatat.' }}</div>
                            </div>

                            <div class="record-card">
                                <div class="record-label">Subjective Exam</div>
                                <div class="record-value">{{ $mr->subjective_examination ?: 'Belum dicatat.' }}</div>
                            </div>

                            <div class="record-card">
                                <div class="record-label">Blood Pressure</div>
                                <div class="record-value">{{ $mr->blood_pressure ?: '-' }}</div>
                            </div>

                            <div class="record-card">
                                <div class="record-label">Heart Rate</div>
                                <div class="record-value">{{ $mr->heart_rate ?: '-' }}</div>
                            </div>

                            <div class="record-card">
                                <div class="record-label">Temperature</div>
                                <div class="record-value">{{ $mr->temperature ?: '-' }}</div>
                            </div>

                            <div class="record-card">
                                <div class="record-label">BMI</div>
                                <div class="record-value">{{ $mr->bmi ?: '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">Belum ada data pemeriksaan.</div>
                    @endif
                </section>

                <section class="section-card">
                    <h2 class="section-title">Supporting Data</h2>
                    <p class="section-subtitle">Data penunjang jika tercatat.</p>

                    @if($mr && $mr->supportingData && $mr->supportingData->count())
                        <div class="list">
                            @foreach($mr->supportingData as $support)
                                <div class="list-item">
                                    <div class="item-title">{{ $support->data_type ?: 'Supporting Data' }}</div>
                                    <div class="item-meta">
                                        Date: {{ $support->data_date ?: '-' }}<br>
                                        Interpretation: {{ $support->interpretation ?: '-' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">Belum ada supporting data.</div>
                    @endif
                </section>

                <section class="section-card">
                    <h2 class="section-title">Progress Tracking</h2>
                    <p class="section-subtitle">Progress khusus yang dicatat untuk pasien.</p>

                    @forelse($progressEntries as $entry)
                        <div class="list-item" style="margin-bottom:12px;">
                            <div class="item-title">{{ $entry->entry_date ? $entry->entry_date->format('Y-m-d') : 'Progress Entry' }}</div>
                            <div class="item-meta">
                                Pain Scale: {{ is_null($entry->pain_scale) ? '-' : $entry->pain_scale . '/10' }}<br>
                                ROM: {{ $entry->rom_notes ?: '-' }}<br>
                                Goal: {{ $entry->functional_goal ?: '-' }}<br>
                                Progress: {{ $entry->progress_notes ?: '-' }}
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">Belum ada progress tracking untuk visit ini.</div>
                    @endforelse
                </section>

                <section class="section-card">
                    <h2 class="section-title">Medical Record Update History</h2>
                    <p class="section-subtitle">Riwayat update rekam medis untuk visit ini. Setiap therapist menyimpan Medical Record, sistem mencatat snapshot ringkas.</p>

                    @if(($medicalRecordUpdateLogs ?? collect())->count())
                        <div class="list">
                            @foreach($medicalRecordUpdateLogs as $index => $log)
                                <div class="list-item">
                                    <div class="list-top">
                                        <div>
                                            <div class="item-title">Update #{{ $medicalRecordUpdateLogs->count() - $index }}</div>
                                            <div class="item-meta">
                                                {{ $log->snapshot_date ? $log->snapshot_date->format('Y-m-d H:i') : '-' }}
                                                · {{ $log->updated_by_name ?: 'Therapist' }}
                                            </div>
                                        </div>
                                        <span class="pill pill-violet">MR Log</span>
                                    </div>

                                    <div class="item-meta" style="margin-top:12px;">
                                        <strong>Pain Scale:</strong> {{ is_null($log->pain_scale) ? '-' : $log->pain_scale . '/10' }}<br>
                                        <strong>Response:</strong> {{ $log->response_to_treatment ?: '-' }}<br>
                                        <strong>Next Plan:</strong> {{ $log->next_session_plan ?: '-' }}<br>
                                        <strong>Next Control:</strong> {{ $log->date_of_control ? $log->date_of_control->format('Y-m-d') : '-' }}<br>
                                        <strong>Frequency:</strong> {{ $log->frequency_per_week ?: '-' }} ·
                                        <strong>Total Session:</strong> {{ $log->total_session ?: '-' }}
                                    </div>

                                    @if($log->control_plan)
                                        <div class="item-meta" style="margin-top:10px;">
                                            <strong>Control Plan:</strong> {{ $log->control_plan }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">Belum ada history update medical record untuk visit ini. History akan mulai tercatat setelah therapist menyimpan Medical Record berikutnya.</div>
                    @endif
                </section>

                <section class="section-card">
                    <h2 class="section-title">History & Comorbidity</h2>
                    <p class="section-subtitle">Riwayat dan komorbiditas jika tersedia.</p>

                    @if($mr && (($mr->histories && $mr->histories->count()) || ($mr->comorbidities && $mr->comorbidities->count())))
                        <div class="list">
                            @foreach($mr->histories as $history)
                                <div class="list-item">
                                    <div class="item-title">{{ $history->history_type ?: 'History' }}</div>
                                    <div class="item-meta">
                                        {{ $history->history_date ?: '-' }}<br>
                                        {{ $history->history_note ?: '-' }}
                                    </div>
                                </div>
                            @endforeach

                            @foreach($mr->comorbidities as $comorbidity)
                                <div class="list-item">
                                    <div class="item-title">{{ $comorbidity->name ?: 'Comorbidity' }}</div>
                                    <div class="item-meta">
                                        Value: {{ $comorbidity->final_value ?: '-' }}<br>
                                        Note: {{ $comorbidity->note ?: '-' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">Belum ada history / comorbidity yang tercatat.</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
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
