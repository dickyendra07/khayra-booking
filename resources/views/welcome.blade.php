<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khayra Physio</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #1f2937;
            background:
                radial-gradient(circle at top left, rgba(15, 118, 110, 0.14), transparent 28%),
                radial-gradient(circle at bottom right, rgba(20, 184, 166, 0.12), transparent 24%),
                linear-gradient(180deg, #f7fcfb 0%, #edf7f5 100%);
        }

        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            width: 100%;
            padding: 22px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .brand {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .brand-name {
            font-size: 28px;
            font-weight: 800;
            color: #0f766e;
            line-height: 1;
        }

        .brand-subtitle {
            font-size: 13px;
            color: #6b7280;
            letter-spacing: 0.4px;
        }

        .nav-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .nav-link {
            text-decoration: none;
            padding: 11px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            border: 1px solid #d7ebe6;
            background: rgba(255,255,255,0.75);
            color: #0f766e;
        }

        .hero {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 20px 24px 56px;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 28px;
            align-items: center;
            flex: 1;
        }

        .hero-left {
            background: rgba(255,255,255,0.74);
            border: 1px solid #e4f1ee;
            box-shadow: 0 18px 45px rgba(15, 118, 110, 0.08);
            border-radius: 32px;
            padding: 38px;
            backdrop-filter: blur(10px);
        }

        .badge {
            display: inline-block;
            padding: 9px 14px;
            border-radius: 999px;
            background: #ecfdf5;
            color: #047857;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .hero-left h1 {
            margin: 0;
            font-size: 52px;
            line-height: 1.08;
            color: #0f766e;
            max-width: 720px;
        }

        .hero-left p {
            margin: 18px 0 0;
            font-size: 17px;
            line-height: 1.8;
            color: #4b5563;
            max-width: 760px;
        }

        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 26px;
        }

        .btn-primary,
        .btn-secondary {
            text-decoration: none;
            padding: 14px 20px;
            border-radius: 16px;
            font-size: 15px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: #0f766e;
            color: white;
            box-shadow: 0 12px 26px rgba(15, 118, 110, 0.22);
        }

        .btn-secondary {
            background: white;
            color: #0f766e;
            border: 1px solid #d7ebe6;
        }

        .hero-points {
            margin-top: 28px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .point-card {
            background: #f8fffd;
            border: 1px solid #dff0eb;
            border-radius: 18px;
            padding: 18px;
        }

        .point-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f766e;
            margin-bottom: 8px;
        }

        .point-text {
            font-size: 14px;
            line-height: 1.7;
            color: #6b7280;
        }

        .hero-right {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .feature-card {
            background: linear-gradient(180deg, #0f766e 0%, #115e59 100%);
            color: white;
            border-radius: 28px;
            padding: 30px;
            box-shadow: 0 20px 45px rgba(15, 118, 110, 0.18);
        }

        .feature-card h2 {
            margin: 0 0 12px;
            font-size: 30px;
        }

        .feature-card p {
            margin: 0;
            line-height: 1.8;
            font-size: 15px;
            opacity: 0.95;
        }

        .feature-list {
            margin: 18px 0 0;
            padding-left: 20px;
            line-height: 2;
            font-size: 14px;
        }

        .mini-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .mini-card {
            background: white;
            border: 1px solid #e4f1ee;
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 16px 35px rgba(15, 118, 110, 0.08);
        }

        .mini-card-title {
            font-size: 14px;
            font-weight: 800;
            color: #0f766e;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .mini-card-text {
            font-size: 14px;
            line-height: 1.8;
            color: #6b7280;
        }

        .footer {
            text-align: center;
            padding: 0 24px 28px;
            color: #6b7280;
            font-size: 14px;
        }

        @media (max-width: 1100px) {
            .hero {
                grid-template-columns: 1fr;
            }

            .hero-left h1 {
                font-size: 42px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 20px;
                flex-direction: column;
                align-items: flex-start;
            }

            .hero {
                padding: 12px 18px 40px;
            }

            .hero-left {
                padding: 26px;
            }

            .hero-left h1 {
                font-size: 34px;
            }

            .hero-left p {
                font-size: 15px;
            }

            .hero-points,
            .mini-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <header class="navbar">
            <div class="brand">
                <div class="brand-name">Khayra Physio</div>
                <div class="brand-subtitle">Physiotherapy & Rehabilitation Clinic</div>
            </div>

            <div class="nav-actions">
                <a href="/booking" class="nav-link">Book Appointment</a>
                <a href="/admin" class="nav-link">Admin Portal</a>
            </div>
        </header>

        <section class="hero">
            <div class="hero-left">
                <span class="badge">Modern Clinic Workflow</span>

                <h1>Physiotherapy booking and clinic management in one system.</h1>

                <p>
                    Khayra Physio sekarang punya alur yang lebih rapi untuk booking pasien,
                    data patient, visit, medical record, billing, dan invoice print
                    dalam satu sistem yang saling terhubung.
                </p>

                <div class="hero-buttons">
                    <a href="/booking" class="btn-primary">Book Appointment</a>
                    <a href="/admin" class="btn-secondary">Open Admin Dashboard</a>
                </div>

                <div class="hero-points">
                    <div class="point-card">
                        <div class="point-title">Booking</div>
                        <div class="point-text">Pasien dapat mengirim permintaan booking dengan lebih cepat dan rapi.</div>
                    </div>

                    <div class="point-card">
                        <div class="point-title">Clinical Flow</div>
                        <div class="point-text">Visit, medical record, dan billing sekarang sudah saling terhubung.</div>
                    </div>

                    <div class="point-card">
                        <div class="point-title">Admin Control</div>
                        <div class="point-text">Admin dapat memantau dashboard, patient, visit, dan invoice dari satu panel.</div>
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <div class="feature-card">
                    <h2>Built for Khayra Physio</h2>
                    <p>
                        Sistem ini dirancang untuk mendukung kebutuhan operasional klinik
                        mulai dari proses pasien masuk sampai sesi terapi dan invoice selesai.
                    </p>

                    <ul class="feature-list">
                        <li>Booking management</li>
                        <li>Patient database</li>
                        <li>Visit & medical record</li>
                        <li>Billing & printable invoice</li>
                        <li>Live admin dashboard</li>
                    </ul>
                </div>

                <div class="mini-grid">
                    <div class="mini-card">
                        <div class="mini-card-title">Book</div>
                        <div class="mini-card-text">
                            Halaman booking siap dipakai untuk menerima appointment pasien.
                        </div>
                    </div>

                    <div class="mini-card">
                        <div class="mini-card-title">Admin</div>
                        <div class="mini-card-text">
                            Panel admin sudah punya dashboard, patient detail, billing, dan invoice print.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="footer">
            © Khayra Physio — internal clinic workflow system
        </div>
    </div>
</body>
</html>