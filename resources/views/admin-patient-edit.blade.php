<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient - Khayra Physio</title>
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
            max-width: 1100px;
            margin: 0 auto;
        }
        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
        }
        .title {
            font-size: 38px;
            font-weight: 800;
            color: #22343a;
            margin: 0 0 10px;
        }
        .subtitle {
            font-size: 14px;
            line-height: 1.8;
            color: #6b7280;
            margin: 0 0 22px;
        }
        .top-actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 18px;
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
        .readonly-note {
            margin-top: 6px;
            font-size: 12px;
            color: #7b8794;
            line-height: 1.6;
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 22px;
        }
        .submit-btn {
            border: none;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            padding: 14px 22px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(47,124,122,.16);
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
        @media (max-width: 860px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    
        .field-pair {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            grid-column: 1 / -1;
            align-items: start;
        }
        @media (max-width: 900px) {
            .field-pair {
                grid-template-columns: 1fr;
            }
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
    @include('partials.admin-sidebar', ['activeMenu' => 'patients'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/patients/{{ $patient->id }}" class="ghost-link">← Kembali ke Detail Patient</a>
            </div>

            <section class="section-card">
                <h1 class="title">Edit Patient</h1>
                <p class="subtitle">
                    Perbarui data patient dan lengkapi identitas yang dibutuhkan untuk sistem klinik serta rekam medis.
                </p>

                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/admin/patients/{{ $patient->id }}/update">
                    @csrf

                    <div class="form-grid">
                        <div class="field full">
                            <label>Medical Record Number</label>
                            <div class="readonly-box">
                                {{ $patient->medical_record_number ?: 'Belum terbentuk. Akan dibuat otomatis jika gender dan tanggal lahir sudah lengkap.' }}
                            </div>
                            <div class="readonly-note">
                                Nomor rekam medis bersifat otomatis dan tidak diubah manual dari form ini.
                            </div>
                        </div>

                        <div class="field">
                            <label>Nama Lengkap</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $patient->full_name) }}" required>
                        </div>

                        <div class="field">
                            <label>NIK</label>
                            <input type="text" name="nik" value="{{ old('nik', $patient->nik) }}">
                        </div>

                        <div class="field">
                            <label>Gender</label>
                            <select name="gender">
                                <option value="">Pilih Gender</option>
                                <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                                <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            </select>
                        </div>

                        <div class="field">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date', $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '') }}">
                        </div>

                        <div class="field">
                            <label>WhatsApp</label>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp', $patient->whatsapp) }}" required>
                        </div>

                        <div class="field">
                            <label>Agama</label>
                            <input type="text" name="religion" value="{{ old('religion', $patient->religion) }}">
                        </div>

                        <div class="field">
                            <label>Pekerjaan</label>
                            <input type="text" name="occupation" value="{{ old('occupation', $patient->occupation) }}">
                        </div>

                        <div class="field">
                            <label>Pendidikan</label>
                            <input type="text" name="education" value="{{ old('education', $patient->education) }}">
                        </div>

                        <div class="field">
                            <label>Status Perkawinan</label>
                            <select name="marital_status">
                                <option value="">Pilih Status</option>
                                <option value="Cerai hidup" {{ old('marital_status', $patient->marital_status) == 'Cerai hidup' ? 'selected' : '' }}>Cerai hidup</option>
                                <option value="Cerai mati" {{ old('marital_status', $patient->marital_status) == 'Cerai mati' ? 'selected' : '' }}>Cerai mati</option>
                                <option value="Kawin" {{ old('marital_status', $patient->marital_status) == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                                <option value="Belum kawin" {{ old('marital_status', $patient->marital_status) == 'Belum kawin' ? 'selected' : '' }}>Belum kawin</option>
                            </select>
                        </div>

                        <div class="field full">
                            <label>Alamat</label>
                            <textarea name="address" rows="4">{{ old('address', $patient->address) }}</textarea>
                        </div>
                    </div>

                                                <div class="field-pair">
                            <div class="field">
                                <label>Tahu Khayra dari mana?</label>
                                <select name="referral_source">
                                    @php
                                        $selectedReferralSource = old('referral_source', $patient->referral_source ?? '');
                                        $referralSourceOptions = [
                                            'Instagram' => 'Instagram',
                                            'Google Maps' => 'Google Maps',
                                            'TikTok' => 'TikTok',
                                            'Referral teman/keluarga' => 'Referral teman/keluarga',
                                            'Dokter / tenaga kesehatan' => 'Dokter / tenaga kesehatan',
                                            'Walk-in' => 'Walk-in',
                                            'Website' => 'Website',
                                            'Lainnya' => 'Lainnya',
                                        ];
                                    @endphp
                                    <option value="">Pilih sumber informasi</option>
                                    @foreach($referralSourceOptions as $value => $label)
                                        <option value="{{ $value }}" {{ $selectedReferralSource === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field">
                                <label>Jika lainnya</label>
                                <input type="text" name="referral_source_other" value="{{ old('referral_source_other', $patient->referral_source_other ?? '') }}" placeholder="Contoh: event, komunitas, corporate, dll">
                            </div>
                        </div>

                        <div class="field-pair">
                            <div class="field">
                                <label>Consent Dokumentasi / Publish</label>
                                <select name="documentation_consent">
                                    @php
                                        $selectedDocumentationConsent = old('documentation_consent', $patient->documentation_consent ?? '');
                                        $documentationConsentOptions = [
                                            'Bersedia dipublish' => 'Bersedia dipublish',
                                            'Dokumentasi internal saja' => 'Dokumentasi internal saja',
                                            'Tidak bersedia' => 'Tidak bersedia',
                                        ];
                                    @endphp
                                    <option value="">Pilih consent dokumentasi</option>
                                    @foreach($documentationConsentOptions as $value => $label)
                                        <option value="{{ $value }}" {{ $selectedDocumentationConsent === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field">
                                <label>Catatan Consent Dokumentasi</label>
                                <input type="text" name="documentation_consent_notes" value="{{ old('documentation_consent_notes', $patient->documentation_consent_notes ?? '') }}" placeholder="Contoh: boleh foto tanpa wajah / hanya testimoni tertulis">
                            </div>
                        </div>




                    <div class="actions">
                        <button type="submit" class="submit-btn">Simpan Perubahan</button>
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