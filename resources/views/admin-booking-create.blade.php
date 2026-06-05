<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Appointment - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1040px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
        .badge { display: inline-flex; padding: 8px 13px; border-radius: 999px; background: #eef5f4; color: #35565d; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px; }
        .title { margin: 0; font-size: 40px; line-height: 1.05; color: #22343a; font-weight: 900; letter-spacing: -1px; }
        .subtitle { margin: 10px 0 0; color: #6b7280; font-size: 14px; line-height: 1.8; max-width: 800px; }
        .card { background: #fff; border: 1px solid #ecefef; border-radius: 28px; padding: 26px; box-shadow: 0 14px 34px rgba(15,23,42,.05); }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .full { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 900; color: #334155; }
        input, select, textarea { width: 100%; padding: 14px; border: 1px solid #dde5e3; border-radius: 14px; font-size: 14px; background: #fff; color: #111827; font-family: Arial, sans-serif; }
        textarea { min-height: 110px; line-height: 1.7; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #176f69; box-shadow: 0 0 0 4px rgba(23,111,105,.08); }
        .btn { min-height: 44px; border: 0; cursor: pointer; padding: 0 18px; border-radius: 14px; font-size: 13px; font-weight: 900; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-family: Arial, sans-serif; white-space: nowrap; }
        .btn-primary { background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%); color: #fff; box-shadow: 0 12px 24px rgba(47,124,122,.16); }
        .btn-soft { color: #2f7c7a; background: #fff; border: 1px solid #d8ebe7; }
        .actions { display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
        .error-box { background: #fff1f2; border: 1px solid #ffe0e6; color: #be123c; padding: 14px 16px; border-radius: 16px; margin-bottom: 18px; font-size: 13px; line-height: 1.8; }
        @media (max-width: 900px) { .layout { display: block; } .main { padding: 16px; } .grid { grid-template-columns: 1fr; } .full { grid-column: auto; } .title { font-size: 32px; } }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'bookings'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <div>
                    <span class="badge">Admin Appointment</span>
                    <h1 class="title">Buat Appointment</h1>
                    <p class="subtitle">Buat appointment pasien langsung dari admin dan hubungkan dengan fisioterapis.</p>
                </div>
                <a href="/admin/bookings/calendar?date={{ $prefill['booking_date'] }}" class="btn btn-soft">Kembali Calendar</a>
            </div>

            <form method="POST" action="/admin/bookings/create" class="card">
                @csrf

                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="grid">
                    <div>
                        <label>Patient Existing</label>
                        <select name="patient_id" onchange="syncPatient(this)">
                            <option value="">Tanpa patient existing</option>
                            @foreach($patients as $patient)
                                <option
                                    value="{{ $patient->id }}"
                                    data-name="{{ $patient->full_name }}"
                                    data-wa="{{ $patient->whatsapp }}"
                                    {{ old('patient_id') == $patient->id ? 'selected' : '' }}
                                >
                                    {{ $patient->full_name }}{{ $patient->medical_record_number ? ' - ' . $patient->medical_record_number : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>Fisioterapis</label>
                        <select name="therapist_id">
                            <option value="">Belum ditentukan</option>
                            @foreach($therapists as $therapist)
                                <option value="{{ $therapist->id }}" {{ old('therapist_id', $prefill['therapist_id']) == $therapist->id ? 'selected' : '' }}>
                                    {{ $therapist->full_name ?? $therapist->name ?? ('Therapist #' . $therapist->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>Room</label>
                        <select name="room_name">
                            <option value="">Belum ditentukan</option>
                            @foreach(($roomOptions ?? []) as $room)
                                <option value="{{ $room }}" {{ old('room_name', $prefill['room_name'] ?? '') === $room ? 'selected' : '' }}>
                                    {{ $room }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>Nama Pasien</label>
                        <input type="text" name="full_name" id="fullNameInput" value="{{ old('full_name') }}" required placeholder="Nama pasien">
                    </div>

                    <div>
                        <label>WhatsApp</label>
                        <input type="text" name="whatsapp" id="whatsappInput" value="{{ old('whatsapp') }}" required placeholder="08xxxxxxxxxx">
                    </div>

                    <div>
                        <label>Layanan / Program</label>
                        <select name="service" required>
                            <option value="">Pilih layanan</option>
                            @foreach($services as $service)
                                <option value="{{ $service->name }}" {{ old('service') === $service->name ? 'selected' : '' }}>
                                    {{ $service->name }}
                                </option>
                            @endforeach
                            <option value="Fisioterapi Umum" {{ old('service') === 'Fisioterapi Umum' ? 'selected' : '' }}>Fisioterapi Umum</option>
                        </select>
                    </div>

                    <div>
                        <label>Status</label>
                        <select name="status" required>
                            @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'arrived' => 'Arrived', 'in_treatment' => 'In Treatment', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'no_show' => 'No Show'] as $key => $label)
                                <option value="{{ $key }}" {{ old('status', 'confirmed') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>Tanggal</label>
                        <input type="date" name="booking_date" value="{{ old('booking_date', $prefill['booking_date']) }}" required>
                    </div>

                    <div>
                        <label>Jam</label>
                        <select name="booking_time" required>

                            @foreach(['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00'] as $slot)
                                <option value="{{ $slot }}" {{ old('booking_time', substr((string) $prefill['booking_time'], 0, 5)) === $slot ? 'selected' : '' }}>{{ $slot }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="full">
                        <label>Keluhan Awal / Catatan</label>
                        <textarea name="complaint" placeholder="Keluhan awal atau catatan appointment">{{ old('complaint') }}</textarea>
                    </div>
                </div>

                <div class="actions">
                    <a href="/admin/bookings/calendar?date={{ $prefill['booking_date'] }}" class="btn btn-soft">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Appointment</button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
function syncPatient(select) {
    const selected = select.options[select.selectedIndex];
    if (!selected || !selected.value) return;

    document.getElementById('fullNameInput').value = selected.dataset.name || '';
    document.getElementById('whatsappInput').value = selected.dataset.wa || '';
}
</script>
</body>
</html>
