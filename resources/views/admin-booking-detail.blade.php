<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Detail - Khayra Admin</title>
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

        .topbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .back-link,
        .edit-link,
        .patient-link {
            display: inline-block;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 14px;
            font-weight: 700;
            white-space: nowrap;
            font-size: 14px;
        }

        .back-link {
            background: white;
            color: #0f766e;
            border: 1px solid #d7ebe6;
        }

        .edit-link {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #cfe0ff;
        }

        .patient-link {
            background: #eefcf5;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
            font-size: 14px;
            line-height: 1.7;
        }

        .hero-card,
        .section-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .hero-card {
            margin-bottom: 20px;
        }

        .booking-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #effaf7;
            color: #0f766e;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .booking-title {
            margin: 0;
            font-size: 34px;
            color: #111827;
            line-height: 1.15;
        }

        .booking-subtitle {
            margin: 12px 0 0;
            color: #6b7280;
            font-size: 15px;
            line-height: 1.8;
            max-width: 760px;
        }

        .summary-grid,
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .summary-grid {
            margin-top: 20px;
        }

        .summary-box,
        .detail-box {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 18px;
            padding: 16px;
        }

        .detail-box.full {
            grid-column: 1 / -1;
        }

        .summary-label,
        .detail-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 6px;
        }

        .summary-value,
        .detail-value {
            font-size: 15px;
            color: #111827;
            line-height: 1.7;
            word-break: break-word;
        }

        .status-pill {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #dbeafe; color: #1d4ed8; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        .section-card + .section-card {
            margin-top: 20px;
        }

        .section-title {
            margin: 0;
            font-size: 24px;
            color: #0f766e;
        }

        .section-subtitle {
            margin: 8px 0 18px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        @media (max-width: 1180px) {
            .layout {
                display: block;
            }

            .main {
                padding: 20px;
            }

            .summary-grid,
            .detail-grid {
                grid-template-columns: 1fr;
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

            .hero-card,
            .section-card {
                padding: 20px;
                border-radius: 22px;
            }

            .booking-title {
                font-size: 28px;
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
                <h1 class="page-title">Booking Detail</h1>
                <p class="page-subtitle">Lihat ringkasan booking, koreksi data booking, dan hubungkan booking ke patient bila diperlukan.</p>
            </div>

            <div class="topbar-actions">
                <a href="/admin/bookings/{{ $booking->id }}/edit" class="edit-link">Edit Booking</a>

                @if(!$booking->patient_id)
                    <form method="POST" action="/admin/bookings/{{ $booking->id }}/create-patient" style="margin:0;">
                        @csrf
                        <button type="submit" class="patient-link" style="cursor:pointer;">Create Patient</button>
                    </form>
                @endif

                <a href="/admin/bookings" class="back-link">← Kembali ke Bookings</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <section class="hero-card">
            <div class="booking-badge">Booking Summary</div>
            <h2 class="booking-title">{{ $booking->full_name }}</h2>
            <p class="booking-subtitle">
                Data booking ini berasal dari form booking patient dan dapat dikoreksi admin bila ada perubahan jadwal, layanan, atau data kontak.
            </p>

            <div class="summary-grid">
                <div class="summary-box">
                    <div class="summary-label">Booking ID</div>
                    <div class="summary-value">#{{ $booking->id }}</div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Status</div>
                    <div class="summary-value">
                        <span class="status-pill status-{{ $booking->status }}">{{ $booking->status }}</span>
                    </div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Linked Patient</div>
                    <div class="summary-value">
                        @if($booking->patient)
                            {{ $booking->patient->full_name }} (Patient ID #{{ $booking->patient->id }})
                        @else
                            Belum terhubung
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="section-card">
            <h2 class="section-title">Booking Information</h2>
            <p class="section-subtitle">Detail informasi booking yang disimpan di sistem.</p>

            <div class="detail-grid">
                <div class="detail-box">
                    <div class="detail-label">Nama Lengkap</div>
                    <div class="detail-value">{{ $booking->full_name ?: '-' }}</div>
                </div>

                <div class="detail-box">
                    <div class="detail-label">WhatsApp</div>
                    <div class="detail-value">{{ $booking->whatsapp ?: '-' }}</div>
                </div>

                <div class="detail-box">
                    <div class="detail-label">Service</div>
                    <div class="detail-value">{{ $booking->service ?: '-' }}</div>
                </div>

                <div class="detail-box">
                    <div class="detail-label">Booking Date</div>
                    <div class="detail-value">{{ $booking->booking_date ?: '-' }}</div>
                </div>

                <div class="detail-box">
                    <div class="detail-label">Booking Time</div>
                    <div class="detail-value">{{ $booking->booking_time ?: '-' }}</div>
                </div>

                <div class="detail-box">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">{{ $booking->status ?: '-' }}</div>
                </div>

                <div class="detail-box full">
                    <div class="detail-label">Complaint</div>
                    <div class="detail-value">{{ $booking->complaint ?: '-' }}</div>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>