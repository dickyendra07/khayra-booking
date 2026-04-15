<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Physiotherapy Visit - Khayra Physio</title>
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
        .container { max-width: 1100px; margin: 0 auto; }
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
        }
        @media (max-width: 860px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'visits'])

    <main class="main">
        <div class="container">
            <div class="top-actions">
                <a href="/admin/visits" class="ghost-link">← Kembali ke Visit List</a>
            </div>

            <section class="section-card">
                <h1 class="title">Edit Physiotherapy Visit</h1>
                <p class="subtitle">
                    Perbarui jadwal, patient, physiotherapy staff, dan status sesi agar data visit tetap akurat dan rapi.
                </p>

                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/admin/visits/{{ $visit->id }}/update">
                    @csrf

                    <div class="form-grid">
                        <div class="field">
                            <label>Patient</label>
                            <select name="patient_id" required>
                                <option value="">Pilih Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id', $visit->patient_id) == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->full_name }}{{ $patient->medical_record_number ? ' - ' . $patient->medical_record_number : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Related Booking</label>
                            <select name="booking_id">
                                <option value="">Pilih Booking (opsional)</option>
                                @foreach($bookings as $booking)
                                    <option value="{{ $booking->id }}" {{ old('booking_id', $visit->booking_id) == $booking->id ? 'selected' : '' }}>
                                        #{{ $booking->id }} - {{ $booking->full_name }} - {{ $booking->booking_date }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Physiotherapy Staff</label>
                            <select name="therapist_id" required>
                                <option value="">Pilih Staff</option>
                                @foreach($therapists as $therapist)
                                    <option value="{{ $therapist->id }}" {{ old('therapist_id', $visit->therapist_id) == $therapist->id ? 'selected' : '' }}>
                                        {{ $therapist->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Visit Date</label>
                            <input type="date" name="visit_date" value="{{ old('visit_date', $visit->visit_date) }}" required>
                        </div>

                        <div class="field">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="scheduled" {{ old('status', $visit->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ old('status', $visit->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $visit->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $visit->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="field full">
                            <label>Clinical Notes</label>
                            <textarea name="notes" rows="4">{{ old('notes', $visit->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="submit-btn">Simpan Perubahan Visit</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>