<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Discharge Summary - Khayra Physio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;background:#f6f8f8;color:#17232b}
        .layout{display:flex;min-height:100vh}
        .main{flex:1;padding:28px;min-width:0}
        .container{max-width:1180px;margin:0 auto}
        .top-actions{display:flex;justify-content:flex-end;margin-bottom:18px}
        .btn{min-height:42px;border:0;padding:0 16px;border-radius:14px;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;font-weight:900;cursor:pointer;font-size:13px}
        .btn-soft{background:#fff;color:#2f7c7a;border:1px solid #dfe8e6}
        .btn-primary{background:linear-gradient(135deg,#3d8a89,#2f7c7a);color:#fff}
        .card{background:#fff;border:1px solid #ecefef;border-radius:26px;padding:24px;box-shadow:0 10px 26px rgba(15,23,42,.04);margin-bottom:18px}
        .badge{display:inline-flex;padding:8px 13px;border-radius:999px;background:#eef7f5;color:#2f7c7a;font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px}
        h1{margin:0;font-size:40px;color:#22343a}
        .subtitle{color:#64748b;line-height:1.8;font-size:14px;margin:12px 0 22px}
        .grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
        .field.full{grid-column:1/-1}
        label{display:block;font-size:12px;font-weight:900;color:#334155;margin-bottom:8px}
        input,select,textarea{width:100%;min-height:46px;border:1px solid #d7dedd;border-radius:14px;padding:0 14px;font-size:13px;font-family:Arial,sans-serif}
        textarea{padding:14px;min-height:120px;resize:vertical;line-height:1.7}
        .actions{display:flex;justify-content:flex-end;gap:10px;margin-top:18px;flex-wrap:wrap}
        .error{background:#fff1f2;color:#be123c;border:1px solid #ffe0e6;border-radius:14px;padding:14px;margin-bottom:18px;font-size:13px;line-height:1.7;font-weight:800}
        @media(max-width:900px){.layout{display:block}.main{padding:16px}.grid{grid-template-columns:1fr}h1{font-size:32px}}
    </style>
</head>
<body>
@php
    $record = optional($selectedVisit)->medicalRecord;
    $prefillPatientId = old('patient_id', $selectedPatientId);
    $prefillVisitId = old('visit_id', optional($selectedVisit)->id);
    $homeProgram = '';
    if ($record && $record->homeExercises) {
        $homeProgram = $record->homeExercises->map(fn($ex) => trim(($ex->exercise ?: 'Exercise') . ' — ' . ($ex->dosage ?: '-') . ($ex->note_caution ? ' — ' . $ex->note_caution : '')))->implode("\n");
    }
@endphp

<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'documents'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/documents" class="btn btn-soft">← Dokumen Klinik</a>
            </div>

            <section class="card">
                <span class="badge">Discharge Summary</span>
                <h1>Buat Discharge Summary</h1>
                <p class="subtitle">Ringkasan selesai terapi yang bisa dicetak/simpan PDF. Cocok untuk penutup program terapi atau arsip pasien.</p>

                @if($errors->any())
                    <div class="error">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/admin/discharge-summary/print" target="_blank">
                    @csrf
                    <div class="grid">
                        <div class="field">
                            <label>Patient</label>
                            <select name="patient_id" required>
                                <option value="">Pilih Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ (string)$prefillPatientId === (string)$patient->id ? 'selected' : '' }}>
                                        {{ $patient->full_name }}{{ $patient->medical_record_number ? ' - ' . $patient->medical_record_number : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Visit Terakhir / Visit Terkait</label>
                            <select name="visit_id">
                                <option value="">Pilih Visit</option>
                                @foreach($visits as $visit)
                                    <option value="{{ $visit->id }}" {{ (string)$prefillVisitId === (string)$visit->id ? 'selected' : '' }}>
                                        #{{ $visit->id }} - {{ optional($visit->patient)->full_name ?: 'Patient' }} - {{ $visit->visit_date ?: '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Tanggal Summary</label>
                            <input type="date" name="summary_date" value="{{ old('summary_date', now()->format('Y-m-d')) }}" required>
                        </div>

                        <div class="field">
                            <label>Visit Status</label>
                            <input type="text" value="{{ $selectedVisit ? ($selectedVisit->status ?: '-') : '-' }}" readonly>
                        </div>

                        <div class="field full">
                            <label>Kondisi Awal</label>
                            <textarea name="initial_condition">{{ old('initial_condition', $record->complaint ?? $record->condition_felt ?? '') }}</textarea>
                        </div>

                        <div class="field full">
                            <label>Kondisi Akhir</label>
                            <textarea name="final_condition">{{ old('final_condition', $record->response_to_treatment ?? $record->progress_note ?? '') }}</textarea>
                        </div>

                        <div class="field full">
                            <label>Ringkasan Terapi</label>
                            <textarea name="therapy_summary">{{ old('therapy_summary', $record->treatment_given ?? $record->treatment ?? '') }}</textarea>
                        </div>

                        <div class="field full">
                            <label>Home Program</label>
                            <textarea name="home_program">{{ old('home_program', $homeProgram) }}</textarea>
                        </div>

                        <div class="field full">
                            <label>Rekomendasi Lanjutan</label>
                            <textarea name="recommendation">{{ old('recommendation', $record->recommendation ?? $record->next_session_plan ?? $record->control_plan ?? '') }}</textarea>
                        </div>

                        <div class="field full">
                            <label>Catatan Tambahan</label>
                            <textarea name="notes">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="actions">
                        <button class="btn btn-primary">Generate Print / PDF</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>
