<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Visit & Rekam Medis - Khayra Physio ERM</title>
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
            max-width: 1120px;
            margin: 0 auto;
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
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
            font-weight: 800;
        }

        .section-card,
        .info-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
            margin-bottom: 18px;
        }

        .title {
            font-size: 38px;
            font-weight: 800;
            color: #22343a;
            margin: 0 0 10px;
            line-height: 1.08;
        }

        .subtitle {
            font-size: 14px;
            line-height: 1.8;
            color: #6b7280;
            margin: 0 0 22px;
            max-width: 860px;
        }

        .badge {
            display: inline-block;
            padding: 8px 13px;
            border-radius: 999px;
            background: #eef5f4;
            color: #35565d;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        .info-box {
            background: #fbfcfc;
            border: 1px solid #edf1f0;
            border-radius: 18px;
            padding: 16px;
        }

        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .45px;
            color: #7b8794;
            font-weight: 800;
            margin-bottom: 7px;
        }

        .info-value {
            font-size: 14px;
            color: #22343a;
            font-weight: 800;
            line-height: 1.55;
            word-break: break-word;
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
            font-weight: 800;
            color: #334155;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 14px 14px;
            border: 1px solid #dde5e3;
            border-radius: 14px;
            font-size: 14px;
            background: #ffffff;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #176f69;
            box-shadow: 0 0 0 4px rgba(23,111,105,.08);
        }

        .hint {
            margin-top: 7px;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
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
        }

        .secondary-btn {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            padding: 14px 18px;
            border-radius: 14px;
            background: #f7faf9;
            border: 1px solid #e6ebea;
            color: #2c5b5a;
            font-size: 14px;
            font-weight: 800;
        }

        @media (max-width: 980px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .form-grid,
            .info-grid { grid-template-columns: 1fr; }
            .title { font-size: 32px; }
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
@php
    $selectedBooking = $selectedBooking ?? null;

    $prefillPatientId = old('patient_id', optional(optional($selectedBooking)->patient)->id);
    $prefillBookingId = old('booking_id', optional($selectedBooking)->id);
    $prefillVisitDate = old('visit_date', optional($selectedBooking)->booking_date ?? now()->format('Y-m-d'));
    $prefillRoomName = old('room_name', optional($selectedBooking)->room_name);
    $prefillNotes = old('notes', $selectedBooking ? trim(
        "Keluhan awal dari booking: " . ($selectedBooking->complaint ?: '-') . "\n" .
        "Layanan booking: " . ($selectedBooking->service ?: '-') . "\n" .
        "Jadwal booking: " . ($selectedBooking->booking_date ?: '-') . " " . ($selectedBooking->booking_time ?: '-')
    ) : '');
@endphp

<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'visits'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/visits" class="ghost-link">← Kembali ke Visit List</a>
                @if($selectedBooking ?? null)
                    <a href="/admin/bookings/{{ $selectedBooking->id }}" class="ghost-link">Lihat Booking #{{ $selectedBooking->id }}</a>
                @endif
            </div>

            @if($selectedBooking ?? null)
                <section class="info-card">
                    <span class="badge">Booking Linked</span>
                    <h1 class="title">Buat Visit dari Booking</h1>
                    <p class="subtitle">
                        Data booking sudah dibawa ke form visit. Setelah visit dibuat, tahap berikutnya adalah mengisi rekam medis pasien.
                    </p>

                    <div class="info-grid">
                        <div class="info-box">
                            <div class="info-label">Pasien</div>
                            <div class="info-value">{{ $selectedBooking->full_name ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">WhatsApp</div>
                            <div class="info-value">{{ $selectedBooking->whatsapp ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Layanan</div>
                            <div class="info-value">{{ $selectedBooking->service ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Jadwal</div>
                            <div class="info-value">{{ $selectedBooking->booking_date ?: '-' }} {{ $selectedBooking->booking_time ?: '' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Room</div>
                            <div class="info-value">{{ $selectedBooking->room_name ?: 'Belum ditentukan' }}</div>
                        </div>
                    </div>
                </section>
            @endif

            <section class="section-card">
                <span class="badge">Visit & Rekam Medis Flow</span>
                <h1 class="title">{{ ($selectedBooking ?? null) ? 'Lengkapi Data Visit' : 'Create Physiotherapy Visit' }}</h1>
                <p class="subtitle">
                    Buat visit fisioterapi dengan menghubungkan pasien, booking, fisioterapis, dan status sesi.
                    Visit ini akan menjadi pintu masuk untuk pengisian rekam medis.
                </p>

                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if(($selectedBooking ?? null) && !$selectedBooking->patient)
                    <div class="error-box">
                        Booking ini belum terhubung ke biodata pasien. Buat atau link pasien dulu sebelum membuat visit.
                    </div>
                @endif

                <form method="POST" action="/admin/visits">
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
                            <div class="hint">Pilih biodata pasien yang akan dikaitkan dengan visit ini.</div>
                        </div>

                        <div class="field">
                            <label>Related Booking</label>
                            <select name="booking_id">
                                <option value="">Pilih Booking (opsional)</option>
                                @foreach($bookings as $booking)
                                    <option value="{{ $booking->id }}" {{ (string)$prefillBookingId === (string)$booking->id ? 'selected' : '' }}>
                                        #{{ $booking->id }} - {{ $booking->full_name }} - {{ $booking->booking_date }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="hint">Gunakan ini agar visit tersambung ke appointment awal.</div>
                        </div>

                        <div class="field">
                            <label>Physiotherapy Staff</label>
                            <select name="therapist_id" required>
                                <option value="">Pilih Staff</option>
                                @foreach($therapists as $therapist)
                                    <option value="{{ $therapist->id }}" {{ old('therapist_id') == $therapist->id ? 'selected' : '' }}>
                                        {{ $therapist->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Visit Date</label>
                            <input type="date" name="visit_date" value="{{ $prefillVisitDate }}" required>
                        </div>

                        <div class="field">
                            <label>Room</label>
                            <select name="room_name">
                                <option value="">Belum ditentukan</option>
                                @foreach(($roomOptions ?? []) as $room)
                                    <option value="{{ $room }}" {{ $prefillRoomName === $room ? 'selected' : '' }}>
                                        {{ $room }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="hint">Room otomatis mengikuti booking bila visit dibuat dari appointment.</div>
                        </div>

                        <div class="field">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="scheduled" {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="field full">
                            <label>Clinical Notes</label>
                            <textarea name="notes" rows="5" placeholder="Tulis catatan awal visit, keluhan utama, atau informasi dari booking.">{{ $prefillNotes }}</textarea>
                            <div class="hint">Catatan ini membantu fisioterapis memulai rekam medis dari konteks appointment.</div>
                        </div>
                    </div>

                    <div class="actions">
                        @if($selectedBooking ?? null)
                            <a href="/admin/bookings/{{ $selectedBooking->id }}" class="secondary-btn">Batal</a>
                        @else
                            <a href="/admin/visits" class="secondary-btn">Batal</a>
                        @endif

                        <button type="submit" class="submit-btn" {{ (($selectedBooking ?? null) && !$selectedBooking->patient) ? 'disabled style=opacity:.55;cursor:not-allowed;' : '' }}>
                            Simpan Visit
                        </button>
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
