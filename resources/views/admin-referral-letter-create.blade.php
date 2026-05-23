<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Surat Rujukan - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1120px; margin: 0 auto; }
        .top-actions { display: flex; justify-content: flex-end; margin-bottom: 18px; }
        .ghost-link { display: inline-flex; align-items: center; text-decoration: none; padding: 11px 14px; border-radius: 12px; background: #ffffff; border: 1px solid #e6ebea; color: #2c5b5a; font-size: 13px; font-weight: 800; }
        .section-card { background: #ffffff; border: 1px solid #ecefef; border-radius: 24px; padding: 24px; box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04); margin-bottom: 18px; }
        .badge { display: inline-block; padding: 8px 14px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 800; margin-bottom: 14px; }
        .title { font-size: 38px; font-weight: 800; color: #22343a; margin: 0 0 10px; line-height: 1.08; }
        .subtitle { font-size: 14px; line-height: 1.8; color: #6b7280; margin: 0 0 22px; max-width: 860px; }
        .error-box { background: #fff1f2; border: 1px solid #ffe0e6; color: #be123c; padding: 14px 16px; border-radius: 14px; margin-bottom: 18px; line-height: 1.8; font-size: 13px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .field.full { grid-column: 1 / -1; }
        .field label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 800; color: #334155; }
        input, select, textarea { width: 100%; padding: 14px 14px; border: 1px solid #dde5e3; border-radius: 14px; font-size: 14px; background: #ffffff; color: #111827; font-family: Arial, sans-serif; }
        textarea { min-height: 120px; resize: vertical; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #176f69; box-shadow: 0 0 0 4px rgba(23,111,105,.08); }
        .hint { margin-top: 7px; font-size: 12px; color: #94a3b8; line-height: 1.6; }
        .actions { display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap; margin-top: 22px; }
        .submit-btn { border: none; background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: #ffffff; padding: 14px 22px; border-radius: 14px; font-size: 14px; font-weight: 800; cursor: pointer; }
        .secondary-btn { display: inline-flex; align-items: center; text-decoration: none; padding: 14px 18px; border-radius: 14px; background: #f7faf9; border: 1px solid #e6ebea; color: #2c5b5a; font-size: 14px; font-weight: 800; }
        @media (max-width: 900px) { .layout { display: block; } .main { padding: 16px; } .form-grid { grid-template-columns: 1fr; } .title { font-size: 32px; } }
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
    $prefillPatientId = old('patient_id', $selectedPatientId);
    $prefillVisitId = old('visit_id', optional($selectedVisit)->id);
    $prefillSummary = old('clinical_summary', $selectedVisit ? trim(
        "Ringkasan visit: " . ($selectedVisit->notes ?: '-') . "\n" .
        "Tanggal visit: " . ($selectedVisit->visit_date ?: '-') . "\n" .
        "Fisioterapis: " . ($selectedVisit->therapist ?: '-')
    ) : '');
@endphp

<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'documents'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/referral-letters" class="ghost-link">← Kembali ke Surat Rujukan</a>
            </div>

            <section class="section-card">
                <span class="badge">Referral Letter Add-on</span>
                <h1 class="title">Buat Surat Rujukan Pasien</h1>
                <p class="subtitle">
                    Buat surat rujukan yang bisa langsung dicetak atau disimpan sebagai PDF. Dokumen bisa dikaitkan dengan pasien dan visit tertentu.
                </p>

                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/admin/referral-letters">
                    @csrf

                    <div class="form-grid">
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
                                <option value="">Pilih Visit (opsional)</option>
                                @foreach($visits as $visit)
                                    <option value="{{ $visit->id }}" {{ (string)$prefillVisitId === (string)$visit->id ? 'selected' : '' }}>
                                        #{{ $visit->id }} - {{ optional($visit->patient)->full_name ?: 'Patient' }} - {{ $visit->visit_date ?: '-' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="hint">Opsional, tapi disarankan agar surat terkait dengan riwayat visit.</div>
                        </div>

                        <div class="field">
                            <label>Tanggal Surat</label>
                            <input type="date" name="letter_date" value="{{ old('letter_date', now()->format('Y-m-d')) }}" required>
                        </div>

                        <div class="field">
                            <label>Dirujuk Kepada</label>
                            <input type="text" name="referral_to" value="{{ old('referral_to') }}" placeholder="Contoh: Dokter Spesialis Ortopedi / RS tujuan">
                        </div>

                        <div class="field full">
                            <label>Alasan Rujukan</label>
                            <textarea name="referral_reason" placeholder="Tulis alasan pasien dirujuk.">{{ old('referral_reason') }}</textarea>
                        </div>

                        <div class="field full">
                            <label>Ringkasan Klinis</label>
                            <textarea name="clinical_summary" placeholder="Tulis ringkasan keluhan, pemeriksaan, atau kondisi pasien.">{{ $prefillSummary }}</textarea>
                        </div>

                        <div class="field full">
                            <label>Rekomendasi</label>
                            <textarea name="recommendation" placeholder="Tulis rekomendasi pemeriksaan atau tindak lanjut.">{{ old('recommendation') }}</textarea>
                        </div>

                        <div class="field full">
                            <label>Catatan Tambahan</label>
                            <textarea name="notes" placeholder="Opsional.">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="actions">
                        <a href="/admin/referral-letters" class="secondary-btn">Batal</a>
                        <button type="submit" class="submit-btn">Simpan Surat Rujukan</button>
                    </div>
                </form>
            </section>
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
