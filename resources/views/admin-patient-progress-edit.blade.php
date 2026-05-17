<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Progress Pasien - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f8f8;
            color: #17232b;
        }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1180px; margin: 0 auto; }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .badge {
            display: inline-flex;
            padding: 8px 13px;
            border-radius: 999px;
            background: #eef5f4;
            color: #35565d;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 12px;
        }
        .title {
            margin: 0;
            font-size: 42px;
            line-height: 1.05;
            color: #22343a;
            font-weight: 900;
            letter-spacing: -.7px;
        }
        .subtitle {
            margin: 12px 0 0;
            max-width: 820px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.9;
        }
        .btn {
            min-height: 42px;
            border: 0;
            cursor: pointer;
            padding: 0 16px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            white-space: nowrap;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(47,124,122,.16);
        }
        .btn-soft {
            color: #2f7c7a;
            background: #ffffff;
            border: 1px solid #e6ebea;
        }
        .card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(15,23,42,.05);
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .field.full { grid-column: 1 / -1; }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        input, select, textarea {
            width: 100%;
            border: 1px solid #d7dedd;
            border-radius: 15px;
            padding: 14px;
            font-size: 14px;
            color: #17232b;
            background: #ffffff;
            font-family: Arial, sans-serif;
        }
        textarea {
            min-height: 120px;
            resize: vertical;
            line-height: 1.7;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #2f7c7a;
            box-shadow: 0 0 0 4px rgba(47,124,122,.08);
        }
        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 18px;
            font-size: 14px;
            line-height: 1.7;
            font-weight: 700;
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #ffe0e6;
        }
        .submit-row {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        @media (max-width: 900px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .title { font-size: 32px; }
            .form-grid { grid-template-columns: 1fr; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'patients'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Patient Progress</span>
                    <h1 class="title">Edit Progress</h1>
                    <p class="subtitle">
                        Perbarui progress tracking untuk {{ $patient->full_name }}. Pastikan Visit Terkait sesuai agar muncul di halaman visit pasien yang benar.
                    </p>
                </div>

                <a href="/admin/patients/{{ $patient->id }}" class="btn btn-soft">← Kembali ke Patient</a>
            </div>

            @if($errors->any())
                <div class="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <section class="card">
                <form method="POST" action="/admin/patients/{{ $patient->id }}/progress/{{ $progress->id }}/update">
                    @csrf

                    <div class="form-grid">
                        <div class="field">
                            <label>Tanggal Progress</label>
                            <input
                                type="date"
                                name="entry_date"
                                value="{{ old('entry_date', optional($progress->entry_date)->format('Y-m-d')) }}"
                                required
                            >
                        </div>

                        <div class="field">
                            <label>Visit Terkait</label>
                            <select name="visit_id">
                                <option value="">Tidak dikaitkan ke visit</option>
                                @foreach($visits as $visit)
                                    <option value="{{ $visit->id }}" {{ (string) old('visit_id', $progress->visit_id) === (string) $visit->id ? 'selected' : '' }}>
                                        Visit #{{ $visit->id }} · {{ $visit->visit_date ?: '-' }} · {{ optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: 'Therapist belum diisi' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field full">
                            <label>Pain Scale 0-10</label>
                            <input
                                type="number"
                                name="pain_scale"
                                min="0"
                                max="10"
                                value="{{ old('pain_scale', $progress->pain_scale) }}"
                                placeholder="Contoh: 4"
                            >
                        </div>

                        <div class="field full">
                            <label>ROM / Movement Notes</label>
                            <textarea name="rom_notes" placeholder="Contoh: fleksi bahu membaik, nyeri berkurang saat elevasi tangan...">{{ old('rom_notes', $progress->rom_notes) }}</textarea>
                        </div>

                        <div class="field full">
                            <label>Functional Goal</label>
                            <textarea name="functional_goal" placeholder="Contoh: pasien bisa angkat tangan tanpa nyeri, bisa berjalan 30 menit...">{{ old('functional_goal', $progress->functional_goal) }}</textarea>
                        </div>

                        <div class="field full">
                            <label>Progress Notes</label>
                            <textarea name="progress_notes" placeholder="Contoh: pasien sudah latihan mandiri 2x sehari, nyeri turun dari 7 ke 4...">{{ old('progress_notes', $progress->progress_notes) }}</textarea>
                        </div>
                    </div>

                    <div class="submit-row">
                        <a href="/admin/patients/{{ $patient->id }}" class="btn btn-soft">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Progress</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>
