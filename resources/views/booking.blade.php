<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f8f8;
            color: #1f2937;
        }

        .page {
            min-height: 100vh;
            padding: 28px;
        }

        .container {
            max-width: 1180px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 22px;
            font-weight: 800;
            color: #22343a;
        }

        .brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 10px;
        }

        .topbar-actions {
            display: flex;
            gap: 10px;
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
            grid-template-columns: 1.15fr .85fr;
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
            font-size: 46px;
            line-height: 1.06;
            color: #22343a;
            max-width: 720px;
        }

        .hero-text {
            margin: 16px 0 0;
            font-size: 14px;
            line-height: 1.95;
            color: #6b7280;
            max-width: 650px;
        }

        .tag-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .tag {
            display: inline-block;
            padding: 9px 13px;
            border-radius: 999px;
            background: #f7faf9;
            border: 1px solid #e7eceb;
            color: #486168;
            font-size: 12px;
            font-weight: 700;
        }

        .hero-side {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            border-radius: 24px;
            padding: 24px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
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

        .hero-side-title {
            margin: 0 0 10px;
            font-size: 28px;
            line-height: 1.2;
            color: #ffffff;
            font-weight: 800;
        }

        .hero-side-text {
            margin: 0;
            font-size: 14px;
            line-height: 1.9;
            color: rgba(255,255,255,.95);
        }

        .check-list {
            margin: 18px 0 0;
            padding-left: 18px;
        }

        .check-list li {
            margin-bottom: 10px;
            font-size: 14px;
            line-height: 1.8;
            color: rgba(255,255,255,.92);
        }

        .form-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.05);
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 22px;
        }

        .form-title {
            margin: 0;
            font-size: 34px;
            line-height: 1.1;
            color: #22343a;
        }

        .form-subtitle {
            margin: 10px 0 0;
            font-size: 14px;
            line-height: 1.85;
            color: #6b7280;
        }

        .mini-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .mini-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 12px;
            background: #f9fbfb;
            border: 1px solid #e7eceb;
            color: #2c5b5a;
            font-size: 13px;
            font-weight: 700;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.7;
            border: 1px solid transparent;
        }

        .alert-success {
            background: #ecfdf5;
            color: #166534;
            border-color: #bbf7d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .field-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .field {
            margin-bottom: 16px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid #dde5e3;
            border-radius: 14px;
            font-size: 14px;
            background: #ffffff;
            color: #111827;
            transition: .2s ease;
            font-family: Arial, sans-serif;
        }

        input,
        select {
            height: 52px;
            padding: 0 16px;
        }

        textarea {
            min-height: 130px;
            padding: 14px 16px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #176f69;
            box-shadow: 0 0 0 4px rgba(23, 111, 105, 0.08);
        }

        .helper {
            margin-top: 7px;
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.6;
        }


        .slot-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .slot-card {
            border: 1px solid #e3ebea;
            border-radius: 18px;
            padding: 16px;
            min-height: 88px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdfd 100%);
            transition: .18s ease;
            position: relative;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.025);
        }

        .slot-card:hover {
            transform: translateY(-1px);
            border-color: #9fcfca;
            box-shadow: 0 12px 24px rgba(47, 124, 122, 0.08);
        }

        .slot-card input {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            pointer-events: none;
        }

        .slot-check {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            border: 2px solid #d6e4e2;
            background: #ffffff;
            flex: 0 0 auto;
            position: relative;
        }

        .slot-card:has(input:checked) {
            border-color: #2f7c7a;
            background: #eef8f6;
            box-shadow: 0 0 0 4px rgba(47, 124, 122, 0.08);
        }

        .slot-card:has(input:checked) .slot-check {
            border-color: #2f7c7a;
            background: #2f7c7a;
        }

        .slot-card:has(input:checked) .slot-check::after {
            content: "";
            position: absolute;
            width: 7px;
            height: 11px;
            border: solid #ffffff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            left: 6px;
            top: 2px;
        }

        .slot-main {
            display: grid;
            gap: 6px;
            min-width: 0;
            flex: 1;
        }

        .slot-time {
            font-size: 20px;
            font-weight: 900;
            color: #22343a;
            letter-spacing: -0.2px;
        }

        .slot-badge {
            width: fit-content;
            padding: 6px 10px;
            border-radius: 999px;
            background: #ecfdf5;
            color: #166534;
            font-size: 11px;
            font-weight: 900;
        }

        .slot-small {
            font-size: 12px;
            color: #64748b;
            font-weight: 800;
            white-space: nowrap;
            text-align: right;
            line-height: 1.4;
        }

        .slot-disabled {
            opacity: .58;
            cursor: not-allowed;
            background: #f8fafc;
        }

        .slot-disabled:hover {
            transform: none;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.025);
            border-color: #e3ebea;
        }

        .slot-disabled .slot-badge {
            background: #fee2e2;
            color: #b91c1c;
        }

        .empty-slot {
            padding: 18px;
            border-radius: 16px;
            border: 1px dashed #d9e2e1;
            background: #fafcfc;
            color: #7b8794;
            font-size: 13px;
            line-height: 1.8;
        }

        @media (max-width: 980px) {
            .slot-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 560px) {
            .slot-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 1080px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }

            .form-header {
                flex-direction: column;
            }
        }

        @media (max-width: 760px) {
            .page {
                padding: 16px;
            }

            .hero,
            .form-card {
                padding: 20px;
                border-radius: 22px;
            }

            .hero-title {
                font-size: 34px;
            }

            .form-title {
                font-size: 28px;
            }

            .field-grid,
            .slot-grid {
                grid-template-columns: 1fr;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .brand {
                font-size: 18px;
            }

            .submit-row {
                flex-direction: column;
                align-items: stretch;
            }

            .submit-btn {
                width: 100%;
                min-width: 0;
            }
        }
    
        .booking-submit-row {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 18px;
            flex-wrap: wrap;
        }
        .booking-submit-btn {
            min-height: 46px;
            border: 0;
            cursor: pointer;
            padding: 0 20px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            white-space: nowrap;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(47,124,122,.16);
        }
        .booking-submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px rgba(47,124,122,.20);
        }
        @media (max-width: 640px) {
            .booking-submit-row {
                justify-content: stretch;
            }
            .booking-submit-btn {
                width: 100%;
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
<div class="page">
    <div class="container">
        <div class="topbar">
            <div class="brand">
                <img src="/images/khayra-logo.png" alt="Khayra Logo">
                <span>Khayra Booking</span>
            </div>

            <div class="topbar-actions">
                <a href="/" class="ghost-link">Home</a>
                <a href="/admin/bookings" class="ghost-link">Admin View</a>
            </div>
        </div>

        <section class="hero">
            <div class="hero-grid">
                <div>
                    <div class="hero-badge">Patient Booking</div>
                    <h1 class="hero-title">Book your therapy session with a clearer and more premium experience.</h1>
                    <p class="hero-text">
                        Jadwalkan sesi fisioterapi Anda melalui sistem booking Khayra Physio dengan alur yang lebih sederhana,
                        rapi, dan mudah dipahami. Isi data patient, pilih layanan, lalu tentukan jadwal terapi yang diinginkan.
                    </p>

                    <div class="tag-row">
                        <span class="tag">Fast Booking</span>
                        <span class="tag">Clear Form</span>
                        <span class="tag">Integrated to Admin</span>
                    </div>
                </div>

                <div class="hero-side">
                    <div>
                        <h2 class="hero-side-title">Booking Notes</h2>
                        <p class="hero-side-text">
                            Pastikan seluruh data yang diisi sudah benar agar admin dapat memproses booking
                            dengan lebih cepat dan akurat.
                        </p>
                    </div>

                    <ul class="check-list">
                        <li>Isi nama patient dengan lengkap.</li>
                        <li>Gunakan nomor WhatsApp yang aktif.</li>
                        <li>Pilih layanan yang sesuai kebutuhan terapi.</li>
                        <li>Tentukan tanggal dan jam yang diinginkan dengan jelas.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="form-card">
            <div class="form-header">
                <div>
                    <h2 class="form-title">Form Booking Pasien</h2>
                    <p class="form-subtitle">
                        Silakan isi data booking di bawah ini. Informasi akan diteruskan ke admin untuk proses konfirmasi.
                    </p>
                </div>

                <div class="mini-actions">
                    <a href="/" class="mini-link">Home</a>
                    <a href="/admin/bookings" class="mini-link">Admin View</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Periksa kembali input Anda:</strong>
                    <ul style="margin:8px 0 0 18px; padding:0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/booking">
                @csrf

                <div class="field-grid">
                    <div class="field full">
                        <label>Nama Lengkap</label>
                        <input
                            type="text"
                            name="full_name"
                            value="{{ old('full_name') }}"
                            placeholder="Masukkan nama lengkap"
                        >
                    </div>

                    <div class="field full">
                        <label>Nomor WhatsApp</label>
                        <input
                            type="text"
                            name="whatsapp"
                            value="{{ old('whatsapp') }}"
                            placeholder="Masukkan nomor WhatsApp aktif"
                        >
                    </div>

                    <div class="field">
                        <label>Layanan</label>
                        <select name="service">
                            <option value="">Pilih layanan</option>
                            <option value="Fisioterapi Umum" {{ old('service') == 'Fisioterapi Umum' ? 'selected' : '' }}>Fisioterapi Umum</option>
                            <option value="Sports Injury Rehab" {{ old('service') == 'Sports Injury Rehab' ? 'selected' : '' }}>Sports Injury Rehab</option>
                            <option value="Posture / Spinal Care" {{ old('service') == 'Posture / Spinal Care' ? 'selected' : '' }}>Posture / Spinal Care</option>
                            <option value="Home Visit Therapy" {{ old('service') == 'Home Visit Therapy' ? 'selected' : '' }}>Home Visit Therapy</option>
                        </select>
                        <div class="helper">Pilih layanan yang paling sesuai dengan kebutuhan Anda.</div>
                    </div>

                    <div class="field">
                        <label>Tanggal Booking</label>
                        <input
                            type="date"
                            id="booking_date"
                            name="booking_date"
                            value="{{ old('booking_date', $selectedDate ?? now()->toDateString()) }}"
                            min="{{ now()->toDateString() }}"
                        >
                        <div class="helper">Pilih tanggal terapi yang Anda inginkan. Slot jam akan mengikuti tanggal ini.</div>
                    </div>

                    <div class="field full">
                        <label>Jam Booking</label>

                        @if(isset($timeSlots) && $timeSlots->count())
                            <div class="slot-grid">
                                @foreach($timeSlots as $slot)
                                    <label class="slot-card {{ !$slot['available'] ? 'slot-disabled' : '' }}">
                                        <input
                                            type="radio"
                                            name="booking_time"
                                            value="{{ $slot['time'] }}"
                                            {{ old('booking_time') == $slot['time'] ? 'checked' : '' }}
                                            {{ !$slot['available'] ? 'disabled' : '' }}
                                        >

                                        <span class="slot-check"></span>

                                        <span class="slot-main">
                                            <span class="slot-time">{{ $slot['time'] }}</span>
                                            <span class="slot-badge">
                                                {{ $slot['available'] ? 'Available' : 'FULL' }}
                                            </span>
                                        </span>

                                        <span class="slot-small">
                                            {{ $slot['booked'] }}/{{ $slot['capacity'] }}<br>booked
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-slot">
                                Belum ada slot tersedia untuk tanggal ini. Silakan pilih tanggal lain atau hubungi admin.
                            </div>
                        @endif

                        <div class="helper">Slot mengikuti jadwal availability therapist dan otomatis disabled jika sudah penuh.</div>
                    </div>

                    <div class="field full">
                        <label>Keluhan</label>
                        <textarea
                            name="complaint"
                            placeholder="Tuliskan keluhan singkat atau area yang sedang dirasakan"
                        >{{ old('complaint') }}</textarea>
                    </div>
                </div>

                <div class="submit-row">
                    <div class="submit-note">
                        Dengan mengirim form ini, data booking Anda akan masuk ke sistem admin Khayra Physio untuk proses tindak lanjut.
                    </div>
                    <div class="booking-submit-row">
                    <button type="submit" class="booking-submit-btn">Kirim Booking</button>
                </div>
                </div>
            </form>
        </section>
    </div>
</div>
<script>
    const bookingDateInput = document.getElementById('booking_date');

    if (bookingDateInput) {
        bookingDateInput.addEventListener('change', function () {
            if (this.value) {
                window.location.href = '/booking?date=' + encodeURIComponent(this.value);
            }
        });
    }
</script>
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