<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Visit - Khayra</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(15,118,110,.10), transparent 30%),
                linear-gradient(180deg, #f6fbfa 0%, #eef7f5 100%);
            color: #1f2937;
        }

        .layout { min-height: 100vh; display: flex; }

        .sidebar {
            width: 250px;
            flex-shrink: 0;
            background: linear-gradient(180deg, #0f766e 0%, #115e59 100%);
            color: white;
            padding: 28px 22px;
            box-shadow: 8px 0 30px rgba(15,118,110,.12);
        }

        .brand {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 14px;
            opacity: .88;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .nav-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: .72;
            margin-bottom: 12px;
        }

        .nav-item {
            display: block;
            text-decoration: none;
            color: white;
            padding: 13px 14px;
            border-radius: 14px;
            margin-bottom: 10px;
            background: rgba(255,255,255,.07);
            font-weight: 600;
        }

        .nav-item.active {
            background: rgba(255,255,255,.18);
        }

        .logout-form { margin-top: 18px; }

        .logout-btn {
            width: 100%;
            border: none;
            padding: 13px 14px;
            border-radius: 14px;
            background: rgba(255,255,255,.14);
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 32px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .title h1 {
            margin: 0;
            font-size: 34px;
            color: #0f766e;
        }

        .title p {
            margin: 8px 0 0;
            color: #6b7280;
        }

        .ghost-link {
            display: inline-block;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid #d7ebe6;
            color: #0f766e;
            background: #f8fffd;
            font-size: 14px;
            font-weight: 600;
        }

        .form-card {
            background: white;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .field {
            display: flex;
            flex-direction: column;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            margin-bottom: 8px;
            font-weight: 700;
            color: #374151;
            font-size: 14px;
        }

        input, select, textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #d7dedd;
            border-radius: 14px;
            font-size: 14px;
            background: #fcfefd;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #0f766e;
            box-shadow: 0 0 0 4px rgba(15,118,110,.08);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .submit-row {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
        }

        .submit-btn {
            border: none;
            padding: 14px 20px;
            border-radius: 14px;
            background: #0f766e;
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 12px 26px rgba(15,118,110,.16);
        }

        @media (max-width: 1024px) {
            .layout { display: block; }
            .sidebar {
                width: 100%;
                border-radius: 0 0 24px 24px;
            }
            .main { padding: 20px; }
        }

        @media (max-width: 768px) {
            .grid { grid-template-columns: 1fr; }
            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'visits'])

    <main class="main">
        <div class="topbar">
            <div class="title">
                <h1>Edit Visit</h1>
                <p>Perbarui data kunjungan pasien.</p>
            </div>
            <a href="/admin/visits" class="ghost-link">← Kembali ke Visits</a>
        </div>

        <div class="form-card">
            <form method="POST" action="/admin/visits/{{ $visit->id }}/update">
                @csrf

                <div class="grid">
                    <div class="field">
                        <label>Patient</label>
                        <select name="patient_id" required>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ $visit->patient_id == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>Booking (optional)</label>
                        <select name="booking_id">
                            <option value="">Pilih booking</option>
                            @foreach($bookings as $booking)
                                <option value="{{ $booking->id }}" {{ $visit->booking_id == $booking->id ? 'selected' : '' }}>
                                    #{{ $booking->id }} - {{ $booking->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>Visit Date</label>
                        <input type="date" name="visit_date" value="{{ $visit->visit_date }}" required>
                    </div>

                    <div class="field">
                        <label>Therapist</label>
                        <select name="therapist_id" required>
                            @foreach($therapists as $therapist)
                                <option value="{{ $therapist->id }}" {{ $visit->therapist_id == $therapist->id ? 'selected' : '' }}>
                                    {{ $therapist->full_name }} {{ $therapist->status === 'inactive' ? '(Inactive)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="scheduled" {{ $visit->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="in_progress" {{ $visit->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $visit->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $visit->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="field full">
                        <label>Notes</label>
                        <textarea name="notes">{{ $visit->notes }}</textarea>
                    </div>
                </div>

                <div class="submit-row">
                    <button type="submit" class="submit-btn">Update Visit</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>