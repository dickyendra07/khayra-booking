<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking - Khayra Admin</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #1f2937;
            background:
                radial-gradient(circle at top left, rgba(15,118,110,.10), transparent 30%),
                linear-gradient(180deg, #f6fbfa 0%, #eef7f5 100%);
        }

        .layout {
            min-height: 100vh;
            display: flex;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 32px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 22px;
        }

        .page-title {
            margin: 0;
            font-size: 38px;
            color: #0f766e;
            line-height: 1.1;
        }

        .page-subtitle {
            margin: 10px 0 0;
            color: #6b7280;
            font-size: 15px;
            line-height: 1.7;
            max-width: 860px;
        }

        .back-link {
            display: inline-block;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 14px;
            background: white;
            color: #0f766e;
            border: 1px solid #d7ebe6;
            font-weight: 700;
            white-space: nowrap;
            font-size: 14px;
        }

        .form-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.7;
        }

        .field-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .field {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #374151;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        select,
        textarea {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #d7dedd;
            border-radius: 14px;
            font-size: 14px;
            background: #fcfefd;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #0f766e;
            box-shadow: 0 0 0 4px rgba(15,118,110,.08);
        }

        .submit-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 8px;
        }

        .submit-btn {
            border: none;
            border-radius: 14px;
            padding: 14px 22px;
            background: #0f766e;
            color: white;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 12px 26px rgba(15,118,110,.16);
        }

        @media (max-width: 1024px) {
            .layout {
                display: block;
            }

            .main {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 32px;
            }

            .field-grid {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 20px;
                border-radius: 22px;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'bookings'])

    <main class="main">
        <div class="topbar">
            <div>
                <h1 class="page-title">Edit Booking</h1>
                <p class="page-subtitle">Perbarui data booking untuk koreksi nama, kontak, layanan, jadwal, keluhan, atau status booking.</p>
            </div>

            <a href="/admin/bookings/{{ $booking->id }}" class="back-link">← Kembali ke Detail Booking</a>
        </div>

        <section class="form-card">
            @if($errors->any())
                <div class="alert-error">
                    <strong>Periksa kembali input Anda:</strong>
                    <ul style="margin:8px 0 0 18px; padding:0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/admin/bookings/{{ $booking->id }}/update">
                @csrf

                <div class="field-grid">
                    <div class="field">
                        <label>Nama Lengkap</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $booking->full_name) }}">
                    </div>

                    <div class="field">
                        <label>WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $booking->whatsapp) }}">
                    </div>

                    <div class="field">
                        <label>Service</label>
                        <input type="text" name="service" value="{{ old('service', $booking->service) }}">
                    </div>

                    <div class="field">
                        <label>Status</label>
                        <select name="status">
                            <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>pending</option>
                            <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>confirmed</option>
                            <option value="completed" {{ old('status', $booking->status) == 'completed' ? 'selected' : '' }}>completed</option>
                            <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>cancelled</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Booking Date</label>
                        <input type="date" name="booking_date" value="{{ old('booking_date', $booking->booking_date) }}">
                    </div>

                    <div class="field">
                        <label>Booking Time</label>
                        <input type="time" name="booking_time" value="{{ old('booking_time', $booking->booking_time) }}">
                    </div>

                    <div class="field full">
                        <label>Complaint</label>
                        <textarea name="complaint">{{ old('complaint', $booking->complaint) }}</textarea>
                    </div>
                </div>

                <div class="submit-row">
                    <button type="submit" class="submit-btn">Simpan Perubahan</button>
                </div>
            </form>
        </section>
    </main>
</div>
</body>
</html>