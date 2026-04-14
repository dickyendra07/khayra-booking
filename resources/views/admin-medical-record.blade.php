<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Record Viewer - Khayra Admin</title>
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

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 22px;
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
            max-width: 860px;
        }

        .back-link {
            display: inline-block;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 14px;
            background: white;
            color: #0f766e;
            border: 1px solid #d7ebe6;
            font-weight: 700;
            white-space: nowrap;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.35fr .85fr;
            gap: 18px;
            margin-bottom: 22px;
        }

        .hero-main,
        .hero-side {
            border-radius: 28px;
            padding: 26px;
            box-shadow: 0 18px 42px rgba(15,118,110,.08);
        }

        .hero-main {
            background: linear-gradient(135deg, #0f766e 0%, #2f7f74 100%);
            color: white;
        }

        .hero-side {
            background: white;
            border: 1px solid #e5f1ee;
        }

        .hero-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.16);
            color: white;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .hero-title {
            margin: 0;
            font-size: 36px;
            line-height: 1.08;
            font-weight: 800;
        }

        .hero-text {
            margin: 14px 0 0;
            line-height: 1.8;
            font-size: 15px;
            color: rgba(255,255,255,.92);
            max-width: 760px;
        }

        .hero-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .hero-tag {
            display: inline-block;
            padding: 9px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.18);
            color: white;
            font-size: 13px;
            font-weight: 700;
        }

        .side-title {
            margin: 0;
            font-size: 22px;
            color: #0f766e;
        }

        .side-subtitle {
            margin: 8px 0 18px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        .mini-grid {
            display: grid;
            gap: 12px;
        }

        .mini-box {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
        }

        .mini-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 6px;
        }

        .mini-value {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            line-height: 1.6;
            word-break: break-word;
        }

        .notice {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #cfe0ff;
            border-radius: 16px;
            padding: 14px 16px;
            margin-bottom: 22px;
            line-height: 1.7;
            font-size: 14px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 20px;
        }

        .section-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .section-title {
            margin: 0;
            font-size: 24px;
            color: #0f766e;
        }

        .section-subtitle {
            margin: 8px 0 18px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        .profile-stack {
            display: grid;
            gap: 14px;
        }

        .profile-box,
        .data-box {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
        }

        .profile-label,
        .data-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .profile-value,
        .data-value {
            font-size: 15px;
            color: #111827;
            line-height: 1.7;
            word-break: break-word;
        }

        .viewer-section {
            margin-bottom: 24px;
            padding-bottom: 22px;
            border-bottom: 1px solid #e8f1ef;
        }

        .viewer-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .viewer-title {
            margin: 0 0 8px;
            font-size: 20px;
            color: #0f766e;
            font-weight: 800;
        }

        .viewer-text {
            margin: 0 0 18px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
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

        .status-scheduled { background: #dbeafe; color: #1d4ed8; }
        .status-in_progress { background: #fef3c7; color: #92400e; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        .empty-block {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            padding: 18px;
            color: #64748b;
            text-align: center;
            line-height: 1.7;
        }

        .repeat-wrap {
            display: grid;
            gap: 14px;
        }

        .repeat-item {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
        }

        .repeat-head {
            font-size: 14px;
            font-weight: 800;
            color: #0f766e;
            margin-bottom: 12px;
        }

        .spacer {
            margin-top: 14px;
        }

        @media (max-width: 1180px) {
            .hero,
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .grid-2,
            .grid-3 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 1024px) {
            .layout {
                display: block;
            }

            .main {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 32px;
            }

            .hero-main,
            .hero-side,
            .section-card {
                padding: 20px;
                border-radius: 22px;
            }
        }
    </style>
</head>
<body>
@php
    $record = $visit->medicalRecord;

    $histories = $record && $record->histories && $record->histories->count()
        ? $record->histories
        : collect();

    $comorbidities = $record && $record->comorbidities && $record->comorbidities->count()
        ? $record->comorbidities
        : collect();

    $supportingData = $record && $record->supportingData && $record->supportingData->count()
        ? $record->supportingData
        : collect();

    $homeExercises = $record && $record->homeExercises && $record->homeExercises->count()
        ? $record->homeExercises
        : collect();
@endphp

<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'visits'])

    <main class="main">
        <div class="topbar">
            <div>
                <h1 class="page-title">Medical Record Viewer</h1>
                <p class="page-subtitle">
                    Halaman ini dipakai admin untuk melihat hasil clinical assessment yang diisi therapist.
                    Admin bersifat monitoring, sedangkan pengisian rekam medis tetap menjadi domain therapist.
                </p>
            </div>
            <a href="/admin/visits" class="back-link">← Kembali ke Visits</a>
        </div>

        <section class="hero">
            <div class="hero-main">
                <div class="hero-badge">Viewer V2</div>
                <h2 class="hero-title">Clinical record untuk {{ $visit->patient->full_name ?? 'Patient' }}</h2>
                <p class="hero-text">
                    Admin dapat memantau isi rekam medis therapist secara lengkap untuk kebutuhan operasional,
                    follow up, dan koordinasi internal tanpa mengambil alih pengisian klinis.
                </p>

                <div class="hero-tags">
                    <span class="hero-tag">Visit #{{ $visit->id }}</span>
                    <span class="hero-tag">{{ $visit->visit_date ?: '-' }}</span>
                    <span class="hero-tag">{{ $visit->therapistRelation->full_name ?? $visit->therapist ?? '-' }}</span>
                </div>
            </div>

            <div class="hero-side">
                <h2 class="side-title">Ringkasan Visit</h2>
                <p class="side-subtitle">Informasi singkat visit yang sedang dipantau admin.</p>

                <div class="mini-grid">
                    <div class="mini-box">
                        <div class="mini-label">Patient</div>
                        <div class="mini-value">{{ $visit->patient->full_name ?? '-' }}</div>
                    </div>

                    <div class="mini-box">
                        <div class="mini-label">Visit Date</div>
                        <div class="mini-value">{{ $visit->visit_date ?: '-' }}</div>
                    </div>

                    <div class="mini-box">
                        <div class="mini-label">Status</div>
                        <div class="mini-value">
                            <span class="status-pill status-{{ $visit->status }}">{{ str_replace('_', ' ', $visit->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="notice">
            Viewer ini hanya untuk monitoring admin. Update clinical assessment dan report tetap dilakukan dari akun therapist.
        </div>

        <div class="content-grid">
            <aside class="section-card">
                <h2 class="section-title">Patient Summary</h2>
                <p class="section-subtitle">Informasi dasar patient dan visit aktif.</p>

                <div class="profile-stack">
                    <div class="profile-box">
                        <div class="profile-label">Patient</div>
                        <div class="profile-value">{{ $visit->patient->full_name ?? '-' }}</div>
                    </div>

                    <div class="profile-box">
                        <div class="profile-label">Visit Date</div>
                        <div class="profile-value">{{ $visit->visit_date ?: '-' }}</div>
                    </div>

                    <div class="profile-box">
                        <div class="profile-label">Therapist</div>
                        <div class="profile-value">{{ $visit->therapistRelation->full_name ?? $visit->therapist ?? '-' }}</div>
                    </div>

                    <div class="profile-box">
                        <div class="profile-label">Status</div>
                        <div class="profile-value">{{ $visit->status ?: '-' }}</div>
                    </div>

                    <div class="profile-box">
                        <div class="profile-label">Visit Notes</div>
                        <div class="profile-value">{{ $visit->notes ?: '-' }}</div>
                    </div>
                </div>
            </aside>

            <section class="section-card">
                <h2 class="section-title">Clinical Notes V2</h2>
                <p class="section-subtitle">
                    Tampilan lengkap hasil clinical assessment therapist untuk kebutuhan koordinasi dan monitoring admin.
                </p>

                <div class="viewer-section">
                    <h3 class="viewer-title">1. Chief Complaint & Pain Profile</h3>
                    <p class="viewer-text">Keluhan utama, onset, nyeri, dan keterbatasan fungsi awal.</p>

                    <div class="grid-2">
                        <div class="data-box">
                            <div class="data-label">Complaint</div>
                            <div class="data-value">{{ $record->complaint ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Onset</div>
                            <div class="data-value">{{ $record->onset ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="grid-3 spacer">
                        <div class="data-box">
                            <div class="data-label">Pain Scale</div>
                            <div class="data-value">{{ $record->pain_scale ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Pain Type</div>
                            <div class="data-value">{{ $record->pain_type ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Functional Limitation (Initial)</div>
                            <div class="data-value">{{ $record->functional_limitation_initial ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="grid-2 spacer">
                        <div class="data-box">
                            <div class="data-label">Condition Felt</div>
                            <div class="data-value">{{ $record->condition_felt ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Pain Body Chart Note</div>
                            <div class="data-value">{{ $record->pain_body_chart_note ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">2. Medical History</h3>
                    <p class="viewer-text">Riwayat injury, surgery, fracture, stroke, malignancy, dan riwayat lain.</p>

                    @if($histories->count())
                        <div class="repeat-wrap">
                            @foreach($histories as $history)
                                <div class="repeat-item">
                                    <div class="repeat-head">{{ $history->history_type ?: 'History Item' }}</div>
                                    <div class="grid-2">
                                        <div class="data-box">
                                            <div class="data-label">History Date</div>
                                            <div class="data-value">{{ $history->history_date ?: '-' }}</div>
                                        </div>
                                        <div class="data-box">
                                            <div class="data-label">History Note</div>
                                            <div class="data-value">{{ $history->history_note ?: '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-block">Belum ada data medical history.</div>
                    @endif
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">3. Comorbidities</h3>
                    <p class="viewer-text">Komorbid yang dicatat therapist berikut status dan hasil pengukuran.</p>

                    @if($comorbidities->count())
                        <div class="repeat-wrap">
                            @foreach($comorbidities as $comorbidity)
                                <div class="repeat-item">
                                    <div class="repeat-head">{{ $comorbidity->name ?: 'Comorbidity Item' }}</div>
                                    <div class="grid-3">
                                        <div class="data-box">
                                            <div class="data-label">Checked</div>
                                            <div class="data-value">{{ $comorbidity->is_checked ? 'Yes' : 'No' }}</div>
                                        </div>
                                        <div class="data-box">
                                            <div class="data-label">Measurement Date</div>
                                            <div class="data-value">{{ $comorbidity->measurement_date ?: '-' }}</div>
                                        </div>
                                        <div class="data-box">
                                            <div class="data-label">Final Value</div>
                                            <div class="data-value">{{ $comorbidity->final_value ?: '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="data-box spacer">
                                        <div class="data-label">Note</div>
                                        <div class="data-value">{{ $comorbidity->note ?: '-' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-block">Belum ada data comorbidities.</div>
                    @endif
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">4. Vital Signs</h3>
                    <p class="viewer-text">Ringkasan vital signs patient pada assessment.</p>

                    <div class="grid-3">
                        <div class="data-box">
                            <div class="data-label">Blood Pressure</div>
                            <div class="data-value">{{ $record->blood_pressure ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Temperature</div>
                            <div class="data-value">{{ $record->temperature ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Respiration Rate</div>
                            <div class="data-value">{{ $record->respiration_rate ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Heart Rate</div>
                            <div class="data-value">{{ $record->heart_rate ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Weight</div>
                            <div class="data-value">{{ $record->weight ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Height</div>
                            <div class="data-value">{{ $record->height ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="data-box spacer">
                        <div class="data-label">BMI</div>
                        <div class="data-value">{{ $record->bmi ?? '-' }}</div>
                    </div>
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">5. Supporting Data Result</h3>
                    <p class="viewer-text">Data penunjang seperti X-ray, MRI, lab, dan interpretasi singkat.</p>

                    @if($supportingData->count())
                        <div class="repeat-wrap">
                            @foreach($supportingData as $item)
                                <div class="repeat-item">
                                    <div class="grid-3">
                                        <div class="data-box">
                                            <div class="data-label">Date</div>
                                            <div class="data-value">{{ $item->data_date ?: '-' }}</div>
                                        </div>

                                        <div class="data-box">
                                            <div class="data-label">Type of Data</div>
                                            <div class="data-value">{{ $item->data_type ?: '-' }}</div>
                                        </div>

                                        <div class="data-box">
                                            <div class="data-label">Interpretation</div>
                                            <div class="data-value">{{ $item->interpretation ?: '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-block">Belum ada supporting data.</div>
                    @endif
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">6. Subjective & Objective Examination</h3>
                    <p class="viewer-text">Hasil subjective exam, objective exam, dan parameter klinis terkait.</p>

                    <div class="grid-2">
                        <div class="data-box">
                            <div class="data-label">Subjective Examination</div>
                            <div class="data-value">{{ $record->subjective_examination ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Objective Examination</div>
                            <div class="data-value">{{ $record->objective_examination ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="grid-3 spacer">
                        <div class="data-box">
                            <div class="data-label">Severity Level</div>
                            <div class="data-value">{{ $record->severity_level ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Irritability Level</div>
                            <div class="data-value">{{ $record->irritability_level ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Nature Type</div>
                            <div class="data-value">{{ $record->nature_type ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="grid-2 spacer">
                        <div class="data-box">
                            <div class="data-label">Easing Factors</div>
                            <div class="data-value">{{ $record->easing_factors ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Aggravating Factors</div>
                            <div class="data-value">{{ $record->aggravating_factors ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="data-box spacer">
                        <div class="data-label">Special Test Notes</div>
                        <div class="data-value">{{ $record->special_test_notes ?? '-' }}</div>
                    </div>
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">7. Diagnosis & Clinical Decision</h3>
                    <p class="viewer-text">Diagnosis fisioterapi, impairment, goal, referral, dan rencana program.</p>

                    <div class="data-box">
                        <div class="data-label">Physiotherapy Diagnosis</div>
                        <div class="data-value">{{ $record->physiotherapy_diagnosis ?? '-' }}</div>
                    </div>

                    <div class="grid-2 spacer">
                        <div class="data-box">
                            <div class="data-label">Impairment</div>
                            <div class="data-value">{{ $record->impairment ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Functional Limitation (Clinical)</div>
                            <div class="data-value">{{ $record->functional_limitation_clinical ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="grid-2 spacer">
                        <div class="data-box">
                            <div class="data-label">Patient Goal</div>
                            <div class="data-value">{{ $record->patient_goal ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Referral</div>
                            <div class="data-value">{{ $record->referral ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="data-box spacer">
                        <div class="data-label">Program Patient</div>
                        <div class="data-value">{{ $record->program_patient ?? '-' }}</div>
                    </div>

                    <div class="grid-3 spacer">
                        <div class="data-box">
                            <div class="data-label">Date of Control</div>
                            <div class="data-value">{{ $record->date_of_control ? $record->date_of_control->format('Y-m-d') : '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Total Session</div>
                            <div class="data-value">{{ $record->total_session ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Frequency per Week</div>
                            <div class="data-value">{{ $record->frequency_per_week ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="data-box spacer">
                        <div class="data-label">Control Plan</div>
                        <div class="data-value">{{ $record->control_plan ?? '-' }}</div>
                    </div>
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">8. Health Management</h3>
                    <p class="viewer-text">Catatan edukasi dan manajemen pendukung terapi.</p>

                    <div class="grid-3">
                        <div class="data-box">
                            <div class="data-label">Diet / Nutrition</div>
                            <div class="data-value">{{ $record->diet_nutrition ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Lifestyle</div>
                            <div class="data-value">{{ $record->lifestyle ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Flare-up Management</div>
                            <div class="data-value">{{ $record->flare_up_management ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">9. Home Exercise Program</h3>
                    <p class="viewer-text">Latihan rumah yang diberikan therapist kepada patient.</p>

                    @if($homeExercises->count())
                        <div class="repeat-wrap">
                            @foreach($homeExercises as $exercise)
                                <div class="repeat-item">
                                    <div class="grid-3">
                                        <div class="data-box">
                                            <div class="data-label">Exercise</div>
                                            <div class="data-value">{{ $exercise->exercise ?? '-' }}</div>
                                        </div>

                                        <div class="data-box">
                                            <div class="data-label">Dosage</div>
                                            <div class="data-value">{{ $exercise->dosage ?? '-' }}</div>
                                        </div>

                                        <div class="data-box">
                                            <div class="data-label">Note / Caution</div>
                                            <div class="data-value">{{ $exercise->note_caution ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-block">Belum ada home exercise program.</div>
                    @endif
                </div>

                <div class="viewer-section">
                    <h3 class="viewer-title">10. Session Progress</h3>
                    <p class="viewer-text">Ringkasan progress dan intervensi sesi yang dicatat therapist.</p>

                    <div class="grid-2">
                        <div class="data-box">
                            <div class="data-label">Assessment (Legacy)</div>
                            <div class="data-value">{{ $record->assessment ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Treatment (Legacy)</div>
                            <div class="data-value">{{ $record->treatment ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="grid-2 spacer">
                        <div class="data-box">
                            <div class="data-label">Treatment Given</div>
                            <div class="data-value">{{ $record->treatment_given ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Progress Note</div>
                            <div class="data-value">{{ $record->progress_note ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="grid-2 spacer">
                        <div class="data-box">
                            <div class="data-label">Response to Treatment</div>
                            <div class="data-value">{{ $record->response_to_treatment ?? '-' }}</div>
                        </div>

                        <div class="data-box">
                            <div class="data-label">Recommendation</div>
                            <div class="data-value">{{ $record->recommendation ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="data-box spacer">
                        <div class="data-label">Next Session Plan</div>
                        <div class="data-value">{{ $record->next_session_plan ?? '-' }}</div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>