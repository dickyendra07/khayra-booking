<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Dokumen Pembelian Paket - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1120px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
        .badge { display: inline-flex; padding: 8px 13px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px; }
        .title { margin: 0; font-size: 38px; line-height: 1.05; color: #22343a; font-weight: 900; letter-spacing: -1px; }
        .subtitle { margin: 10px 0 0; color: #6b7280; font-size: 14px; line-height: 1.8; max-width: 780px; }
        .card { background: #ffffff; border: 1px solid #ecefef; border-radius: 28px; padding: 26px; box-shadow: 0 14px 34px rgba(15,23,42,.05); margin-bottom: 18px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .full { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 900; color: #334155; }
        input, select, textarea { width: 100%; padding: 14px; border: 1px solid #dde5e3; border-radius: 14px; font-size: 14px; background: #ffffff; color: #111827; font-family: Arial, sans-serif; }
        textarea { min-height: 120px; line-height: 1.7; }
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
    @include('partials.admin-sidebar', ['activeMenu' => 'package-treatments'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Package Treatment</span>
                    <h1 class="title">Buat Dokumen Pembelian Paket</h1>
                    <p class="subtitle">Buat surat pembelian paket treatment dengan tabel rekap kedatangan sesuai jumlah session.</p>
                </div>
                <a href="/admin/package-treatments" class="btn btn-soft">Kembali</a>
            </div>

            <form method="POST" action="/admin/package-treatments">
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
                            <label>Billing Terkait</label>
                            <select name="billing_id">
                                <option value="">Tanpa billing</option>
                                @foreach($billings as $billing)
                                    <option value="{{ $billing->id }}" {{ old('billing_id', $selectedBillingId) == $billing->id ? 'selected' : '' }}>
                                        {{ $billing->invoice_number }} - {{ optional($billing->patient)->full_name }} - Rp {{ number_format($billing->grand_total ?? $billing->amount ?? 0, 0, ',', '.') }}
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
                            <label>Tanggal Dokumen</label>
                            <input type="date" name="document_date" value="{{ old('document_date', now()->format('Y-m-d')) }}">
                        </div>

                        <div class="full">
                            <label>Nama Paket</label>
                            <input type="text" name="package_name" value="{{ old('package_name', $prefill['package_name'] ?? '') }}" placeholder="Contoh: Light Package (3x Kedatangan) Musculoskeletal Rehabilitation" required>
                        </div>

                        <div>
                            <label>Tipe Paket</label>
                            <select name="package_type">
                                <option value="">Pilih tipe</option>
                                <option value="Light Package" {{ old('package_type', $prefill['package_type'] ?? '') === 'Light Package' ? 'selected' : '' }}>Light Package</option>
                                <option value="Medium Package" {{ old('package_type', $prefill['package_type'] ?? '') === 'Medium Package' ? 'selected' : '' }}>Medium Package</option>
                                <option value="Full Package" {{ old('package_type', $prefill['package_type'] ?? '') === 'Full Package' ? 'selected' : '' }}>Full Package</option>
                                <option value="Custom Package" {{ old('package_type', $prefill['package_type'] ?? '') === 'Custom Package' ? 'selected' : '' }}>Custom Package</option>
                            </select>
                        </div>

                        <div>
                            <label>Total Session / Kedatangan</label>
                            <input type="number" name="total_sessions" min="1" max="24" value="{{ old('total_sessions', $prefill['total_sessions'] ?? 3) }}" required>
                        </div>

                        <div>
                            <label>Payment Package</label>
                            <input type="number" name="package_price" min="0" step="1" value="{{ old('package_price', $prefill['package_price'] ?? 0) }}" required>
                        </div>

                        <div>
                            <label>Date Buying Package</label>
                            <input type="date" name="buying_date" value="{{ old('buying_date', $prefill['buying_date'] ?? now()->format('Y-m-d')) }}">
                        </div>

                        <div>
                            <label>Valid Until Package</label>
                            <input type="date" name="valid_until" value="{{ old('valid_until', $prefill['valid_until'] ?? now()->addMonths(3)->format('Y-m-d')) }}">
                            <div class="hint">Default 3 bulan, bisa disesuaikan untuk paket 6x/12x.</div>
                        </div>

                        <div class="full">
                            <label>Syarat Ketentuan</label>
                            <textarea name="terms">{{ old('terms', "1. Penggunaan paket ini sesuai dengan rujukan fisioterapi dimana masa berlaku paket sesuai dengan di atas, dihitung sejak kunjungan pertama digunakan.\n2. Setiap kedatangan pasien akan direkap pada formulir ini.\n3. Biaya paket tidak dapat dikembalikan (refund) apapun alasannya.") }}</textarea>
                        </div>

                        <div class="full">
                            <label>Catatan Internal</label>
                            <textarea name="notes" placeholder="Opsional, tidak wajib tampil di dokumen print.">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="actions">
                        <a href="/admin/package-treatments" class="btn btn-soft">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan & Print</button>
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
