<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Portal - Khayra Physio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(47,124,122,.08), transparent 28%),
                linear-gradient(180deg, #eef5f7 0%, #f7faf9 100%);
            color: #17232b;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 34px;
        }

        .portal-shell {
            width: min(100%, 1220px);
            min-height: 760px;
            display: grid;
            grid-template-columns: 0.95fr 1.45fr;
            background: #ffffff;
            border: 1px solid #dfe8e8;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(15, 23, 42, .10);
        }

        .left-panel {
            padding: 42px 40px 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #ffffff;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 46px;
        }

        .brand img {
            width: 46px;
            height: 46px;
            object-fit: contain;
            border-radius: 14px;
            background: #eef7f5;
            padding: 4px;
            border: 1px solid #d8ebe7;
        }

        .brand-name {
            font-size: 21px;
            font-weight: 900;
            color: #22343a;
        }

        .headline {
            text-align: center;
            margin-bottom: 28px;
        }

        .headline h1 {
            margin: 0 0 16px;
            font-size: 30px;
            line-height: 1.16;
            color: #22343a;
            font-weight: 900;
            letter-spacing: -.4px;
        }

        .headline p {
            margin: 0 auto;
            max-width: 360px;
            color: #60717a;
            font-size: 14px;
            line-height: 1.85;
        }

        .tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border: 1px solid #dfe6e6;
            border-radius: 14px;
            padding: 4px;
            margin-bottom: 22px;
            background: #ffffff;
        }

        .tab {
            border-radius: 11px;
            min-height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 900;
            color: #6b7280;
        }

        .tab.active {
            background: #f3f7f6;
            color: #22343a;
            box-shadow: inset 0 0 0 1px rgba(15,23,42,.02);
        }

        .alert {
            padding: 13px 14px;
            border-radius: 14px;
            margin-bottom: 16px;
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #ffe0e6;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.6;
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 12px;
            font-weight: 900;
        }

        input {
            width: 100%;
            min-height: 52px;
            border: 1px solid #d7dedd;
            border-radius: 14px;
            background: #ffffff;
            padding: 0 16px;
            font-size: 14px;
            color: #17232b;
            outline: none;
            font-family: Arial, sans-serif;
        }

        input:focus {
            border-color: #2f7c7a;
            box-shadow: 0 0 0 4px rgba(47,124,122,.08);
        }

        .help-text {
            margin-top: 8px;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.6;
        }

        .submit-btn {
            width: 100%;
            min-height: 54px;
            border: 0;
            cursor: pointer;
            border-radius: 14px;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: 900;
            box-shadow: 0 14px 26px rgba(47,124,122,.18);
            margin: 8px 0 18px;
            font-family: Arial, sans-serif;
        }

        .divider {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 12px;
            color: #a1acb6;
            font-size: 12px;
            margin-bottom: 18px;
        }

        .divider::before,
        .divider::after {
            content: "";
            height: 1px;
            background: #eef1f1;
        }

        .info-stack {
            display: grid;
            gap: 12px;
        }

        .info-box {
            border: 1px solid #e7eeee;
            border-radius: 15px;
            padding: 15px 16px;
            background: #ffffff;
        }

        .info-title {
            font-size: 12px;
            font-weight: 900;
            color: #22343a;
            margin-bottom: 7px;
        }

        .info-desc {
            font-size: 12px;
            line-height: 1.65;
            color: #64748b;
        }

        .footer-row {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: center;
            margin-top: 22px;
            color: #94a3b8;
            font-size: 12px;
        }

        .footer-row a {
            color: #2f7c7a;
            text-decoration: none;
            font-weight: 900;
        }

        .right-panel {
            background: linear-gradient(145deg, #1f6b6f 0%, #155862 42%, #113f49 100%);
            position: relative;
            overflow: hidden;
            padding: 38px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .right-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px),
                radial-gradient(circle at top right, rgba(255,255,255,.12), transparent 32%);
            background-size: 64px 64px, 64px 64px, auto;
            pointer-events: none;
        }

        .right-panel > * {
            position: relative;
            z-index: 1;
        }

        .portal-badge {
            width: fit-content;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.24);
            background: rgba(255,255,255,.14);
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            margin-left: 6px;
        }

        .floating-area {
            position: relative;
            flex: 1;
            min-height: 450px;
        }

        .floating-card {
            position: absolute;
            width: 210px;
            min-height: 120px;
            background: rgba(255,255,255,.94);
            color: #22343a;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 20px 45px rgba(0,0,0,.16);
            border: 1px solid rgba(255,255,255,.55);
        }

        .floating-card.card-1 {
            top: 40px;
            left: 190px;
        }

        .floating-card.card-2 {
            top: 205px;
            right: 78px;
            width: 250px;
        }

        .floating-card.card-3 {
            bottom: 54px;
            left: 150px;
            width: 230px;
        }

        .card-kicker {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .card-title {
            font-size: 15px;
            font-weight: 900;
            color: #22343a;
            margin-bottom: 12px;
        }

        .skeleton {
            height: 8px;
            background: #e7eeee;
            border-radius: 999px;
            margin-bottom: 9px;
        }

        .skeleton.short { width: 70%; }
        .skeleton.mini { width: 48%; }

        .right-copy {
            max-width: 560px;
            padding-bottom: 10px;
        }

        .right-copy h2 {
            margin: 0 0 18px;
            color: #ffffff;
            font-size: 31px;
            line-height: 1.18;
            font-weight: 900;
            letter-spacing: -.4px;
        }

        .right-copy p {
            margin: 0;
            color: rgba(255,255,255,.86);
            font-size: 14px;
            line-height: 1.85;
            max-width: 600px;
        }

        @media (max-width: 980px) {
            body {
                padding: 18px;
                align-items: flex-start;
            }

            .portal-shell {
                grid-template-columns: 1fr;
            }

            .right-panel {
                min-height: 560px;
            }

            .floating-card.card-1 { left: 40px; top: 60px; }
            .floating-card.card-2 { right: 40px; top: 210px; }
            .floating-card.card-3 { left: 60px; bottom: 130px; }
        }

        @media (max-width: 620px) {
            .left-panel,
            .right-panel {
                padding: 24px;
            }

            .headline h1 {
                font-size: 25px;
            }

            .right-panel {
                min-height: 620px;
            }

            .floating-card {
                position: relative;
                width: 100% !important;
                left: auto !important;
                right: auto !important;
                top: auto !important;
                bottom: auto !important;
                margin-bottom: 16px;
            }

            .floating-area {
                display: grid;
                align-content: start;
                gap: 0;
                min-height: auto;
                margin: 24px 0;
            }

            .footer-row {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <main class="portal-shell">
        <section class="left-panel">
            <div>
                <div class="brand">
                    <img src="/images/khayra-logo.png" alt="Khayra Physio">
                    <div class="brand-name">Khayra Physio</div>
                </div>

                <div class="headline">
                    <h1>Welcome to Patient Portal</h1>
                    <p>
                        Akses informasi patient Anda dengan menggunakan nomor WhatsApp dan tanggal lahir yang terdaftar di sistem Khayra Physio.
                    </p>
                </div>

                <div class="tabs">
                    <div class="tab active">Patient Access</div>
                    <div class="tab">Secure Portal</div>
                </div>

                @if($errors->any())
                    <div class="alert">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/patient/login">
                    @csrf

                    <div class="field">
                        <label>Nomor WhatsApp</label>
                        <input
                            type="text"
                            name="whatsapp"
                            value="{{ old('whatsapp') }}"
                            placeholder="Masukkan nomor WhatsApp"
                            required
                            autofocus
                        >
                        <div class="help-text">Gunakan nomor yang sama dengan data patient yang terdaftar.</div>
                    </div>

                    <div class="field">
                        <label>Tanggal Lahir</label>
                        <input
                            type="date"
                            name="birth_date"
                            value="{{ old('birth_date') }}"
                            required
                        >
                        <div class="help-text">Tanggal lahir digunakan sebagai verifikasi akses patient portal.</div>
                    </div>

                    <button type="submit" class="submit-btn">Lihat Data Patient</button>
                </form>

                <div class="divider">Portal Information</div>

                <div class="info-stack">
                    <div class="info-box">
                        <div class="info-title">Data yang dapat diakses</div>
                        <div class="info-desc">
                            Profil patient, riwayat visit, billing, invoice, home exercise, dan ringkasan terapi terbaru.
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-title">Butuh bantuan?</div>
                        <div class="info-desc">
                            Jika data belum sesuai, patient dapat menghubungi admin klinik setelah masuk ke portal.
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-row">
                <span>Patient portal access • Khayra Physio</span>
                <a href="/">← Back to Home</a>
            </div>
        </section>

        <section class="right-panel">
            <div class="portal-badge">Patient Portal</div>

            <div class="floating-area">
                <div class="floating-card card-1">
                    <div class="card-kicker">Patient Profile</div>
                    <div class="card-title">Basic Information</div>
                    <div class="skeleton"></div>
                    <div class="skeleton short"></div>
                    <div class="skeleton mini"></div>
                </div>

                <div class="floating-card card-2">
                    <div class="card-kicker">Visit History</div>
                    <div class="card-title">Therapy Timeline</div>
                    <div class="skeleton"></div>
                    <div class="skeleton"></div>
                    <div class="skeleton short"></div>
                </div>

                <div class="floating-card card-3">
                    <div class="card-kicker">Billing</div>
                    <div class="card-title">Invoice Summary</div>
                    <div class="skeleton"></div>
                    <div class="skeleton mini"></div>
                    <div class="skeleton short"></div>
                </div>
            </div>

            <div class="right-copy">
                <h2>A simple and secure way to review your therapy journey.</h2>
                <p>
                    Patient portal Khayra Physio membantu Anda melihat informasi patient, visit, billing,
                    dan ringkasan terapi dengan akses yang lebih aman dan mudah dipahami.
                </p>
            </div>
        </section>
    </main>
</body>
</html>
