<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f7f8f8;
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
            max-width: 1160px;
            min-height: 720px;
            display: grid;
            grid-template-columns: 420px 1fr;
            background: #ffffff;
            border: 1px solid #ecefef;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        }

        .form-panel {
            background: #ffffff;
            padding: 34px 34px 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid #f1f3f3;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1f2937;
            font-weight: 800;
            font-size: 18px;
        }

        .brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 12px;
        }

        .form-wrap {
            max-width: 336px;
            width: 100%;
            margin: 0 auto;
        }

        .form-wrap h1 {
            margin: 0 0 12px;
            font-size: 38px;
            line-height: 1.08;
            color: #22343a;
        }

        .subtitle {
            margin: 0 0 28px;
            font-size: 14px;
            line-height: 1.9;
            color: #6b7280;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.7;
            border: 1px solid transparent;
        }

        .alert-error {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        input {
            width: 100%;
            height: 52px;
            padding: 0 16px;
            border: 1px solid #dde5e3;
            border-radius: 14px;
            font-size: 14px;
            background: #ffffff;
            color: #111827;
            transition: .2s ease;
        }

        input:focus {
            outline: none;
            border-color: #176f69;
            box-shadow: 0 0 0 4px rgba(23, 111, 105, 0.08);
        }

        .submit-btn {
            width: 100%;
            height: 52px;
            border: none;
            border-radius: 14px;
            background: #176f69;
            color: white;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: .2s ease;
            margin-top: 8px;
        }

        .submit-btn:hover {
            background: #145d58;
        }

        .footer-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            margin-top: 24px;
            font-size: 12px;
            color: #94a3b8;
            flex-wrap: wrap;
        }

        .footer-row a {
            text-decoration: none;
            color: #176f69;
            font-weight: 700;
        }

        .image-panel {
            position: relative;
            background: #f4f6f5;
        }

        .image-panel img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                rgba(255,255,255,0.02) 0%,
                rgba(255,255,255,0.08) 100%
            );
            pointer-events: none;
        }

        @media (max-width: 980px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .image-panel {
                min-height: 280px;
                order: 1;
            }

            .form-panel {
                order: 2;
                border-right: none;
                border-top: 1px solid #f1f3f3;
            }
        }

        @media (max-width: 640px) {
            .page {
                padding: 16px;
            }

            .form-panel {
                padding: 22px;
            }

            .form-wrap h1 {
                font-size: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="shell">
            <section class="form-panel">
                <div class="brand">
                    <img src="/images/khayra-logo.png" alt="Khayra Logo">
                    <span>Khayra Physio</span>
                </div>

                <div class="form-wrap">
                    <h1>Admin Login</h1>
                    <p class="subtitle">
                        Masuk ke admin workspace untuk mengelola patient, booking, visit, therapist, dan billing klinik.
                    </p>

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

                    <form method="POST" action="/admin/login">
                        @csrf

                        <div class="field">
                            <label>Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your admin email">
                        </div>

                        <div class="field">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Enter your password">
                        </div>

                        <button type="submit" class="submit-btn">Sign In</button>
                    </form>
                </div>

                <div class="footer-row">
                    <span>Internal admin access only</span>
                    <a href="/">← Back to Home</a>
                </div>
            </section>

            <section class="image-panel">
                <img src="/images/admin-login-bg.png" alt="Khayra Clinic Interior">
                <div class="image-overlay"></div>
            </section>
        </div>
    </div>
</body>
</html>