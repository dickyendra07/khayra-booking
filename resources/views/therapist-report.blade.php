<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapist Report - Khayra Physio</title>
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

        .page {
            min-height: 100vh;
            padding: 24px 20px 40px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 20px;
        }

        .brand {
            font-size: 28px;
            font-weight: 800;
            color: #0f766e;
        }

        .top-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ghost-link,
        .print-link {
            display: inline-block;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
        }

        .ghost-link {
            background: white;
            color: #0f766e;
            border: 1px solid #d7ebe6;
        }

        .print-link {
            background: #0f766e;
            color: white;
            border: 1px solid #0f766e;
        }

        .hero {
            background: linear-gradient(135deg, #0f766e 0%, #2f7f74 100%);
            color: white;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 18px 42px rgba(15,118,110,.08);
            margin-bottom: 22px;
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
            line-height: 1.1;
            font-weight: 800;
        }

        .hero-text {
            margin: 12px 0 0;
            line-height: 1.8;
            font-size: 15px;
            color: rgba(255,255,255,.92);
            max-width: 800px;
        }

        .section-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
            margin-bottom: 18px;
        }

        .section-title {
            margin: 0 0 8px;
            font-size: 22px;
            color: #0f766e;
            font-weight: 800;
        }

        .section-subtitle {
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

        .data-box {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
        }

        .data-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .data-value {
            font-size: 15px;
            color: #111827;
            line-height: 1.7;
            word-break: break-word;
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

        .empty-block {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            padding: 18px;
            color: #64748b;
            text-align: center;
            line-height: 1.7;
        }

        .spacer {
            margin-top: 14px;
        }

        @media (max-width: 900px) {
            .grid-2,
            .grid-3 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .page {
                padding: 16px 14px 32px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero {
                padding: 22px;
                border-radius: 22px;
            }

            .hero-title {
                font-size: 30px;
            }

            .section-card {
                padding: 20px;
                border-radius: 22px;
            }

            .brand {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
@php
    $record = $visit->medicalRecord;
    $histories = $record && $record->histories ? $record->histories : collect();
    $comorbidities = $record && $record->comorbidities ? $record->comorbidities : collect();
    $supportingData = $record && $record->supportingData ? $record->supportingData : collect();
    $homeExercises = $record && $record->homeExercises ? $record->homeExercises : collect();
@endphp

<div class="page">
    <div class="container">
        <div class="topbar">
            <div class="brand">Khayra Therapist Report</div>
            <div class="top-actions">
                <a href="/therapist/dashboard" class="ghost-link">← Dashboard</a>
                <a href="/therapist/visits/{{ $visit->id }}/medical-record" class="ghost-link">Medical Record</a>
                <a href="/therapist/visits/{{ $visit->id }}/report/print" target="_blank" class="print-link">Print View</a>
            </div>
        </div>

        <section class="hero">
            <div class="hero-badge">Physiotherapy Report</div>
            <h1 class="hero-title">Report untuk {{ $visit->patient->full_name ?? 'Patient' }}</h1>
            <p class="hero-text">
                Ringkasan clinical assessment therapist berdasarkan Medical Record V2 untuk kebutuhan dokumentasi dan tindak lanjut terapi.
            </p>
        </section>

        <section class="section-card">
            <h2 class="section-title">Patient Identity</h2>
            <p class="section-subtitle">Informasi dasar patient dan visit.</p>

            <div class="grid-3">
                <div class="data-box">
                    <div class="data-label">Patient</div>
                    <div class="data-value">{{ $visit->patient->full_name ?? '-' }}</div>
                </div>
                <div class="data-box">
                    <div class="data-label">Visit Date</div>
                    <div class="data-value">{{ $visit->visit_date ?? '-' }}</div>
                </div>
                <div class="data-box">
                    <div class="data-label">Therapist</div>
                    <div class="data-value">{{ $visit->therapistRelation->full_name ?? $visit->therapist ?? '-' }}</div>
                </div>
            </div>
        </section>

        <section class="section-card">
            <h2 class="section-title">Chief Complaint & Examination</h2>
            <p class="section-subtitle">Keluhan utama, examination, dan parameter klinis penting.</p>

            <div class="grid-2">
                <div class="data-box">
                    <div class="data-label">Complaint</div>
                    <div class="data-value">{{ $record->complaint ?? '-' }}</div>
                </div>
                <div class="data-box">
                    <div class="data-label">Subjective Examination</div>
                    <div class="data-value">{{ $record->subjective_examination ?? '-' }}</div>
                </div>
            </div>

            <div class="grid-2 spacer">
                <div class="data-box">
                    <div class="data-label">Objective Examination</div>
                    <div class="data-value">{{ $record->objective_examination ?? '-' }}</div>
                </div>
                <div class="data-box">
                    <div class="data-label">Special Test Notes</div>
                    <div class="data-value">{{ $record->special_test_notes ?? '-' }}</div>
                </div>
            </div>

            <div class="grid-3 spacer">
                <div class="data-box">
                    <div class="data-label">Severity</div>
                    <div class="data-value">{{ $record->severity_level ?? '-' }}</div>
                </div>
                <div class="data-box">
                    <div class="data-label">Irritability</div>
                    <div class="data-value">{{ $record->irritability_level ?? '-' }}</div>
                </div>
                <div class="data-box">
                    <div class="data-label">Nature</div>
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
        </section>

        <section class="section-card">
            <h2 class="section-title">Vital Signs</h2>
            <p class="section-subtitle">Hasil pemeriksaan vital sign saat assessment.</p>

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
                    <div class="data-label">Height / BMI</div>
                    <div class="data-value">{{ ($record->height ?? '-') }} / {{ ($record->bmi ?? '-') }}</div>
                </div>
            </div>
        </section>

        <section class="section-card">
            <h2 class="section-title">Supporting Data</h2>
            <p class="section-subtitle">Data penunjang dan interpretasi singkat.</p>

            @if($supportingData->count())
                <div class="repeat-wrap">
                    @foreach($supportingData as $item)
                        <div class="repeat-item">
                            <div class="grid-3">
                                <div class="data-box">
                                    <div class="data-label">Date</div>
                                    <div class="data-value">{{ $item->data_date ?? '-' }}</div>
                                </div>
                                <div class="data-box">
                                    <div class="data-label">Type</div>
                                    <div class="data-value">{{ $item->data_type ?? '-' }}</div>
                                </div>
                                <div class="data-box">
                                    <div class="data-label">Interpretation</div>
                                    <div class="data-value">{{ $item->interpretation ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-block">Belum ada supporting data.</div>
            @endif
        </section>

        <section class="section-card">
            <h2 class="section-title">Diagnosis & Clinical Decision</h2>
            <p class="section-subtitle">Diagnosis fisioterapi, impairment, goal, dan program.</p>

            <div class="grid-2">
                <div class="data-box">
                    <div class="data-label">Physiotherapy Diagnosis</div>
                    <div class="data-value">{{ $record->physiotherapy_diagnosis ?? '-' }}</div>
                </div>
                <div class="data-box">
                    <div class="data-label">Impairment</div>
                    <div class="data-value">{{ $record->impairment ?? '-' }}</div>
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
                    <div class="data-label">Frequency</div>
                    <div class="data-value">{{ $record->frequency_per_week ?? '-' }}</div>
                </div>
            </div>
        </section>

        <section class="section-card">
            <h2 class="section-title">Health Management</h2>
            <p class="section-subtitle">Rangkuman diet, lifestyle, flare-up, dan home exercise.</p>

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

            <div class="spacer"></div>

            @if($homeExercises->count())
                <div class="repeat-wrap">
                    @foreach($homeExercises as $exercise)
                        <div class="repeat-item">
                            <div class="repeat-head">{{ $exercise->exercise ?? 'Exercise' }}</div>
                            <div class="grid-2">
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
        </section>

        <section class="section-card">
            <h2 class="section-title">Session Progress</h2>
            <p class="section-subtitle">Ringkasan intervensi dan perkembangan sesi.</p>

            <div class="grid-2">
                <div class="data-box">
                    <div class="data-label">Treatment Given</div>
                    <div class="data-value">{{ $record->treatment_given ?? $record->treatment ?? '-' }}</div>
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
                    <div class="data-label">Recommendation / Next Plan</div>
                    <div class="data-value">{{ $record->next_session_plan ?? $record->recommendation ?? '-' }}</div>
                </div>
            </div>
        </section>
    </div>
</div>
</body>
</html>