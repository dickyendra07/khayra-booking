<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Surat Kontrol / Surat Terapi - Khayra Physio</title>
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
        textarea{padding:14px;min-height:110px;resize:vertical;line-height:1.7}
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
@endphp

<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'documents'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/documents" class="btn btn-soft">← Dokumen Klinik</a>
            </div>

            <section class="card">
                <span class="badge">Surat Kontrol / Surat Terapi</span>
                <h1>Buat Dokumen Kontrol / Terapi</h1>
                <p class="subtitle">Ambil data dari pasien, visit, dan medical record. Setelah submit, halaman print/save PDF akan terbuka.</p>

                @if($errors->any())
                    <div class="error">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/admin/control-letter/print" target="_blank">
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
                            <label>Visit Terkait</label>
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
                            <label>Tanggal Dokumen</label>
                            <input type="date" name="letter_date" value="{{ old('letter_date', now()->format('Y-m-d')) }}" required>
                        </div>

                        <div class="field">
                            <label>Tipe Dokumen</label>
                            <select name="document_type" required>
                                <option value="control">Surat Kontrol</option>
                                <option value="therapy">Surat Keterangan Terapi</option>
                            </select>
                        </div>

                        <div class="field">
                            <label>Tanggal Kontrol Berikutnya</label>
                            <input type="date" name="control_date" value="{{ old('control_date', $record && $record->date_of_control ? $record->date_of_control->format('Y-m-d') : '') }}">
                        </div>

                        <div class="field">
                            <label>Frekuensi Terapi</label>
                            <input type="text" name="therapy_frequency" value="{{ old('therapy_frequency', $record->frequency_per_week ?? '') }}" placeholder="Contoh: 2x/minggu">
                        </div>

                        <div class="field">
                            <label>Total Session</label>
                            <input type="text" name="total_session" value="{{ old('total_session', $record->total_session ?? '') }}" placeholder="Contoh: 5 sesi">
                        </div>

                        <div class="field">
                            <label>Diagnosis / Kondisi</label>
                            <input type="text" name="diagnosis" value="{{ old('diagnosis', $record->physiotherapy_diagnosis ?? $record->assessment ?? '') }}" placeholder="Diagnosis fisioterapi / kondisi">
                        </div>

                        <div class="field full">
                            <label>Program Terapi</label>
                            <textarea name="therapy_program">{{ old('therapy_program', $record->program_patient ?? '') }}</textarea>
                        </div>

                        <div class="field full">
                            <label>Rekomendasi / Rencana Kontrol</label>
                            <textarea name="recommendation">{{ old('recommendation', $record->control_plan ?? $record->next_session_plan ?? '') }}</textarea>
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
