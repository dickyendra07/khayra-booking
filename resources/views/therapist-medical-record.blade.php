<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Record V2 - Khayra Physio</title>
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
            max-width: 1380px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .brand {
            font-size: 28px;
            font-weight: 800;
            color: #0f766e;
        }

        .topbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ghost-link {
            display: inline-block;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 12px;
            background: white;
            color: #0f766e;
            border: 1px solid #d7ebe6;
            font-size: 14px;
            font-weight: 700;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.35fr .85fr;
            gap: 18px;
            margin-bottom: 20px;
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
            font-size: 38px;
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

        .alert {
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 18px;
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

        .profile-box {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
        }

        .profile-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .profile-value {
            font-size: 15px;
            color: #111827;
            line-height: 1.6;
            word-break: break-word;
        }

        .form-section {
            margin-bottom: 24px;
            padding-bottom: 22px;
            border-bottom: 1px solid #e8f1ef;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .form-section-title {
            margin: 0 0 8px;
            font-size: 20px;
            color: #0f766e;
            font-weight: 800;
        }

        .form-section-text {
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

        .field {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
        }

        label {
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #374151;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #d7dedd;
            border-radius: 14px;
            font-size: 14px;
            background: #fcfefd;
            color: #111827;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
            line-height: 1.7;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #0f766e;
            box-shadow: 0 0 0 4px rgba(15,118,110,.08);
        }

        .helper {
            margin-top: 7px;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.6;
        }

        .inline-card {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .inline-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .inline-title {
            font-size: 14px;
            font-weight: 800;
            color: #0f766e;
        }

        .tiny-btn,
        .remove-btn,
        .submit-btn {
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 800;
        }

        .tiny-btn {
            padding: 10px 14px;
            background: #0f766e;
            color: white;
            font-size: 13px;
            margin-top: 6px;
        }

        .remove-btn {
            padding: 8px 10px;
            background: #111827;
            color: white;
            font-size: 12px;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 48px;
            padding: 0 2px;
            font-size: 14px;
            color: #374151;
            font-weight: 700;
        }

        .checkbox-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        .submit-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .submit-btn {
            padding: 15px 22px;
            background: #0f766e;
            color: white;
            font-size: 15px;
            box-shadow: 0 12px 26px rgba(15,118,110,.16);
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

        @media (max-width: 768px) {
            .page {
                padding: 16px 14px 32px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero-title {
                font-size: 30px;
            }

            .hero-main,
            .hero-side,
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

    $histories = $record && $record->histories && $record->histories->count()
        ? $record->histories
        : collect([(object)[
            'history_type' => '',
            'history_note' => '',
            'history_date' => '',
        ]]);

    $comorbidities = $record && $record->comorbidities && $record->comorbidities->count()
        ? $record->comorbidities
        : collect([(object)[
            'name' => '',
            'is_checked' => false,
            'measurement_date' => '',
            'final_value' => '',
            'note' => '',
        ]]);

    $supportingData = $record && $record->supportingData && $record->supportingData->count()
        ? $record->supportingData
        : collect([(object)[
            'data_date' => '',
            'data_type' => '',
            'interpretation' => '',
        ]]);

    $homeExercises = $record && $record->homeExercises && $record->homeExercises->count()
        ? $record->homeExercises
        : collect([(object)[
            'exercise' => '',
            'dosage' => '',
            'note_caution' => '',
        ]]);
@endphp

<div class="page">
    <div class="container">
        <div class="topbar">
            <div class="brand">Khayra Therapist Dashboard</div>
            <div class="topbar-actions">
                <a href="/therapist/dashboard" class="ghost-link">← Kembali ke Dashboard</a>
            </div>
        </div>

        <section class="hero">
            <div class="hero-main">
                <div class="hero-badge">Medical Record V2</div>
                <h1 class="hero-title">Clinical assessment untuk {{ $visit->patient->full_name ?? 'Patient' }}</h1>
                <p class="hero-text">
                    Isi rekam medis secara lebih lengkap dan terstruktur sesuai alur fisioterapi:
                    complaint, examination, diagnosis, planning, dan home exercise program.
                </p>

                <div class="hero-tags">
                    <span class="hero-tag">Visit #{{ $visit->id }}</span>
                    <span class="hero-tag">{{ $visit->visit_date ?: '-' }}</span>
                    <span class="hero-tag">{{ $visit->status ?: '-' }}</span>
                </div>
            </div>

            <div class="hero-side">
                <h2 class="side-title">Ringkasan Visit</h2>
                <p class="side-subtitle">Informasi singkat visit yang sedang Anda tangani.</p>

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
                        <div class="mini-label">Therapist</div>
                        <div class="mini-value">{{ $visit->therapistRelation->full_name ?? $visit->therapist ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <strong>Periksa kembali input Anda:</strong>
                <ul style="margin:8px 0 0 18px; padding:0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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
                    Lengkapi assessment dan intervensi secara lebih terstruktur untuk kebutuhan klinis dan report therapist.
                </p>

                <form method="POST" action="/therapist/visits/{{ $visit->id }}/medical-record">
                    @csrf

                    <div class="form-section">
                        <h3 class="form-section-title">1. Chief Complaint & Pain Profile</h3>
                        <p class="form-section-text">Keluhan utama, onset, karakter nyeri, dan keterbatasan fungsi awal.</p>

                        <div class="field">
                            <label>Complaint</label>
                            <textarea name="complaint" placeholder="Keluhan utama pasien">{{ old('complaint', $record->complaint ?? '') }}</textarea>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Onset</label>
                                <input type="text" name="onset" value="{{ old('onset', $record->onset ?? '') }}" placeholder="Contoh: 2 minggu yang lalu">
                            </div>

                            <div class="field">
                                <label>Pain Type</label>
                                <input type="text" name="pain_type" value="{{ old('pain_type', $record->pain_type ?? '') }}" placeholder="Contoh: Sharp, dull, radiating">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Scale of Pain (0-10)</label>
                                <input type="number" min="0" max="10" name="pain_scale" value="{{ old('pain_scale', $record->pain_scale ?? '') }}" placeholder="0 - 10">
                            </div>

                            <div class="field">
                                <label>Functional Limitation (Initial)</label>
                                <input type="text" name="functional_limitation_initial" value="{{ old('functional_limitation_initial', $record->functional_limitation_initial ?? '') }}" placeholder="Contoh: sulit duduk lama / berjalan">
                            </div>
                        </div>

                        <div class="field">
                            <label>Condition Felt</label>
                            <textarea name="condition_felt" placeholder="Kondisi yang dirasakan pasien">{{ old('condition_felt', $record->condition_felt ?? '') }}</textarea>
                        </div>

                        <div class="field">
                            <label>Pain Body Chart Note</label>
                            <textarea name="pain_body_chart_note" placeholder="Catatan area nyeri / body marking note">{{ old('pain_body_chart_note', $record->pain_body_chart_note ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">2. Medical History</h3>
                        <p class="form-section-text">Riwayat injury, surgery, fracture, stroke, malignancy, atau riwayat lain.</p>

                        <div id="history-wrapper">
                            @foreach($histories as $index => $history)
                                <div class="inline-card history-row">
                                    <div class="inline-head">
                                        <div class="inline-title">History Item</div>
                                        <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
                                    </div>

                                    <div class="grid-3">
                                        <div class="field">
                                            <label>History Type</label>
                                            <input type="text" name="history_type[]" value="{{ old('history_type.' . $index, $history->history_type ?? '') }}" placeholder="injury / surgery / fracture / other">
                                        </div>

                                        <div class="field">
                                            <label>History Date</label>
                                            <input type="date" name="history_date[]" value="{{ old('history_date.' . $index, !empty($history->history_date) ? (string) $history->history_date : '') }}">
                                        </div>

                                        <div class="field">
                                            <label>History Note</label>
                                            <input type="text" name="history_note[]" value="{{ old('history_note.' . $index, $history->history_note ?? '') }}" placeholder="Catatan singkat">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="tiny-btn" onclick="addHistoryRow()">+ Tambah History</button>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">3. Comorbidities</h3>
                        <p class="form-section-text">Catat komorbid, status cek, tanggal pengukuran, dan nilai akhir bila ada.</p>

                        <div id="comorbidity-wrapper">
                            @foreach($comorbidities as $index => $comorbidity)
                                <div class="inline-card comorbidity-row">
                                    <div class="inline-head">
                                        <div class="inline-title">Comorbidity Item</div>
                                        <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
                                    </div>

                                    <div class="grid-3">
                                        <div class="field">
                                            <label>Name</label>
                                            <input type="text" name="comorbidity_name[]" value="{{ old('comorbidity_name.' . $index, $comorbidity->name ?? '') }}" placeholder="Contoh: diabetes / hipertensi">
                                        </div>

                                        <div class="field">
                                            <label>Measurement Date</label>
                                            <input type="date" name="comorbidity_measurement_date[]" value="{{ old('comorbidity_measurement_date.' . $index, !empty($comorbidity->measurement_date) ? (string) $comorbidity->measurement_date : '') }}">
                                        </div>

                                        <div class="field">
                                            <label>Final Value</label>
                                            <input type="text" name="comorbidity_final_value[]" value="{{ old('comorbidity_final_value.' . $index, $comorbidity->final_value ?? '') }}" placeholder="Nilai akhir / hasil ukur">
                                        </div>
                                    </div>

                                    <div class="grid-2">
                                        <div class="field">
                                            <label>Note</label>
                                            <input type="text" name="comorbidity_note[]" value="{{ old('comorbidity_note.' . $index, $comorbidity->note ?? '') }}" placeholder="Catatan tambahan">
                                        </div>

                                        <div class="field">
                                            <label>Checked</label>
                                            <div class="checkbox-row">
                                                <input type="checkbox" name="comorbidity_checked[{{ $index }}]" {{ old('comorbidity_checked.' . $index, $comorbidity->is_checked ?? false) ? 'checked' : '' }}>
                                                <span>Tandai bila komorbid ini aktif / relevan</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="tiny-btn" onclick="addComorbidityRow()">+ Tambah Comorbidity</button>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">4. Vital Signs</h3>
                        <p class="form-section-text">Masukkan vital sign dasar yang dibutuhkan pada assessment.</p>

                        <div class="grid-3">
                            <div class="field">
                                <label>Blood Pressure</label>
                                <input type="text" name="blood_pressure" value="{{ old('blood_pressure', $record->blood_pressure ?? '') }}" placeholder="Contoh: 120/80">
                            </div>

                            <div class="field">
                                <label>Temperature</label>
                                <input type="text" name="temperature" value="{{ old('temperature', $record->temperature ?? '') }}" placeholder="Contoh: 36.5 C">
                            </div>

                            <div class="field">
                                <label>Respiration Rate</label>
                                <input type="text" name="respiration_rate" value="{{ old('respiration_rate', $record->respiration_rate ?? '') }}" placeholder="Contoh: 18 x/min">
                            </div>

                            <div class="field">
                                <label>Heart Rate</label>
                                <input type="text" name="heart_rate" value="{{ old('heart_rate', $record->heart_rate ?? '') }}" placeholder="Contoh: 72 bpm">
                            </div>

                            <div class="field">
                                <label>Weight</label>
                                <input type="text" name="weight" value="{{ old('weight', $record->weight ?? '') }}" placeholder="Contoh: 60 kg">
                            </div>

                            <div class="field">
                                <label>Height</label>
                                <input type="text" name="height" value="{{ old('height', $record->height ?? '') }}" placeholder="Contoh: 165 cm">
                            </div>
                        </div>

                        <div class="field">
                            <label>BMI</label>
                            <input type="text" name="bmi" value="{{ old('bmi', $record->bmi ?? '') }}" placeholder="Contoh: 22">
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">5. Supporting Data Result</h3>
                        <p class="form-section-text">Tambahkan data penunjang seperti X-ray, MRI, lab, atau data evaluasi lain.</p>

                        <div id="supporting-data-wrapper">
                            @foreach($supportingData as $index => $item)
                                <div class="inline-card supporting-row">
                                    <div class="inline-head">
                                        <div class="inline-title">Supporting Data Item</div>
                                        <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
                                    </div>

                                    <div class="grid-3">
                                        <div class="field">
                                            <label>Date</label>
                                            <input type="date" name="supporting_data_date[]" value="{{ old('supporting_data_date.' . $index, !empty($item->data_date) ? (string) $item->data_date : '') }}">
                                        </div>

                                        <div class="field">
                                            <label>Type of Data</label>
                                            <input type="text" name="supporting_data_type[]" value="{{ old('supporting_data_type.' . $index, $item->data_type ?? '') }}" placeholder="Contoh: X-ray / MRI / Lab">
                                        </div>

                                        <div class="field">
                                            <label>Interpretation</label>
                                            <input type="text" name="supporting_data_interpretation[]" value="{{ old('supporting_data_interpretation.' . $index, $item->interpretation ?? '') }}" placeholder="Interpretasi singkat">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="tiny-btn" onclick="addSupportingDataRow()">+ Tambah Supporting Data</button>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">6. Subjective & Objective Examination</h3>
                        <p class="form-section-text">Isi hasil subjective examination, objective findings, dan parameter klinis.</p>

                        <div class="field">
                            <label>Subjective Examination</label>
                            <textarea name="subjective_examination" placeholder="Riwayat keluhan, pola nyeri, aktivitas pemicu, tujuan terapi">{{ old('subjective_examination', $record->subjective_examination ?? '') }}</textarea>
                        </div>

                        <div class="field">
                            <label>Objective Examination</label>
                            <textarea name="objective_examination" placeholder="Temuan objektif, ROM, palpasi, posture, dan temuan fisik lainnya">{{ old('objective_examination', $record->objective_examination ?? '') }}</textarea>
                        </div>

                        <div class="grid-3">
                            <div class="field">
                                <label>Severity Level</label>
                                <select name="severity_level">
                                    <option value="">Pilih severity</option>
                                    <option value="low" {{ old('severity_level', $record->severity_level ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('severity_level', $record->severity_level ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('severity_level', $record->severity_level ?? '') === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>Irritability Level</label>
                                <select name="irritability_level">
                                    <option value="">Pilih irritability</option>
                                    <option value="low" {{ old('irritability_level', $record->irritability_level ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('irritability_level', $record->irritability_level ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('irritability_level', $record->irritability_level ?? '') === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>Nature Type</label>
                                <select name="nature_type">
                                    <option value="">Pilih nature</option>
                                    <option value="mechanical" {{ old('nature_type', $record->nature_type ?? '') === 'mechanical' ? 'selected' : '' }}>Mechanical</option>
                                    <option value="non_mechanical" {{ old('nature_type', $record->nature_type ?? '') === 'non_mechanical' ? 'selected' : '' }}>Non Mechanical</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Easing Factors</label>
                                <textarea name="easing_factors" placeholder="Apa yang mengurangi keluhan">{{ old('easing_factors', $record->easing_factors ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Aggravating Factors</label>
                                <textarea name="aggravating_factors" placeholder="Apa yang memperberat keluhan">{{ old('aggravating_factors', $record->aggravating_factors ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="field">
                            <label>Special Test Notes</label>
                            <textarea name="special_test_notes" placeholder="Catatan special test / additional examination">{{ old('special_test_notes', $record->special_test_notes ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">7. Diagnosis & Clinical Decision</h3>
                        <p class="form-section-text">Tulis diagnosis, impairment, goal, referral, dan rencana program therapy.</p>

                        <div class="field">
                            <label>Physiotherapy Diagnosis</label>
                            <textarea name="physiotherapy_diagnosis" placeholder="Diagnosis fisioterapi">{{ old('physiotherapy_diagnosis', $record->physiotherapy_diagnosis ?? '') }}</textarea>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Impairment</label>
                                <textarea name="impairment" placeholder="Impairment utama">{{ old('impairment', $record->impairment ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Functional Limitation (Clinical)</label>
                                <textarea name="functional_limitation_clinical" placeholder="Keterbatasan fungsi dari sudut klinis">{{ old('functional_limitation_clinical', $record->functional_limitation_clinical ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Patient Goal</label>
                                <textarea name="patient_goal" placeholder="Target patient / goal terapi">{{ old('patient_goal', $record->patient_goal ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Referral</label>
                                <textarea name="referral" placeholder="Rujukan bila ada">{{ old('referral', $record->referral ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="field">
                            <label>Program Patient</label>
                            <textarea name="program_patient" placeholder="Program terapi yang direncanakan">{{ old('program_patient', $record->program_patient ?? '') }}</textarea>
                        </div>

                        <div class="grid-3">
                            <div class="field">
                                <label>Date of Control</label>
                                <input type="date" name="date_of_control" value="{{ old('date_of_control', !empty($record->date_of_control) ? $record->date_of_control->format('Y-m-d') : '') }}">
                            </div>

                            <div class="field">
                                <label>Total Session</label>
                                <input type="number" min="0" name="total_session" value="{{ old('total_session', $record->total_session ?? '') }}" placeholder="Contoh: 6">
                            </div>

                            <div class="field">
                                <label>Frequency per Week</label>
                                <input type="text" name="frequency_per_week" value="{{ old('frequency_per_week', $record->frequency_per_week ?? '') }}" placeholder="Contoh: 2x/minggu">
                            </div>
                        </div>

                        <div class="field">
                            <label>Control Plan</label>
                            <textarea name="control_plan" placeholder="Rencana kontrol / follow up">{{ old('control_plan', $record->control_plan ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">8. Health Management</h3>
                        <p class="form-section-text">Edukasi terkait nutrisi, lifestyle, dan management flare-up.</p>

                        <div class="grid-3">
                            <div class="field">
                                <label>Diet / Nutrition</label>
                                <textarea name="diet_nutrition" placeholder="Catatan diet / nutrisi">{{ old('diet_nutrition', $record->diet_nutrition ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Lifestyle</label>
                                <textarea name="lifestyle" placeholder="Catatan lifestyle">{{ old('lifestyle', $record->lifestyle ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Flare-up Management</label>
                                <textarea name="flare_up_management" placeholder="Cara mengelola flare-up">{{ old('flare_up_management', $record->flare_up_management ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">9. Home Exercise Program</h3>
                        <p class="form-section-text">Tambahkan latihan rumah, dosis, dan catatan/caution.</p>

                        <div id="home-exercise-wrapper">
                            @foreach($homeExercises as $index => $exercise)
                                <div class="inline-card home-exercise-row">
                                    <div class="inline-head">
                                        <div class="inline-title">Home Exercise Item</div>
                                        <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
                                    </div>

                                    <div class="grid-3">
                                        <div class="field">
                                            <label>Exercise</label>
                                            <input type="text" name="home_exercise_name[]" value="{{ old('home_exercise_name.' . $index, $exercise->exercise ?? '') }}" placeholder="Nama latihan">
                                        </div>

                                        <div class="field">
                                            <label>Dosage</label>
                                            <input type="text" name="home_exercise_dosage[]" value="{{ old('home_exercise_dosage.' . $index, $exercise->dosage ?? '') }}" placeholder="Contoh: 10 reps x 3 set">
                                        </div>

                                        <div class="field">
                                            <label>Note / Caution</label>
                                            <input type="text" name="home_exercise_note[]" value="{{ old('home_exercise_note.' . $index, $exercise->note_caution ?? '') }}" placeholder="Catatan kehati-hatian">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="tiny-btn" onclick="addHomeExerciseRow()">+ Tambah Home Exercise</button>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">10. Session Progress</h3>
                        <p class="form-section-text">Catatan intervensi sesi ini dan rencana sesi selanjutnya.</p>

                        <div class="field">
                            <label>Assessment (Legacy / Summary)</label>
                            <textarea name="assessment" placeholder="Ringkasan assessment singkat">{{ old('assessment', $record->assessment ?? '') }}</textarea>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Treatment (Legacy)</label>
                                <textarea name="treatment" placeholder="Treatment singkat versi lama">{{ old('treatment', $record->treatment ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Treatment Given</label>
                                <textarea name="treatment_given" placeholder="Intervensi yang diberikan di sesi ini">{{ old('treatment_given', $record->treatment_given ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Progress Note</label>
                                <textarea name="progress_note" placeholder="Progress pasien">{{ old('progress_note', $record->progress_note ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Response to Treatment</label>
                                <textarea name="response_to_treatment" placeholder="Respon pasien terhadap terapi">{{ old('response_to_treatment', $record->response_to_treatment ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label>Recommendation</label>
                                <textarea name="recommendation" placeholder="Rekomendasi umum">{{ old('recommendation', $record->recommendation ?? '') }}</textarea>
                            </div>

                            <div class="field">
                                <label>Next Session Plan</label>
                                <textarea name="next_session_plan" placeholder="Rencana visit berikutnya">{{ old('next_session_plan', $record->next_session_plan ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="submit-row">
                        <button type="submit" class="submit-btn">Simpan Medical Record V2</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>

<script>
    function removeRow(button) {
        const row = button.closest('.inline-card');
        if (row) row.remove();
    }

    function addHistoryRow() {
        const wrapper = document.getElementById('history-wrapper');
        const div = document.createElement('div');
        div.className = 'inline-card history-row';
        div.innerHTML = `
            <div class="inline-head">
                <div class="inline-title">History Item</div>
                <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
            </div>
            <div class="grid-3">
                <div class="field">
                    <label>History Type</label>
                    <input type="text" name="history_type[]" placeholder="injury / surgery / fracture / other">
                </div>
                <div class="field">
                    <label>History Date</label>
                    <input type="date" name="history_date[]">
                </div>
                <div class="field">
                    <label>History Note</label>
                    <input type="text" name="history_note[]" placeholder="Catatan singkat">
                </div>
            </div>
        `;
        wrapper.appendChild(div);
    }

    function addComorbidityRow() {
        const wrapper = document.getElementById('comorbidity-wrapper');
        const index = wrapper.querySelectorAll('.comorbidity-row').length;
        const div = document.createElement('div');
        div.className = 'inline-card comorbidity-row';
        div.innerHTML = `
            <div class="inline-head">
                <div class="inline-title">Comorbidity Item</div>
                <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
            </div>
            <div class="grid-3">
                <div class="field">
                    <label>Name</label>
                    <input type="text" name="comorbidity_name[]" placeholder="Contoh: diabetes / hipertensi">
                </div>
                <div class="field">
                    <label>Measurement Date</label>
                    <input type="date" name="comorbidity_measurement_date[]">
                </div>
                <div class="field">
                    <label>Final Value</label>
                    <input type="text" name="comorbidity_final_value[]" placeholder="Nilai akhir / hasil ukur">
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label>Note</label>
                    <input type="text" name="comorbidity_note[]" placeholder="Catatan tambahan">
                </div>
                <div class="field">
                    <label>Checked</label>
                    <div class="checkbox-row">
                        <input type="checkbox" name="comorbidity_checked[${index}]">
                        <span>Tandai bila komorbid ini aktif / relevan</span>
                    </div>
                </div>
            </div>
        `;
        wrapper.appendChild(div);
    }

    function addSupportingDataRow() {
        const wrapper = document.getElementById('supporting-data-wrapper');
        const div = document.createElement('div');
        div.className = 'inline-card supporting-row';
        div.innerHTML = `
            <div class="inline-head">
                <div class="inline-title">Supporting Data Item</div>
                <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
            </div>
            <div class="grid-3">
                <div class="field">
                    <label>Date</label>
                    <input type="date" name="supporting_data_date[]">
                </div>
                <div class="field">
                    <label>Type of Data</label>
                    <input type="text" name="supporting_data_type[]" placeholder="Contoh: X-ray / MRI / Lab">
                </div>
                <div class="field">
                    <label>Interpretation</label>
                    <input type="text" name="supporting_data_interpretation[]" placeholder="Interpretasi singkat">
                </div>
            </div>
        `;
        wrapper.appendChild(div);
    }

    function addHomeExerciseRow() {
        const wrapper = document.getElementById('home-exercise-wrapper');
        const div = document.createElement('div');
        div.className = 'inline-card home-exercise-row';
        div.innerHTML = `
            <div class="inline-head">
                <div class="inline-title">Home Exercise Item</div>
                <button type="button" class="remove-btn" onclick="removeRow(this)">Hapus</button>
            </div>
            <div class="grid-3">
                <div class="field">
                    <label>Exercise</label>
                    <input type="text" name="home_exercise_name[]" placeholder="Nama latihan">
                </div>
                <div class="field">
                    <label>Dosage</label>
                    <input type="text" name="home_exercise_dosage[]" placeholder="Contoh: 10 reps x 3 set">
                </div>
                <div class="field">
                    <label>Note / Caution</label>
                    <input type="text" name="home_exercise_note[]" placeholder="Catatan kehati-hatian">
                </div>
            </div>
        `;
        wrapper.appendChild(div);
    }
</script>
</body>
</html>