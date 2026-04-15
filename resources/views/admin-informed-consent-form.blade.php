<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informed Consent - Khayra Physio</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        .top-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 18px;
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
            grid-template-columns: 1.1fr .9fr;
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
            font-size: 40px;
            line-height: 1.06;
            color: #22343a;
        }
        .hero-text {
            margin: 16px 0 0;
            font-size: 14px;
            line-height: 1.9;
            color: #6b7280;
            max-width: 700px;
        }
        .hero-side {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            border-radius: 24px;
            padding: 24px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
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
            margin: 0 0 18px;
            font-size: 13px;
            line-height: 1.85;
            color: rgba(255,255,255,.94);
        }
        .snapshot-grid {
            display: grid;
            gap: 12px;
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
        .error-box {
            background: #fff1f2;
            border: 1px solid #ffe0e6;
            color: #be123c;
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 18px;
            line-height: 1.8;
            font-size: 13px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }
        .field.full {
            grid-column: 1 / -1;
        }
        .field label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }
        input, select, textarea {
            width: 100%;
            padding: 14px 14px;
            border: 1px solid #dde5e3;
            border-radius: 14px;
            font-size: 14px;
            background: #ffffff;
            color: #111827;
            font-family: Arial, sans-serif;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #176f69;
            box-shadow: 0 0 0 4px rgba(23,111,105,.08);
        }
        .readonly-box {
            width: 100%;
            padding: 14px 14px;
            border: 1px dashed #d9e4e1;
            border-radius: 14px;
            background: #f8fbfa;
            color: #334155;
            font-size: 14px;
            line-height: 1.7;
            font-weight: 700;
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 22px;
            flex-wrap: wrap;
        }
        .submit-btn, .print-link {
            border: none;
            text-decoration: none;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            padding: 14px 22px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(47,124,122,.16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .print-link {
            background: #ffffff;
            color: #2c5b5a;
            border: 1px solid #dce7e4;
            box-shadow: none;
        }
        @media (max-width: 960px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-grid, .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'patients'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/patients/{{ $patient->id }}" class="ghost-link">← Kembali ke Detail Patient</a>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <div class="hero-badge">Informed Consent</div>
                        <h1 class="hero-title">Create consent document for {{ $patient->full_name }}</h1>
                        <p class="hero-text">
                            Form ini dipakai untuk mencatat persetujuan tindakan fisioterapi berdasarkan identitas patient, visit yang dipilih, dan detail pelaksanaan tindakan.
                        </p>
                    </div>

                    <div class="hero-side">
                        <h3>Patient Snapshot</h3>
                        <p>Ringkasan dasar patient yang akan dipakai di dokumen informed consent.</p>

                        <div class="snapshot-grid">
                            <div class="snapshot-card">
                                <div class="snapshot-label">MR Number</div>
                                <div class="snapshot-value">{{ $patient->medical_record_number ?: 'Belum terbentuk' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">WhatsApp</div>
                                <div class="snapshot-value">{{ $patient->whatsapp ?: '-' }}</div>
                            </div>

                            <div class="snapshot-card">
                                <div class="snapshot-label">Latest Consent</div>
                                <div class="snapshot-value">
                                    {{ $latestConsent && $latestConsent->consent_date ? $latestConsent->consent_date->format('Y-m-d') : 'Belum ada' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <h2 class="section-title">Consent Form</h2>
                <p class="section-subtitle">Lengkapi informasi persetujuan tindakan fisioterapi untuk patient ini.</p>

                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/admin/patients/{{ $patient->id }}/informed-consent">
                    @csrf

                    <div class="form-grid">
                        <div class="field">
                            <label>Nama Patient</label>
                            <div class="readonly-box">{{ $patient->full_name }}</div>
                        </div>

                        <div class="field">
                            <label>Medical Record Number</label>
                            <div class="readonly-box">{{ $patient->medical_record_number ?: '-' }}</div>
                        </div>

                        <div class="field">
                            <label>Tanggal Consent</label>
                            <input type="date" name="consent_date" value="{{ old('consent_date', now()->format('Y-m-d')) }}" required>
                        </div>

                        <div class="field">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="signed" {{ old('status') == 'signed' ? 'selected' : '' }}>Signed</option>
                            </select>
                        </div>

                        <div class="field">
                            <label>Visit Terkait</label>
                            <select name="visit_id">
                                <option value="">Pilih Visit (opsional)</option>
                                @foreach($patient->visits as $visit)
                                    <option value="{{ $visit->id }}" {{ old('visit_id') == $visit->id ? 'selected' : '' }}>
                                        #{{ $visit->id }} - {{ $visit->visit_date ?: '-' }} - {{ $visit->therapist ?: 'Physiotherapy' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Nama Physiotherapy</label>
                            <input type="text" name="physiotherapy_name" value="{{ old('physiotherapy_name', 'Khayra Physio') }}" required>
                        </div>

                        <div class="field">
                            <label>Lokasi Tindakan</label>
                            <input type="text" name="treatment_location" value="{{ old('treatment_location', 'Khayra Physio Bandung') }}" required>
                        </div>

                        <div class="field">
                            <label>Nama Perwakilan (opsional)</label>
                            <input type="text" name="representative_name" value="{{ old('representative_name') }}">
                        </div>

                        <div class="field">
                            <label>Hubungan dengan Patient (opsional)</label>
                            <input type="text" name="relationship_to_patient" value="{{ old('relationship_to_patient') }}">
                        </div>

                        <div class="field full">
                            <label>Teks Persetujuan</label>
                            <textarea name="agreement_text" rows="8">{{ old('agreement_text', 'Saya menyatakan telah menerima penjelasan mengenai tindakan fisioterapi yang akan dilakukan, tujuan terapi, manfaat, serta kemungkinan respons selama proses penanganan. Dengan ini saya memberikan persetujuan untuk dilakukan tindakan fisioterapi sesuai kebutuhan klinis patient.') }}</textarea>
                        </div>

                        <div class="field full">
                            <label>Notes Tambahan</label>
                            <textarea name="notes" rows="4">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="actions">
                        @if($latestConsent)
                            <a href="/admin/informed-consents/{{ $latestConsent->id }}/print" class="print-link" target="_blank">Print Latest Consent</a>
                        @endif
                        <button type="submit" class="submit-btn">Simpan Informed Consent</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>