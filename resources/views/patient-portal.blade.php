<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #edf3f5;
            color: #0f172a;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
        }

        .shell {
            width: 100%;
            max-width: 1080px;
            min-height: 660px;
            display: grid;
            grid-template-columns: 430px 1fr;
            background: #ffffff;
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid #e7eceb;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
        }

        .left-panel {
            padding: 26px 28px 22px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #edf1f0;
            background: #ffffff;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 34px;
        }

        .brand img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 10px;
        }

        .form-wrap {
            width: 100%;
            max-width: 320px;
            margin: auto;
        }

        .welcome {
            text-align: center;
            margin-bottom: 28px;
        }

        .welcome h1 {
            margin: 0 0 12px;
            font-size: 24px;
            line-height: 1.2;
            color: #294047;
        }

        .welcome p {
            margin: 0;
            font-size: 13px;
            line-height: 1.8;
            color: #6b7280;
        }

        .tab-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 3px;
            margin-bottom: 20px;
        }

        .tab {
            text-align: center;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #6b7280;
        }

        .tab.active {
            background: #f2f5f5;
            color: #334155;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 14px;
            font-size: 13px;
            line-height: 1.7;
            border: 1px solid transparent;
        }

        .alert-error {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .field {
            margin-bottom: 14px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
        }

        .input-wrap {
            position: relative;
        }

        input {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border: 1px solid #dfe6e4;
            border-radius: 10px;
            font-size: 13px;
            color: #111827;
            background: #ffffff;
            transition: .2s ease;
        }

        input:focus {
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

        .submit-btn {
            width: 100%;
            height: 46px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #3d8a89 0%, #2f7c7a 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s ease;
            margin-top: 6px;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #2f7c7a 0%, #246866 100%);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 18px 0 14px;
            color: #9ca3af;
            font-size: 12px;
            justify-content: center;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .quick-info {
            display: grid;
            gap: 10px;
            margin-top: 4px;
        }

        .quick-box {
            padding: 12px 14px;
            border: 1px solid #edf1f0;
            border-radius: 12px;
            background: #fafcfc;
        }

        .quick-title {
            font-size: 12px;
            font-weight: 700;
            color: #35565d;
            margin-bottom: 4px;
        }

        .quick-text {
            font-size: 12px;
            line-height: 1.7;
            color: #6b7280;
        }

        .footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 22px;
            font-size: 11px;
            color: #94a3b8;
            flex-wrap: wrap;
        }

        .footer-row a {
            text-decoration: none;
            color: #2f7c7a;
            font-weight: 700;
        }

        .right-panel {
            position: relative;
            background:
                linear-gradient(180deg, rgba(10, 60, 70, 0.06), rgba(10, 60, 70, 0.16)),
                linear-gradient(135deg, #245f67 0%, #123f46 100%);
            overflow: hidden;
        }

        .grid-overlay {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
            background-size: 56px 56px;
            pointer-events: none;
        }

        .right-inner {
            position: relative;
            z-index: 1;
            height: 100%;
            padding: 34px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .visual-top {
            display: flex;
            justify-content: flex-start;
        }

        .pill {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.16);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
        }

        .visual-center {
            position: relative;
            flex: 1;
            min-height: 320px;
            margin: 24px 0;
        }

        .floating-card {
            position: absolute;
            background: rgba(255,255,255,.96);
            border-radius: 18px;
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.14);
            border: 1px solid rgba(255,255,255,.75);
            padding: 16px;
            color: #1f2937;
        }

        .floating-card.one {
            width: 180px;
            top: 6%;
            left: 26%;
        }

        .floating-card.two {
            width: 220px;
            top: 32%;
            right: 12%;
        }

        .floating-card.three {
            width: 200px;
            bottom: 10%;
            left: 20%;
        }

        .card-label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 8px;
        }

        .card-title {
            font-size: 14px;
            font-weight: 800;
            color: #21343a;
            margin-bottom: 10px;
        }

        .line {
            height: 8px;
            border-radius: 999px;
            background: #e7eeed;
            margin-bottom: 8px;
        }

        .line.short { width: 56%; }
        .line.mid { width: 74%; }
        .line.long { width: 100%; }

        .visual-bottom h2 {
            margin: 0 0 12px;
            font-size: 22px;
            line-height: 1.35;
            color: #ffffff;
            max-width: 420px;
        }

        .visual-bottom p {
            margin: 0;
            font-size: 13px;
            line-height: 1.85;
            color: rgba(255,255,255,.82);
            max-width: 430px;
        }

        @media (max-width: 980px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .right-panel {
                min-height: 360px;
            }

            .left-panel {
                border-right: none;
                border-bottom: 1px solid #edf1f0;
            }
        }

        @media (max-width: 640px) {
            .page {
                padding: 16px;
            }

            .left-panel,
            .right-inner {
                padding: 20px;
            }

            .shell {
                border-radius: 22px;
            }

            .form-wrap {
                max-width: 100%;
            }

            .floating-card.one,
            .floating-card.two,
            .floating-card.three {
                position: relative;
                width: 100%;
                top: auto;
                left: auto;
                right: auto;
                bottom: auto;
                margin-bottom: 12px;
            }

            .visual-center {
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="shell">
            <section class="left-panel">
                <div class="brand">
                    <img src="/images/khayra-logo.png" alt="Khayra Logo">
                    <span>Khayra Physio</span>
                </div>

                <div class="form-wrap">
                    <div class="welcome">
                        <h1>Welcome to Patient Portal</h1>
                        <p>
                            Akses informasi patient Anda dengan menggunakan nomor WhatsApp dan tanggal lahir
                            yang terdaftar di sistem Khayra Physio.
                        </p>
                    </div>

                    <div class="tab-row">
                        <div class="tab active">Patient Access</div>
                        <div class="tab">Secure Portal</div>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-error">{{ session('error') }}</div>
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

                    <form method="POST" action="/patient/search">
                        @csrf

                        <div class="field">
                            <label>Nomor WhatsApp</label>
                            <div class="input-wrap">
                                <input
                                    type="text"
                                    name="whatsapp"
                                    value="{{ old('whatsapp') }}"
                                    placeholder="Masukkan nomor WhatsApp"
                                >
                            </div>
                            <div class="helper">Gunakan nomor yang sama dengan data patient yang terdaftar.</div>
                        </div>

                        <div class="field">
                            <label>Tanggal Lahir</label>
                            <div class="input-wrap">
                                <input
                                    type="date"
                                    name="birth_date"
                                    value="{{ old('birth_date') }}"
                                >
                            </div>
                            <div class="helper">Tanggal lahir digunakan sebagai verifikasi akses patient portal.</div>
                        </div>

                        <button type="submit" class="submit-btn">Lihat Data Patient</button>
                    </form>

                    <div class="divider">Portal Information</div>

                    <div class="quick-info">
                        <div class="quick-box">
                            <div class="quick-title">Data yang dapat diakses</div>
                            <div class="quick-text">Profil patient, riwayat visit, billing, dan ringkasan terapi terbaru.</div>
                        </div>
                        <div class="quick-box">
                            <div class="quick-title">Butuh bantuan?</div>
                            <div class="quick-text">Jika data belum sesuai, patient dapat menghubungi admin klinik setelah masuk ke portal.</div>
                        </div>
                    </div>
                </div>

                <div class="footer-row">
                    <span>Patient portal access • Khayra Physio</span>
                    <a href="/">← Back to Home</a>
                </div>
            </section>

            <section class="right-panel">
                <div class="grid-overlay"></div>

                <div class="right-inner">
                    <div class="visual-top">
                        <div class="pill">Patient Portal</div>
                    </div>

                    <div class="visual-center">
                        <div class="floating-card one">
                            <div class="card-label">Patient Profile</div>
                            <div class="card-title">Basic Information</div>
                            <div class="line long"></div>
                            <div class="line mid"></div>
                            <div class="line short"></div>
                        </div>

                        <div class="floating-card two">
                            <div class="card-label">Visit History</div>
                            <div class="card-title">Therapy Timeline</div>
                            <div class="line long"></div>
                            <div class="line long"></div>
                            <div class="line mid"></div>
                        </div>

                        <div class="floating-card three">
                            <div class="card-label">Billing</div>
                            <div class="card-title">Invoice Summary</div>
                            <div class="line long"></div>
                            <div class="line short"></div>
                            <div class="line mid"></div>
                        </div>
                    </div>

                    <div class="visual-bottom">
                        <h2>A simple and secure way to review your therapy journey.</h2>
                        <p>
                            Patient portal Khayra Physio membantu Anda melihat informasi patient,
                            visit, billing, dan ringkasan terapi dengan akses yang lebih aman dan mudah dipahami.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>