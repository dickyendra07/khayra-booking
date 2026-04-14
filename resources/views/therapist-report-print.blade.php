<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Physiotherapy Report Print - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #111827;
            background: white;
        }

        .page {
            max-width: 980px;
            margin: 0 auto;
            padding: 28px;
        }

        .printbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .print-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-link,
        .print-btn {
            display: inline-block;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
        }

        .action-link {
            border: 1px solid #d1d5db;
            color: #111827;
            background: white;
        }

        .print-btn {
            border: none;
            background: #111827;
            color: white;
            cursor: pointer;
        }

        .report-header {
            border: 2px solid #0f766e;
            border-radius: 18px;
            padding: 22px;
            margin-bottom: 18px;
        }

        .report-brand {
            font-size: 28px;
            font-weight: 800;
            color: #0f766e;
        }

        .report-title {
            margin: 10px 0 0;
            font-size: 28px;
            font-weight: 800;
            color: #111827;
        }

        .report-subtitle {
            margin: 10px 0 0;
            color: #4b5563;
            line-height: 1.7;
            font-size: 14px;
        }

        .section {
            border: 1px solid #dbe5e3;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 16px;
        }

        .section-title {
            margin: 0 0 14px;
            font-size: 18px;
            color: #0f766e;
            font-weight: 800;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .item {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px;
            background: #fafafa;
        }

        .item-label {
            font-size: 11px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 6px;
        }

        .item-value {
            font-size: 14px;
            color: #111827;
            line-height: 1.7;
            word-break: break-word;
        }

        .repeat-item {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px;
            background: #fafafa;
            margin-bottom: 12px;
        }

        .repeat-head {
            font-size: 14px;
            font-weight: 800;
            color: #0f766e;
            margin-bottom: 10px;
        }

        .empty {
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 14px;
            color: #64748b;
            text-align: center;
            background: #f8fafc;
        }

        @media print {
            .printbar {
                display: none;
            }

            .page {
                max-width: 100%;
                padding: 0;
            }

            body {
                background: white;
            }

            .section,
            .report-header,
            .item,
            .repeat-item {
                break-inside: avoid;
            }
        }

        @media (max-width: 768px) {
            .grid-2,
            .grid-3 {
                grid-template-columns: 1fr;
            }

            .page {
                padding: 18px;
            }

            .printbar {
                flex-direction: column;
                align-items: flex-start;
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
    <div class="printbar">
        <div class="report-brand">Khayra Physio</div>
        <div class="print-actions">
            <a href="/therapist/visits/{{ $visit->id }}/report" class="action-link">← Kembali ke Report</a>
            <button onclick="window.print()" class="print-btn">Print / Save PDF</button>
        </div>
    </div>

    <div class="report-header">
        <div class="report-brand">Khayra Physio</div>
        <h1 class="report-title">Physiotherapy Medical Report</h1>
        <p class="report-subtitle">
            Ringkasan clinical assessment therapist berdasarkan Medical Record V2.
        </p>
    </div>

    <section class="section">
        <h2 class="section-title">Patient Identity</h2>
        <div class="grid-3">
            <div class="item">
                <div class="item-label">Patient</div>
                <div class="item-value">{{ $visit->patient->full_name ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Visit Date</div>
                <div class="item-value">{{ $visit->visit_date ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Therapist</div>
                <div class="item-value">{{ $visit->therapistRelation->full_name ?? $visit->therapist ?? '-' }}</div>
            </div>
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Chief Complaint & Examination</h2>
        <div class="grid-2">
            <div class="item">
                <div class="item-label">Complaint</div>
                <div class="item-value">{{ $record->complaint ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Subjective Examination</div>
                <div class="item-value">{{ $record->subjective_examination ?? '-' }}</div>
            </div>
        </div>

        <div class="grid-2" style="margin-top:12px;">
            <div class="item">
                <div class="item-label">Objective Examination</div>
                <div class="item-value">{{ $record->objective_examination ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Special Test Notes</div>
                <div class="item-value">{{ $record->special_test_notes ?? '-' }}</div>
            </div>
        </div>

        <div class="grid-3" style="margin-top:12px;">
            <div class="item">
                <div class="item-label">Severity</div>
                <div class="item-value">{{ $record->severity_level ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Irritability</div>
                <div class="item-value">{{ $record->irritability_level ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Nature</div>
                <div class="item-value">{{ $record->nature_type ?? '-' }}</div>
            </div>
        </div>

        <div class="grid-2" style="margin-top:12px;">
            <div class="item">
                <div class="item-label">Easing Factors</div>
                <div class="item-value">{{ $record->easing_factors ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Aggravating Factors</div>
                <div class="item-value">{{ $record->aggravating_factors ?? '-' }}</div>
            </div>
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Vital Signs</h2>
        <div class="grid-3">
            <div class="item">
                <div class="item-label">Blood Pressure</div>
                <div class="item-value">{{ $record->blood_pressure ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Temperature</div>
                <div class="item-value">{{ $record->temperature ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Respiration Rate</div>
                <div class="item-value">{{ $record->respiration_rate ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Heart Rate</div>
                <div class="item-value">{{ $record->heart_rate ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Weight</div>
                <div class="item-value">{{ $record->weight ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Height / BMI</div>
                <div class="item-value">{{ ($record->height ?? '-') }} / {{ ($record->bmi ?? '-') }}</div>
            </div>
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Supporting Data</h2>
        @if($supportingData->count())
            @foreach($supportingData as $item)
                <div class="repeat-item">
                    <div class="grid-3">
                        <div class="item">
                            <div class="item-label">Date</div>
                            <div class="item-value">{{ $item->data_date ?? '-' }}</div>
                        </div>
                        <div class="item">
                            <div class="item-label">Type</div>
                            <div class="item-value">{{ $item->data_type ?? '-' }}</div>
                        </div>
                        <div class="item">
                            <div class="item-label">Interpretation</div>
                            <div class="item-value">{{ $item->interpretation ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty">Belum ada supporting data.</div>
        @endif
    </section>

    <section class="section">
        <h2 class="section-title">Diagnosis & Clinical Decision</h2>
        <div class="grid-2">
            <div class="item">
                <div class="item-label">Physiotherapy Diagnosis</div>
                <div class="item-value">{{ $record->physiotherapy_diagnosis ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Impairment</div>
                <div class="item-value">{{ $record->impairment ?? '-' }}</div>
            </div>
        </div>

        <div class="grid-2" style="margin-top:12px;">
            <div class="item">
                <div class="item-label">Patient Goal</div>
                <div class="item-value">{{ $record->patient_goal ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Referral</div>
                <div class="item-value">{{ $record->referral ?? '-' }}</div>
            </div>
        </div>

        <div class="item" style="margin-top:12px;">
            <div class="item-label">Program Patient</div>
            <div class="item-value">{{ $record->program_patient ?? '-' }}</div>
        </div>

        <div class="grid-3" style="margin-top:12px;">
            <div class="item">
                <div class="item-label">Date of Control</div>
                <div class="item-value">{{ $record->date_of_control ? $record->date_of_control->format('Y-m-d') : '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Total Session</div>
                <div class="item-value">{{ $record->total_session ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Frequency</div>
                <div class="item-value">{{ $record->frequency_per_week ?? '-' }}</div>
            </div>
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Health Management & Home Exercise</h2>
        <div class="grid-3">
            <div class="item">
                <div class="item-label">Diet / Nutrition</div>
                <div class="item-value">{{ $record->diet_nutrition ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Lifestyle</div>
                <div class="item-value">{{ $record->lifestyle ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Flare-up Management</div>
                <div class="item-value">{{ $record->flare_up_management ?? '-' }}</div>
            </div>
        </div>

        <div style="margin-top:14px;"></div>

        @if($homeExercises->count())
            @foreach($homeExercises as $exercise)
                <div class="repeat-item">
                    <div class="repeat-head">{{ $exercise->exercise ?? 'Exercise' }}</div>
                    <div class="grid-2">
                        <div class="item">
                            <div class="item-label">Dosage</div>
                            <div class="item-value">{{ $exercise->dosage ?? '-' }}</div>
                        </div>
                        <div class="item">
                            <div class="item-label">Note / Caution</div>
                            <div class="item-value">{{ $exercise->note_caution ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty">Belum ada home exercise program.</div>
        @endif
    </section>

    <section class="section">
        <h2 class="section-title">Session Progress</h2>
        <div class="grid-2">
            <div class="item">
                <div class="item-label">Treatment Given</div>
                <div class="item-value">{{ $record->treatment_given ?? $record->treatment ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Progress Note</div>
                <div class="item-value">{{ $record->progress_note ?? '-' }}</div>
            </div>
        </div>

        <div class="grid-2" style="margin-top:12px;">
            <div class="item">
                <div class="item-label">Response to Treatment</div>
                <div class="item-value">{{ $record->response_to_treatment ?? '-' }}</div>
            </div>
            <div class="item">
                <div class="item-label">Recommendation / Next Plan</div>
                <div class="item-value">{{ $record->next_session_plan ?? $record->recommendation ?? '-' }}</div>
            </div>
        </div>
    </section>
</div>
</body>
</html>