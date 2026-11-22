<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Ruangan - Khayra Physio</title>
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
        .top-actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 18px;
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
        .field.full { grid-column: 1 / -1; }
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
            margin-top: 22px;
        }

        @media (max-width: 860px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .form-grid { grid-template-columns: 1fr; }
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
    @include('partials.ui-assets')
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'rooms'])

    <main class="main">
        <div class="container">
            <section class="section-card">
                <h1 class="title">Master Ruangan</h1>
                <p class="subtitle">
                    Biaya penggunaan ruangan <strong>dihitung per sesi</strong> (bukan per jam). Biaya ini adalah komponen tambahan (bukan perubahan harga treatment),
                    menjadi baris tagihan tersendiri, dan <strong>tidak terkena diskon program</strong>. Semua ruangan <strong>default non-billable</strong>;
                    tarif resmi harus disetujui klinik. Sistem tidak menagih otomatis &mdash; kasir hanya menyarankan baris biaya, satu kali per visit,
                    untuk ruangan berbayar yang tarifnya lengkap. Harga di invoice adalah snapshot saat transaksi.
                </p>

                @php($chargeableCount = $rooms->filter(fn ($room) => $room->is_chargeable)->count())
                @if($chargeableCount === 0)
                    <div class="kx-alert kx-alert--info">
                        <strong>Belum ada ruangan berbayar.</strong> Tarif resmi per sesi menunggu persetujuan klinik; sampai itu semua ruangan tidak ditagih.
                    </div>
                @else
                    <div class="kx-alert kx-alert--warning">
                        <strong>{{ $chargeableCount }} ruangan berbayar aktif.</strong> Kasir hanya <em>menyarankan</em> baris biaya (tidak otomatis), satu kali per visit.
                        Mengubah tarif di sini tidak mengubah invoice yang sudah terbit (tarif tersimpan sebagai snapshot di invoice).
                    </div>
                @endif

                @if(session('success'))
                    <div class="kx-alert kx-alert--success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="kx-alert kx-alert--danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <h2 class="room-section-title">Tambah Ruangan</h2>
                <form method="POST" action="/admin/rooms" class="room-card">
                    @csrf
                    <div class="form-grid">
                        <div class="field"><label>Nama Ruangan</label><input type="text" name="name" required maxlength="100"></div>
                        <div class="field"><label>Status</label>
                            <select name="status"><option value="active">Aktif</option><option value="inactive">Nonaktif</option></select>
                        </div>
                    </div>
                    <input type="hidden" name="is_billable" value="0">
                    <span class="kx-help">Ruangan baru otomatis non-billable. Tarif per sesi diatur setelah disetujui klinik.</span>
                    <div class="kx-btn-group kx-btn-group--end"><button type="submit" class="kx-btn kx-btn--primary submit-btn">Tambah (non-billable)</button></div>
                </form>

                <h2 class="room-section-title">Daftar Ruangan</h2>
                @foreach($rooms as $room)
                    <form method="POST" action="/admin/rooms/{{ $room->id }}/update" class="room-card">
                        @csrf
                        <div class="room-head">
                            <strong>{{ $room->name }}</strong>
                            <span class="kx-badge {{ $room->is_chargeable ? 'kx-badge--success' : '' }}">
                                {{ $room->is_chargeable ? 'Billable: Rp ' . number_format($room->rate, 0, ',', '.') . ' ' . strtolower($room->rate_unit_label) : 'Non-billable' }}
                            </span>
                        </div>
                        <div class="room-grid">
                            <div class="field"><label>Status</label>
                                <select name="status">
                                    <option value="active" {{ $room->status === 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ $room->status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            <div class="field"><label>Tarif Ruangan</label>
                                <div class="kx-input-group">
                                    <span class="kx-addon">Rp</span>
                                    <input type="number" name="rate" min="0" step="1" value="{{ $room->rate !== null ? (int) $room->rate : '' }}" placeholder="Menunggu persetujuan klinik">
                                </div>
                            </div>
                            <div class="field"><label>Satuan Tarif</label>
                                <select name="rate_unit">
                                    <option value="">- belum ditentukan -</option>
                                    {{-- Kebijakan klinik: tarif ruangan PER SESI. Satuan lain hanya tampil bila sudah tersimpan (data lama). --}}
                                    @foreach(\App\Models\Room::RATE_UNITS as $unitKey => $unitLabel)
                                        @if($unitKey === 'per_session' || $room->rate_unit === $unitKey)
                                            <option value="{{ $unitKey }}" {{ $room->rate_unit === $unitKey ? 'selected' : '' }}>{{ $unitLabel }}{{ $unitKey === 'per_session' ? '' : ' (satuan lama)' }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="field room-save"><div class="kx-btn-group kx-btn-group--end"><button type="submit" class="kx-btn kx-btn--primary submit-btn">Simpan</button></div></div>
                            <div class="field full">
                                <input type="hidden" name="is_billable" value="0">
                                <label class="kx-check">
                                    <input type="checkbox" name="is_billable" value="1" data-kx-fill-unit {{ $room->is_billable ? 'checked' : '' }}>
                                    <span><strong>Ruangan ini dikenai biaya (per sesi)</strong><small>Wajib mengisi tarif lebih dari 0. Biaya tampil sebagai baris terpisah di invoice dan tidak didiskon.</small></span>
                                </label>
                            </div>
                            <div class="field full"><label>Catatan</label><input type="text" name="notes" value="{{ $room->notes }}" maxlength="1000"></div>
                        </div>
                    </form>
                @endforeach
            </section>
        </div>
    </main>
</div>
<style>
    .room-section-title { font-size: 18px; margin: 26px 0 10px; color: #22343a; }
    .room-card { border: 1px solid #e6ebea; border-radius: 18px; padding: 16px; margin-bottom: 14px; background: #fbfcfc; }
    .room-head { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 12px; }
    .room-grid { display: grid; grid-template-columns: 1fr 1.4fr 1fr auto; gap: 14px; align-items: end; }
    .room-grid .field.full { grid-column: 1 / -1; }
    .room-grid .room-save { align-self: end; }
    @media (max-width: 860px) {
        .room-grid { grid-template-columns: 1fr; }
        .room-grid .room-save .kx-btn-group { justify-content: stretch; }
        .room-grid .room-save .kx-btn { width: 100%; }
    }
</style>
<script>
    // Kebijakan klinik: tarif ruangan per sesi. Satuan hanya diisi otomatis saat staf mencentang "dikenai biaya";
    // ruangan non-billable tidak pernah diberi satuan/tarif otomatis.
    document.querySelectorAll('input[data-kx-fill-unit]').forEach(function (box) {
        box.addEventListener('change', function () {
            var unit = box.closest('form').querySelector('select[name="rate_unit"]');
            if (box.checked && unit && !unit.value) { unit.value = 'per_session'; }
        });
    });
</script>
</body>
</html>
