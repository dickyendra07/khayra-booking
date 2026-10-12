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
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'rooms'])

    <main class="main">
        <div class="container">
            <section class="section-card">
                <h1 class="title">Master Ruangan</h1>
                <p class="subtitle">
                    Biaya penggunaan ruangan adalah komponen tambahan (bukan perubahan harga treatment) dan menjadi baris tagihan tersendiri tanpa diskon.
                    Semua ruangan <strong>default non-billable</strong>. Tarif dan satuan harus disetujui klinik; sistem tidak menagih otomatis dan hanya
                    menyarankan baris biaya di kasir untuk ruangan berbayar yang tarif dan satuannya lengkap. Harga yang tersimpan di invoice adalah snapshot saat transaksi.
                </p>

                @php($chargeableCount = $rooms->filter(fn ($room) => $room->is_chargeable)->count())
                <div class="error-box" style="background:{{ $chargeableCount ? '#fffbeb' : '#f1f5f9' }};border-color:{{ $chargeableCount ? '#fde68a' : '#e2e8f0' }};color:#334155;">
                    @if($chargeableCount === 0)
                        <strong>Belum ada ruangan berbayar.</strong> Tarif resmi dan satuan tarif menunggu persetujuan klinik; sampai itu semua ruangan tidak ditagih.
                    @else
                        <strong>{{ $chargeableCount }} ruangan berbayar aktif.</strong> Kasir hanya <em>menyarankan</em> baris biaya (tidak otomatis), satu kali per visit.
                        Mengubah tarif di sini tidak mengubah invoice yang sudah terbit (tarif tersimpan sebagai snapshot di invoice).
                    @endif
                </div>

                @if(session('success'))
                    <div class="error-box" style="background:#ecfdf5;border-color:#bbf7d0;color:#166534;">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <h2 style="font-size:18px;margin:18px 0 8px;">Tambah Ruangan</h2>
                <form method="POST" action="/admin/rooms">
                    @csrf
                    <div class="form-grid">
                        <div class="field"><label>Nama Ruangan</label><input type="text" name="name" required maxlength="100"></div>
                        <div class="field"><label>Status</label>
                            <select name="status"><option value="active">Aktif</option><option value="inactive">Nonaktif</option></select>
                        </div>
                    </div>
                    <input type="hidden" name="is_billable" value="0">
                    <div class="actions"><button type="submit" class="submit-btn">Tambah (non-billable)</button></div>
                </form>

                <h2 style="font-size:18px;margin:26px 0 8px;">Daftar Ruangan</h2>
                @foreach($rooms as $room)
                    <form method="POST" action="/admin/rooms/{{ $room->id }}/update" class="room-card">
                        @csrf
                        <div class="room-head">
                            <strong>{{ $room->name }}</strong>
                            <span class="room-pill {{ $room->is_chargeable ? 'on' : 'off' }}">
                                {{ $room->is_chargeable ? 'Billable: Rp ' . number_format($room->rate, 0, ',', '.') . ' ' . $room->rate_unit_label : 'Non-billable' }}
                            </span>
                        </div>
                        <div class="form-grid">
                            <div class="field"><label>Status</label>
                                <select name="status">
                                    <option value="active" {{ $room->status === 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ $room->status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            <div class="field"><label>Billable (berbayar)</label>
                                <input type="hidden" name="is_billable" value="0">
                                <label style="font-weight:600;"><input type="checkbox" name="is_billable" value="1" {{ $room->is_billable ? 'checked' : '' }}> Ruangan ini dikenai biaya</label>
                            </div>
                            <div class="field"><label>Tarif (Rp)</label><input type="number" name="rate" min="0" step="1" value="{{ $room->rate !== null ? (int) $room->rate : '' }}" placeholder="Menunggu persetujuan klinik"></div>
                            <div class="field"><label>Satuan Tarif</label>
                                <select name="rate_unit">
                                    <option value="">- belum ditentukan -</option>
                                    @foreach(\App\Models\Room::RATE_UNITS as $unitKey => $unitLabel)
                                        <option value="{{ $unitKey }}" {{ $room->rate_unit === $unitKey ? 'selected' : '' }}>{{ $unitLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field full"><label>Catatan</label><input type="text" name="notes" value="{{ $room->notes }}" maxlength="1000"></div>
                        </div>
                        <div class="actions"><button type="submit" class="submit-btn">Simpan {{ $room->name }}</button></div>
                    </form>
                @endforeach
            </section>
        </div>
    </main>
</div>
<style>
    .room-card { border: 1px solid #e6ebea; border-radius: 18px; padding: 16px; margin-bottom: 14px; background: #fbfcfc; }
    .room-head { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 10px; }
    .room-pill { padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 800; }
    .room-pill.on { background: #dcfce7; color: #166534; }
    .room-pill.off { background: #f1f5f9; color: #475569; }
</style>
</body>
</html>
