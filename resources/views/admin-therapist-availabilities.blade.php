<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapist Availability - Khayra Physio</title>
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
        .container { max-width: 1320px; margin: 0 auto; }

        .top-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .ghost-link, .primary-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 800;
        }
        .ghost-link {
            background: #ffffff;
            border: 1px solid #e6ebea;
            color: #2c5b5a;
        }
        .primary-link {
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            border: none;
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
            grid-template-columns: 1.12fr .88fr;
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
            font-size: 42px;
            line-height: 1.06;
            color: #22343a;
            font-weight: 900;
        }
        .hero-text {
            margin: 16px 0 0;
            font-size: 14px;
            line-height: 1.95;
            color: #6b7280;
            max-width: 760px;
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
        .hero-side > * { position: relative; z-index: 1; }
        .hero-side h3 { margin: 0 0 8px; font-size: 25px; }
        .hero-side p {
            margin: 0;
            font-size: 13px;
            line-height: 1.85;
            color: rgba(255,255,255,.94);
        }
        .summary-stack { display: grid; gap: 10px; margin-top: 18px; }
        .summary-item {
            padding: 13px 14px;
            border-radius: 16px;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.16);
            font-size: 13px;
            line-height: 1.55;
            font-weight: 800;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 16px;
            font-size: 13px;
            line-height: 1.7;
        }
        .alert-success { background: #ecfdf5; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        .month-filter-card,
        .section-card {
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
            margin-bottom: 18px;
        }
        .month-filter-card {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 16px;
            flex-wrap: wrap;
        }
        .month-title {
            margin: 0;
            font-size: 24px;
            color: #22343a;
            font-weight: 900;
        }
        .month-subtitle {
            margin: 7px 0 0;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.7;
        }
        .month-form {
            display: flex;
            gap: 10px;
            align-items: end;
            flex-wrap: wrap;
        }

        .content-grid {
            display: grid;
            grid-template-columns: .86fr 1.14fr;
            gap: 18px;
            align-items: start;
        }
        .section-title {
            margin: 0;
            font-size: 24px;
            color: #22343a;
            font-weight: 900;
        }
        .section-subtitle {
            margin: 8px 0 18px;
            font-size: 13px;
            line-height: 1.8;
            color: #6b7280;
        }

        .field { margin-bottom: 15px; }
        .field.full { grid-column: 1 / -1; }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 800;
            color: #334155;
        }
        input, select, textarea {
            width: 100%;
            border: 1px solid #dde5e3;
            border-radius: 14px;
            font-size: 14px;
            background: #ffffff;
            color: #111827;
            font-family: Arial, sans-serif;
        }
        input, select { height: 50px; padding: 0 14px; }
        textarea {
            min-height: 96px;
            padding: 14px;
            resize: vertical;
            line-height: 1.7;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #176f69;
            box-shadow: 0 0 0 4px rgba(23,111,105,.08);
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .day-chip-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 9px;
        }
        .day-chip { position: relative; display: block; }
        .day-chip input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .day-chip span {
            min-height: 58px;
            border-radius: 16px;
            border: 1px solid #dde5e3;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            line-height: 1.35;
            color: #486168;
            font-weight: 900;
            cursor: pointer;
            transition: .18s ease;
        }
        .day-chip input:checked + span {
            background: #eef7f5;
            border-color: #2f7c7a;
            color: #176f69;
            box-shadow: 0 0 0 4px rgba(47,124,122,.08);
        }
        .quick-select-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .quick-btn {
            border: 1px solid #dce7e4;
            background: #f9fbfb;
            color: #2c5b5a;
            border-radius: 999px;
            padding: 8px 11px;
            font-size: 11px;
            font-weight: 900;
            cursor: pointer;
        }
        .checkbox-line {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 13px 14px;
            border-radius: 16px;
            border: 1px solid #edf1f0;
            background: #fbfcfc;
            font-size: 12px;
            line-height: 1.6;
            color: #64748b;
            font-weight: 700;
        }
        .checkbox-line input {
            width: 18px;
            height: 18px;
            margin-top: 1px;
            flex: 0 0 auto;
        }
        .submit-btn,
        .filter-btn {
            height: 50px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            padding: 0 18px;
        }
        .submit-btn { width: 100%; height: 54px; }

        .preview-grid {
            display: grid;
            gap: 14px;
        }
        .availability-group-card {
            border: 1px solid #edf1f0;
            background: #fbfcfc;
            border-radius: 22px;
            padding: 18px;
        }
        .group-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }
        .group-title {
            font-size: 18px;
            font-weight: 900;
            color: #22343a;
            line-height: 1.4;
        }
        .group-meta {
            margin-top: 6px;
            font-size: 12px;
            color: #6b7280;
            line-height: 1.7;
        }
        .status-pill {
            display: inline-flex;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            background: #ecfdf5;
            color: #166534;
            white-space: nowrap;
        }
        .status-pill.inactive {
            background: #f3f4f6;
            color: #64748b;
        }
        .day-pill-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 14px;
        }
        .day-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 54px;
            padding: 9px 10px;
            border-radius: 14px;
            background: #eef7f5;
            color: #176f69;
            font-size: 12px;
            font-weight: 900;
            border: 1px solid #d7ebe6;
        }
        .group-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .delete-btn {
            border: none;
            background: #fee2e2;
            color: #b91c1c;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
        }
        .empty-state {
            padding: 28px;
            border-radius: 18px;
            border: 1px dashed #d9e2e1;
            background: #fafcfc;
            text-align: center;
            color: #7b8794;
            font-size: 13px;
            line-height: 1.8;
        }

        @media (max-width: 1080px) {
            .hero-grid, .content-grid, .form-grid { grid-template-columns: 1fr; }
            .day-chip-grid { grid-template-columns: repeat(4, 1fr); }
        }
        @media (max-width: 820px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-title { font-size: 32px; }
            .day-chip-grid { grid-template-columns: repeat(2, 1fr); }
            .month-filter-card { align-items: stretch; }
            .month-form { width: 100%; }
            .month-form input, .month-form button { width: 100%; }
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
    $selectedMonth = $selectedMonth ?? request('month', now()->format('Y-m'));
    $monthStart = $monthStart ?? \Carbon\Carbon::parse($selectedMonth . '-01')->startOfMonth();
    $monthEnd = $monthEnd ?? \Carbon\Carbon::parse($selectedMonth . '-01')->endOfMonth();
    $monthLabel = $monthLabel ?? $monthStart->format('F Y');
    $availabilityGroups = $availabilityGroups ?? collect();
@endphp


<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'therapist-availabilities'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/therapists" class="ghost-link">← Tim Fisioterapis</a>
                <a href="/admin/bookings" class="primary-link">Booking Scheduler</a>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <div class="hero-badge">Monthly Therapist Availability</div>
                        <h1 class="hero-title">Atur jadwal therapist per periode bulanan dengan preview yang lebih rapi.</h1>
                        <p class="hero-text">
                            Jadwal therapist dapat berubah setiap bulan. Gunakan periode berlaku agar public booking hanya membaca slot yang aktif pada bulan dan tanggal tersebut.
                        </p>
                    </div>

                    <div class="hero-side">
                        <h3>Flow jadwal bulanan</h3>
                        <p>Set satu kali untuk banyak hari dalam satu periode, lalu preview langsung terlihat per therapist.</p>
                        <div class="summary-stack">
                            <div class="summary-item">1. Pilih periode berlaku</div>
                            <div class="summary-item">2. Pilih therapist dan hari aktif</div>
                            <div class="summary-item">3. Set jam, durasi slot, capacity</div>
                            <div class="summary-item">4. Booking public membaca availability aktif</div>
                        </div>
                    </div>
                </div>
            </section>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Periksa kembali input:</strong>
                    <ul style="margin:8px 0 0 18px; padding:0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="month-filter-card">
                <div>
                    <h2 class="month-title">Preview Jadwal — {{ $monthLabel }}</h2>
                    <p class="month-subtitle">
                        Menampilkan availability yang berlaku pada bulan ini. Ganti bulan untuk cek jadwal periode lain.
                    </p>
                </div>

                <form method="GET" action="/admin/therapist-availabilities" class="month-form">
                    <div>
                        <label>Preview Month</label>
                        <input type="month" name="month" value="{{ $selectedMonth }}">
                    </div>
                    <button type="submit" class="filter-btn">Lihat Bulan</button>
                </form>
            </section>

            <div class="content-grid">
                <section class="section-card">
                    <h2 class="section-title">Tambah Availability Bulanan</h2>
                    <p class="section-subtitle">
                        Buat jadwal therapist untuk periode tertentu. Cocok untuk jadwal yang berubah per bulan.
                    </p>

                    <form method="POST" action="/admin/therapist-availabilities">
                        @csrf

                        <div class="form-grid">
                            <div class="field">
                                <label>Valid From</label>
                                <input type="date" name="valid_from" value="{{ old('valid_from', $monthStart->format('Y-m-d')) }}" required>
                            </div>

                            <div class="field">
                                <label>Valid Until</label>
                                <input type="date" name="valid_until" value="{{ old('valid_until', $monthEnd->format('Y-m-d')) }}" required>
                            </div>

                            <div class="field full">
                                <label>Therapist</label>
                                <select name="therapist_id" required>
                                    <option value="">Pilih therapist</option>
                                    @foreach($therapists as $therapist)
                                        <option value="{{ $therapist->id }}" {{ old('therapist_id') == $therapist->id ? 'selected' : '' }}>
                                            {{ $therapist->full_name }}{{ $therapist->specialty ? ' · ' . $therapist->specialty : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field full">
                                <label>Pilih Hari Availability</label>

                                @php
                                    $oldDays = collect(old('day_of_week', []))->map(fn($day) => (string) $day)->all();
                                @endphp

                                <div class="day-chip-grid">
                                    @foreach($dayLabels as $value => $label)
                                        <label class="day-chip">
                                            <input
                                                type="checkbox"
                                                name="day_of_week[]"
                                                value="{{ $value }}"
                                                {{ in_array((string) $value, $oldDays) ? 'checked' : '' }}
                                            >
                                            <span>{{ substr($label, 0, 3) }}<br>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="quick-select-row">
                                    <button type="button" class="quick-btn" onclick="selectDays([1,2,3,4,5])">Weekdays</button>
                                    <button type="button" class="quick-btn" onclick="selectDays([6,7])">Weekend</button>
                                    <button type="button" class="quick-btn" onclick="selectDays([1,2,3,4,5,6,7])">Everyday</button>
                                    <button type="button" class="quick-btn" onclick="selectDays([])">Clear</button>
                                </div>
                            </div>

                            <div class="field">
                                <label>Start Time</label>
                                <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}" required>
                            </div>

                            <div class="field">
                                <label>End Time</label>
                                <input type="time" name="end_time" value="{{ old('end_time', '17:00') }}" required>
                            </div>

                            <div class="field">
                                <label>Slot Duration</label>
                                <select name="slot_duration_minutes" required>
                                    <option value="30" {{ old('slot_duration_minutes') == 30 ? 'selected' : '' }}>30 menit</option>
                                    <option value="45" {{ old('slot_duration_minutes') == 45 ? 'selected' : '' }}>45 menit</option>
                                    <option value="60" {{ old('slot_duration_minutes', 60) == 60 ? 'selected' : '' }}>60 menit</option>
                                    <option value="90" {{ old('slot_duration_minutes') == 90 ? 'selected' : '' }}>90 menit</option>
                                    <option value="120" {{ old('slot_duration_minutes') == 120 ? 'selected' : '' }}>120 menit</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>Capacity</label>
                                <input type="number" name="capacity" min="1" max="20" value="{{ old('capacity', 1) }}" required>
                            </div>

                            <div class="field">
                                <label>Status</label>
                                <select name="status" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="field full">
                                <label>Notes</label>
                                <textarea name="notes" placeholder="Contoh: jadwal bulan Juni / shift pagi / khusus treatment tertentu">{{ old('notes') }}</textarea>
                            </div>

                            <div class="field full">
                                <label class="checkbox-line">
                                    <input type="checkbox" name="replace_existing" value="1" {{ old('replace_existing') ? 'checked' : '' }}>
                                    <span>
                                        Replace existing schedule untuk therapist, periode, dan hari yang dipilih.
                                        Aktifkan kalau ingin overwrite jadwal lama di periode yang sama.
                                    </span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="submit-btn">Simpan Availability Bulanan</button>
                    </form>
                </section>

                <section class="section-card">
                    <h2 class="section-title">Monthly Availability Preview</h2>
                    <p class="section-subtitle">
                        Jadwal dikelompokkan agar tidak terlihat seperti daftar panjang yang membingungkan.
                    </p>

                    <div class="preview-grid">
                        @forelse($availabilityGroups as $group)
                            @php
                                $first = $group->first();
                                $days = $group->pluck('day_of_week')->unique()->sort()->values();
                            @endphp

                            <div class="availability-group-card">
                                <div class="group-head">
                                    <div>
                                        <div class="group-title">
                                            {{ optional($first->therapist)->full_name ?: 'Therapist' }}
                                        </div>
                                        <div class="group-meta">
                                            Period:
                                            {{ $first->valid_from ? $first->valid_from->format('Y-m-d') : 'Open' }}
                                            -
                                            {{ $first->valid_until ? $first->valid_until->format('Y-m-d') : 'Open' }}
                                        </div>
                                        <div class="group-meta">
                                            Time: {{ substr($first->start_time, 0, 5) }} - {{ substr($first->end_time, 0, 5) }}
                                            · {{ $first->slot_duration_minutes }} menit / slot
                                            · Capacity {{ $first->capacity }}
                                        </div>

                                        @if($first->notes)
                                            <div class="group-meta">{{ $first->notes }}</div>
                                        @endif
                                    </div>

                                    <span class="status-pill {{ $first->status !== 'active' ? 'inactive' : '' }}">
                                        {{ $first->status_label }}
                                    </span>
                                </div>

                                <div class="day-pill-row">
                                    @foreach($days as $day)
                                        <span class="day-pill">{{ substr($dayLabels[$day] ?? '-', 0, 3) }}</span>
                                    @endforeach
                                </div>

                                <div class="group-actions" style="margin-top:14px;">
                                    @foreach($group as $availability)
                                        <form method="POST" action="/admin/therapist-availabilities/{{ $availability->id }}/delete" onsubmit="return confirm('Hapus availability {{ optional($availability->therapist)->full_name }} {{ $availability->day_name }}?')">
                                            @csrf
                                            <button type="submit" class="delete-btn">Delete {{ substr($availability->day_name, 0, 3) }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                Belum ada availability untuk bulan ini. Tambahkan jadwal di form kiri.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>

<script>
    function selectDays(days) {
        const selected = days.map(String);
        document.querySelectorAll('input[name="day_of_week[]"]').forEach(function (checkbox) {
            checkbox.checked = selected.includes(checkbox.value);
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
