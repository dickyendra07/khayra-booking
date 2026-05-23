<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin / Surat Istirahat Pasien - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1120px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
        .badge { display: inline-flex; padding: 8px 13px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px; }
        .title { margin: 0; font-size: 38px; line-height: 1.05; color: #22343a; font-weight: 900; letter-spacing: -1px; }
        .subtitle { margin: 10px 0 0; color: #6b7280; font-size: 14px; line-height: 1.8; max-width: 800px; }
        .card { background: #ffffff; border: 1px solid #ecefef; border-radius: 28px; padding: 26px; box-shadow: 0 14px 34px rgba(15,23,42,.05); margin-bottom: 18px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .full { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 900; color: #334155; }
        input, select, textarea { width: 100%; padding: 14px; border: 1px solid #dde5e3; border-radius: 14px; font-size: 14px; background: #ffffff; color: #111827; font-family: Arial, sans-serif; }
        textarea { min-height: 110px; line-height: 1.7; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #176f69; box-shadow: 0 0 0 4px rgba(23,111,105,.08); }
        select { color: #111827; font-weight: 700; }
        select option { color: #111827; background: #ffffff; font-weight: 700; }
        .btn { min-height: 44px; border: 0; cursor: pointer; padding: 0 18px; border-radius: 14px; font-size: 13px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-family: Arial, sans-serif; white-space: nowrap; }
        .btn-primary { background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: white; box-shadow: 0 12px 24px rgba(47,124,122,.16); }
        .btn-soft { color: #2f7c7a; background: #ffffff; border: 1px solid #d8ebe7; }
        .actions { display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
        .error-box { background: #fff1f2; border: 1px solid #ffe0e6; color: #be123c; padding: 14px 16px; border-radius: 16px; margin-bottom: 18px; font-size: 13px; line-height: 1.8; }
        .hint { margin-top: 6px; color: #94a3b8; font-size: 12px; line-height: 1.6; }
        @media (max-width: 900px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
            .title { font-size: 30px; }
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
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'rest-letter'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Dokumen Klinik</span>
                    <h1 class="title">Surat Izin / Surat Istirahat Pasien</h1>
                    <p class="subtitle">Buat surat resmi untuk kebutuhan izin aktivitas atau istirahat pasien berdasarkan kondisi fisioterapi.</p>
                </div>
                <a href="/admin/documents" class="btn btn-soft">Kembali</a>
            </div>

            <form method="POST" action="/admin/rest-letter/print" target="_blank">
                @csrf

                <section class="card">
                    @if ($errors->any())
                        <div class="error-box">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid">
                        <div>
                            <label>Pasien</label>
                            <select name="patient_id" required>
                                <option value="">Pilih pasien</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id', $selectedPatientId) == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->full_name }}{{ $patient->medical_record_number ? ' - ' . $patient->medical_record_number : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label>Visit Terkait</label>
                            <select name="visit_id">
                                <option value="">Tanpa visit</option>
                                @foreach($visits as $visit)
                                    <option value="{{ $visit->id }}" {{ old('visit_id', $selectedVisitId) == $visit->id ? 'selected' : '' }}>
                                        #{{ $visit->id }} - {{ optional($visit->patient)->full_name }} - {{ $visit->visit_date ?: '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label>Fisioterapis</label>
                            <select name="therapist_id">
                                <option value="">Pilih fisioterapis</option>
                                @foreach($therapists as $therapist)
                                    @php
                                        $therapistLabel = $therapist->name
                                            ?? $therapist->full_name
                                            ?? $therapist->email
                                            ?? ('Therapist #' . $therapist->id);
                                    @endphp
                                    <option value="{{ $therapist->id }}" {{ old('therapist_id') == $therapist->id ? 'selected' : '' }}>
                                        {{ $therapistLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label>Tanggal Surat</label>
                            <input type="date" name="letter_date" value="{{ old('letter_date', now()->format('Y-m-d')) }}">
                        </div>

                        <div>
                            <label>Jenis Surat</label>
                            <select name="letter_type" required>
                                <option value="istirahat" {{ old('letter_type') === 'istirahat' ? 'selected' : '' }}>Surat Istirahat Pasien</option>
                                <option value="izin" {{ old('letter_type') === 'izin' ? 'selected' : '' }}>Surat Izin Aktivitas / Sekolah / Kerja</option>
                            </select>
                        </div>

                        <div>
                            <label>Ditujukan Kepada</label>
                            <input type="text" name="recipient" value="{{ old('recipient') }}" placeholder="Contoh: HRD / Wali Kelas / Pihak terkait">
                        </div>

                        <div>
                            <label>Mulai Istirahat / Izin</label>
                            <input type="date" name="rest_start_date" value="{{ old('rest_start_date', now()->format('Y-m-d')) }}">
                        </div>

                        <div>
                            <label>Sampai Tanggal</label>
                            <input type="date" name="rest_end_date" value="{{ old('rest_end_date', now()->addDays(2)->format('Y-m-d')) }}">
                        </div>

                        <div>
                            <label>Jumlah Hari</label>
                            <input type="number" name="rest_days" min="1" max="60" value="{{ old('rest_days', 3) }}">
                        </div>

                        <div>
                            <label>Diagnosis / Kondisi</label>
                            <input type="text" name="diagnosis" value="{{ old('diagnosis') }}" placeholder="Contoh: Low Back Pain / ankle sprain / post injury">
                        </div>

                        <div class="full">
                            <label>Keterangan Pembatasan Aktivitas</label>
                            <textarea name="activity_limitation" placeholder="Contoh: pasien disarankan menghindari aktivitas berat, berdiri terlalu lama, olahraga intens, atau perjalanan jauh selama masa pemulihan.">{{ old('activity_limitation') }}</textarea>
                        </div>

                        <div class="full">
                            <label>Catatan Tambahan</label>
                            <textarea name="notes" placeholder="Opsional. Contoh: pasien tetap dapat melakukan aktivitas ringan sesuai toleransi nyeri.">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="actions">
                        <a href="/admin/documents" class="btn btn-soft">Batal</a>
                        <button type="submit" class="btn btn-primary">Preview / Print Surat</button>
                    </div>
                </section>
            </form>
        </div>
    </main>
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
